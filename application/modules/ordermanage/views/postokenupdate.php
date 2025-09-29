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

        th.size {
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
        var returnurl = pstatus == 0
            ? "<?php echo base_url('ordermanage/order/pos_invoice'); ?>"
            : "<?php echo base_url('ordermanage/order/pos_invoice'); ?>?tokenorder=<?php echo $orderinfo->order_id; ?>";
        window.print();
        setInterval(function () {
            document.location.href = returnurl;
        }, 3000);
    </script>
</head>

<body>
<?php
/**
 * Robust token print view:
 * - Merge base items and update deltas (SUM with +/-) into a single map per kitchen.
 * - Print exactly one row per unique (menu_id|varientid|addonsuid).
 * - Add-ons are rendered as their own rows tied to the parent key to avoid cross-merging.
 * - Print negative deltas (returns) as negative quantities (e.g., -1).
 * - Only print rows with final qty != 0.
 * - Deterministic token write-back at the end.
 */

$this->load->model('order_model');

// --- Helpers ---
function iint($v) { return (int)$v; }

function generateTableRow($quantity, $productName, $notes, $variantName) {
    $q = iint($quantity);
    $p = htmlspecialchars((string)($productName ?? ''), ENT_QUOTES, 'UTF-8');
    $n = htmlspecialchars((string)($notes ?? ''), ENT_QUOTES, 'UTF-8');
    $v = htmlspecialchars((string)($variantName ?? ''), ENT_QUOTES, 'UTF-8');
    return "<tr>
                <td>{$q}</td>
                <td>{$p}" . ($n !== '' ? "<br>{$n}" : "") . "</td>
                <td class='size'>{$v}</td>
            </tr>";
}

// --- Group incoming items by kitchen safely ---
$itemsByKitchen = [];
if (!empty($iteminfo)) {
    foreach ($iteminfo as $it) {
        $kid = $it->kitchenid ?? 0;
        if (!isset($itemsByKitchen[$kid])) $itemsByKitchen[$kid] = ['iteminfo' => [], 'exitsitem' => []];
        $itemsByKitchen[$kid]['iteminfo'][] = $it;
    }
}
if (!empty($exitsitem)) {
    foreach ($exitsitem as $ex) {
        $kid = $ex->kitchenid ?? 0;
        if (!isset($itemsByKitchen[$kid])) $itemsByKitchen[$kid] = ['iteminfo' => [], 'exitsitem' => []];
        $itemsByKitchen[$kid]['exitsitem'][] = $ex;
    }
}

// --- Token handling (deterministic) ---
$startTokenNumber   = (int)$this->order_model->getTokenNumber(); // assumed getter when no arg
$currentTokenNumber = $startTokenNumber;
$printedTokens      = 0;


// --- Render per kitchen ---
foreach ($itemsByKitchen as $kid => $group) {
    // Build a line map: unique key => ['qty','name','notes','variant']
    $lines = [];

    // 1) Base items
    $seenFromBase = [];
    if (!empty($group['iteminfo'])) {
        foreach ($group['iteminfo'] as $item) {
            $menuId    = $item->menu_id   ?? $item->menuid   ?? null;
            $variantId = $item->varientid ?? null;
            $addonsUid = (string)($item->addonsuid ?? '');
            if ($menuId === null || $variantId === null) continue;

            $key = $menuId . '|' . $variantId . '|' . $addonsUid;

            if (!isset($lines[$key])) {
                $lines[$key] = [
                    'qty'     => 0,
                    'name'    => (string)($item->ProductName ?? ''),
                    'notes'   => (string)($item->notes ?? ''),
                    'variant' => (string)($item->variantName ?? ''),
                ];
            }
            $lines[$key]['qty'] += iint($item->menuqty ?? 0);
            // mark base presence
            $seenFromBase[$key] = true;

            // Base add-ons as separate rows (tied to parent key)
            if (!empty($item->add_on_id)) {
                $addons    = explode(",", (string)$item->add_on_id);
                $addonsqty = !empty($item->addonsqty) ? explode(",", (string)$item->addonsqty) : [];
                $limit     = min(count($addons), count($addonsqty));
                for ($i = 0; $i < $limit; $i++) {
                    $addonsid = trim($addons[$i]);
                    $aqty     = iint($addonsqty[$i] ?? 0);
                    if ($addonsid === '' || $aqty <= 0) continue;

                    // Read once per row (you could cache if needed)
                    $adonsinfo = $this->order_model->read('*', 'add_ons', ['add_on_id' => $addonsid]);
                    $addonName = $adonsinfo->add_on_name ?? ('Addon #' . $addonsid);

                    $akey = 'addon|' . $addonsid . '|' . $key;
                    if (!isset($lines[$akey])) {
                        $lines[$akey] = [
                            'qty'     => 0,
                            'name'    => (string)$addonName,
                            'notes'   => '',
                            'variant' => '',
                        ];
                    }
                    $lines[$akey]['qty'] += $aqty;
                }
            }
        }
    }

    // 2) Update items (delta via SUM with +/-)
    if (!empty($group['exitsitem'])) {
        foreach ($group['exitsitem'] as $exititem) {
            $menuId    = $exititem->menu_id   ?? $exititem->menuid   ?? null;
            $variantId = $exititem->varientid ?? null;
            $addonsUid = (string)($exititem->addonsuid ?? '');
            if ($menuId === null || $variantId === null) continue;

            // Aggregate delta for this exact combination
            $isexitsitem = $this->order_model->readupdate(
                'SUM(CASE 
                    WHEN isupdate IS NULL THEN qty
                    WHEN isupdate = "-" THEN -qty
                    ELSE qty
                 END) AS totalqty',
                'tbl_updateitems',
                [
                    'ordid'     => $orderinfo->order_id,
                    'menuid'    => $menuId,
                    'varientid' => $variantId,
                    'addonsuid' => $addonsUid
                ]
            );

            $delta = iint($isexitsitem->totalqty ?? 0);
            if ($delta === 0) continue;

            $key = $menuId . '|' . $variantId . '|' . $addonsUid;

            if ($delta < 0) {
                // 🔻 PRINT RETURNS AS NEGATIVE LINES (separate entry)
                $dkey = 'return|' . $key;
                if (!isset($lines[$dkey])) {
                    $lines[$dkey] = [
                        'qty'     => 0,
                        'name'    => (string)($exititem->ProductName ?? ''),
                        'notes'   => (string)($exititem->notes ?? ''),
                        'variant' => (string)($exititem->variantName ?? ''),
                    ];
                }
                $lines[$dkey]['qty'] += $delta; // e.g., -1, -2
            } else {
                // ➕ keep existing behavior for positive deltas (additions)
                if (empty($seenFromBase[$key])) {
                    if (!isset($lines[$key])) {
                        // If base didn't exist (e.g., only deltas), initialize from exititem
                        $lines[$key] = [
                            'qty'     => 0,
                            'name'    => (string)($exititem->ProductName ?? ''),
                            'notes'   => (string)($exititem->notes ?? ''),
                            'variant' => (string)($exititem->variantName ?? ''),
                        ];
                    }
                    $lines[$key]['qty'] += $delta;
                }
            }
        }
    }

    // 3) Build rows once, print both positive and negative final qty
    $rows = '';
    foreach ($lines as $line) {
        if (iint($line['qty']) != 0) { // <-- print negatives too
            $rows .= generateTableRow($line['qty'], $line['name'], $line['notes'], $line['variant']);
        }
    }

    // 4) If there is anything to print for this kitchen, render the token block
    if ($rows !== '') {
        echo "<div class='token'>
                <div class='token-header'>
                    <h1>Token No: {$currentTokenNumber}</h1>
                    <p>" . display('date') . ": " . date('M d, Y', strtotime($orderinfo->order_date)) . " - " . date('h:i:s A') . "</p>
                    <p>" . htmlspecialchars((string)($customerinfo->customer_name ?? ''), ENT_QUOTES, 'UTF-8') . "</p>
                </div>
                <div class='token-details'>
                    <p>" . display('table') . ": " . (!empty($tableinfo) ? htmlspecialchars((string)$tableinfo->tablename, ENT_QUOTES, 'UTF-8') : 'N/A') . "</p>
                    <p>" . display('ord_number') . ": " . htmlspecialchars((string)$orderinfo->order_id, ENT_QUOTES, 'UTF-8') . "</p>
                </div>
                <div class='token-details'>
                    <p>" . display('waiter') . ": " . htmlspecialchars((string)($waiterinfo->first_name ?? ''), ENT_QUOTES, 'UTF-8') . "</p>
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
                        {$rows}
                    </tbody>
                </table>
            </div>";
        $currentTokenNumber++;
        $printedTokens++;
    }
}

// --- Write back the last printed token once (mirrors your original setter-ish pattern)
if ($printedTokens > 0) {
    $lastPrinted = $currentTokenNumber - 1;
    $this->order_model->getTokenNumber($lastPrinted);
}
?>
</body>
</html>
