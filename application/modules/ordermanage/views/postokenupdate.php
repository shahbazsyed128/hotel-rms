<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Invoice</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            width: 80mm;
            margin: 0;
            margin-top: -8px;
            padding: 0;
        }

        .token {
            width: 100%;
            border-bottom: 1px dashed #000;
            padding: 5px 0;
            page-break-after: always;
        }

        .token-header {
            text-align: center;
            margin-bottom: 5px;
        }

        .token-header h1 {
            margin: 0;
            font-size: 16px;
        }

        .token-header p {
            margin: 0;
            font-size: 12px;
        }

        .token-details {
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
        }

        .token-details p {
            margin: 0;
            font-size: 12px;
        }

        .token-items {
            width: 100%;
            border-collapse: collapse;
        }

        .token-items th,
        .token-items td {
            text-align: left;
            padding: 2px;
            font-size: 12px;
        }

        .token-items th {
            border-bottom: 1px solid #000;
        }
		th.size{
			text-align: center;
		}

        .token-items td.size {
            text-align: center;
        }

        .token-footer {
            text-align: center;
            margin-top: 5px;
        }

        .token-footer p {
            margin: 0;
            font-size: 12px;
        }
    </style>
    <script type="text/javascript">
        var pstatus = "<?php echo $this->uri->segment(5); ?>";
        var returnurl = pstatus == 0 ? "<?php echo base_url('ordermanage/order/pos_invoice'); ?>" : "<?php echo base_url('ordermanage/order/pos_invoice'); ?>?tokenorder=<?php echo $orderinfo->order_id; ?>";
        window.print();
        setInterval(function() {
            document.location.href = returnurl;
        }, 3000);
    </script>
</head>

<body>

    <?php
    $this->load->model('order_model');
    $tokenNumber = $this->order_model->getTokenNumber();

    function formatNumber($number) {
        return intval($number);
    }

    function generateTableRow($quantity, $productName, $notes, $variantName) {
        $quantity = formatNumber($quantity);
        return "<tr>
                    <td>{$quantity}</td>
                    <td>{$productName}<br>{$notes}</td>
                    <td class='size'>{$variantName}</td>
                </tr>";
    }

    $itemsByKitchen = [];
    foreach ($iteminfo as $iteminf) {
        $itemsByKitchen[$iteminf->kitchenid]['iteminfo'][] = $iteminf;
    }
    foreach ($exitsitem as $exititem) {
        $itemsByKitchen[$exititem->kitchenid]['exitsitem'][] = $exititem;
    }

    foreach ($itemsByKitchen as $allItems) {
        $itemcontent = "";
        foreach ($allItems['iteminfo'] as $item) {
            $itemcontent .= generateTableRow($item->menuqty, $item->ProductName, $item->notes, $item->variantName);
            if (!empty($item->add_on_id)) {
                $addons = explode(",", $item->add_on_id);
                $addonsqty = explode(",", $item->addonsqty);
                foreach ($addons as $index => $addonsid) {
                    $adonsinfo = $this->order_model->read('*', 'add_ons', array('add_on_id' => $addonsid));
                    $itemcontent .= generateTableRow($addonsqty[$index], $adonsinfo->add_on_name, '', '');
                }
            }
        }

        $content = "";
        foreach ($allItems['exitsitem'] as $exititem) {
            $isexitsitem = $this->order_model->readupdate('tbl_updateitems.*,SUM(tbl_updateitems.qty) as totalqty', 'tbl_updateitems', array('ordid' => $orderinfo->order_id, 'menuid' => $exititem->menu_id, 'varientid' => $exititem->varientid, 'addonsuid' => $exititem->addonsuid));
            if (!empty($isexitsitem) && $isexitsitem->qty > 0) {
                $content .= generateTableRow($isexitsitem->totalqty, $exititem->ProductName, $exititem->notes, $exititem->variantName);
            }
        }

        if (!empty($content) || !empty($itemcontent)) {
            echo "<div class='token'>
                    <div class='token-header'>
                        <h1>Token No: {$tokenNumber}</h1>
                        <p>" . display('date') . ": " . date("M d, Y", strtotime($orderinfo->order_date)) . " - " . date("h:i:s A") . "</p>
                        <p>{$customerinfo->customer_name}</p>
                    </div>
                    <div class='token-details'>
                        <p>" . display('table') . ": " . (!empty($tableinfo) ? $tableinfo->tablename : 'N/A') . "</p>
                        <p>" . display('ord_number') . ": {$orderinfo->order_id}</p>
                    </div>
                    <div class='token-details'>
                        <p>" . display('waiter') . ": {$waiterinfo->first_name}</p>
                    </div>
                    <table class='token-items'>
                        <thead>
                            <tr>
                                <th>Q</th>
                                <th>" . display('item') . "</th>
                                <th class='size' align='center'>" . display('size') . "</th>
                            </tr>
                        </thead>
                        <tbody>
                            {$itemcontent}
                            {$content}
                        </tbody>
                    </table>
                </div>";
            $tokenNumber++;
        }
    }
	$this->order_model->getTokenNumber(--$tokenNumber);
    ?>
</body>

</html>