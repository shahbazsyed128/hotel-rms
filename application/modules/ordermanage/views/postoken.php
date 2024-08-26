<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Token</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            width: 80mm;
            margin: 0;
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
        if (pstatus == 0) {
            var returnurl = "<?php echo base_url('ordermanage/order/pos_invoice'); ?>";
        } else {
            var returnurl = "<?php echo base_url('ordermanage/order/pos_invoice'); ?>?tokenorder=<?php echo $orderinfo->order_id; ?>";
        }
        window.print();
        setInterval(function() {
            document.location.href = returnurl;
        }, 3000);
    </script>
</head>

<body>
    <?php
    $itemsByKitchen = [];
    $this->load->model('order_model');
    $tokenNumber = $this->order_model->getTokenNumber();

    // Check if $iteminfo is not empty, use $iteminfo, otherwise use $exitsitem
    if (!empty($iteminfo)) {
        foreach ($iteminfo as $item) {
            $itemsByKitchen[$item->kitchenid][] = $item;
        }
        $loopVariableName = 'iteminfo';
    } else {
        foreach ($exitsitem as $exititem) {
            $itemsByKitchen[$exititem->kitchenid][] = $exititem;
        }
        $loopVariableName = 'exitsitem';
    }

    foreach ($itemsByKitchen as $$loopVariableName) {
        if (!empty($$loopVariableName)) { ?>
            <div class="token">
                <div class="token-header">
                    <h1>Token No: <?php echo $tokenNumber++; ?></h1>
                    <p><?php echo display('date'); ?>: <?php echo date("M d, Y", strtotime($orderinfo->order_date)) . " - " . date("h:i:s A"); ?></p>
                    <p><?php echo $customerinfo->customer_name; ?></p>
                </div>
                <div class="token-details">
                    <p><?php echo display('table'); ?>: <?php echo !empty($tableinfo) ? $tableinfo->tablename : 'N/A'; ?></p>
                    <p><?php echo display('ord_number'); ?>: <?php echo $orderinfo->order_id; ?></p>
                </div>
                <div class="token-details">
                    <p><?php echo display('waiter'); ?>: <?php echo $waiterinfo->first_name; ?></p>
                </div>
                <table class="token-items">
                    <thead>
                        <tr>
                            <th>Q</th>
                            <th><?php echo display('item'); ?></th>
                            <th class="size" align="center"><?php echo display('size'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($$loopVariableName as $item) { ?>
                            <tr>
                                <td><?php echo $item->menuqty; ?></td>
                                <td><?php echo $item->ProductName; ?><br><?php echo $item->notes; ?></td>
                                <td class="size"><?php echo $item->variantName; ?></td>
                            </tr>
                            <?php if (!empty($item->add_on_id)) {
                                $addons = explode(",", $item->add_on_id);
                                $addonsqty = explode(",", $item->addonsqty);
                                $y = 0;
                                foreach ($addons as $addonsid) {
                                    $adonsinfo = $this->order_model->read('*', 'add_ons', array('add_on_id' => $addonsid)); ?>
                                    <tr>
                                        <td colspan="2"><?php echo $adonsinfo->add_on_name; ?></td>
                                        <td class="size"><?php echo $addonsqty[$y]; ?></td>
                                    </tr>
                        <?php $y++;
                                }
                            }
                        } ?>
                    </tbody>
                </table>
            </div>
    <?php }
    }
    $this->order_model->getTokenNumber(--$tokenNumber);
    ?>
</body>

</html>