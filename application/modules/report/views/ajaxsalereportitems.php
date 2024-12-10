<link href="<?php echo base_url('application/modules/report/assets/css/ajaxsalereportitems.css'); ?>" rel="stylesheet" type="text/css"/>
<link href="<?php echo base_url('application/modules/report/assets/css/custom-tabs.css'); ?>" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<?php 
$totalprice = 0;
$tableID = 1;

function toKebabCase($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

// Aggregating items for "All Records"
$aggregatedItems = [];
foreach ($items as $itemGroup) {
    if (!empty($itemGroup)) {
        foreach ($itemGroup as $item) {
            $itemName = $item->ProductName;
            $variantName = $item->variantName; // Include this if variants need to be distinguished
            $key = $itemName . '_' . $variantName; // Unique key per item-variant combo

            $itemPrice = $item->price > 0 
                ? ($item->OffersRate > 0 ? $item->price - ($item->price * $item->OffersRate / 100) : $item->price)
                : ($item->OffersRate > 0 ? $item->mprice - ($item->mprice * $item->OffersRate / 100) : $item->mprice);

            $itemQty = isset($item->totalqty) ? $item->totalqty : 1;
            $totalAmount = $itemQty * $itemPrice;

            if (isset($aggregatedItems[$key])) {
                $aggregatedItems[$key]['quantity'] += $itemQty;
                $aggregatedItems[$key]['totalAmount'] += $totalAmount;
            } else {
                $aggregatedItems[$key] = [
                    'ProductName' => $itemName,
                    'variantName' => $variantName,
                    'quantity' => $itemQty,
                    'totalAmount' => $totalAmount,
                ];
            }
        }
    }
}
?>

<div class="tabbable">
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach ($items as $ctype => $itemGroup): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $tableID === 1 ? 'active' : ''; echo ' ' . toKebabCase($ctype); ?>" data-toggle="tab" href="#tab<?php echo $tableID; ?>" role="tab">
                    <?php echo ucfirst($ctype); ?>
                </a>
            </li>
            <?php $tableID++; ?>
        <?php endforeach; ?>
        <li class="nav-item">
            <a class="nav-link all-records" data-toggle="tab" href="#tabAllRecords" role="tab">All Records</a>
        </li>
    </ul>
    <div class="tab-content">
        <?php $tableID = 1; ?>
        <?php foreach ($items as $ctype => $itemGroup): ?>
            <div class="tab-pane <?php echo $tableID === 1 ? 'active' : ''; ?>" id="tab<?php echo $tableID; ?>" role="tabpanel">
                <h3><?php echo ucfirst($ctype); ?></h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable" id="respritbl<?php echo $tableID; ?>">
                        <thead>
                            <tr>
                                <th><?php echo $name; ?></th>
                                <?php if ($name == "Items Name") { ?>
                                    <th><?php echo display('varient_name'); ?></th>
                                    <th><?php echo display('quantity'); ?></th>
                                <?php } ?>
                                <th><?php echo display('total_amount'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $categoryTotal = 0;
                            if (!empty($itemGroup)) {
                                if ($name == "Items Name") {
                                    foreach ($itemGroup as $item) {
                                        $itemprice = $item->price > 0 
                                            ? ($item->OffersRate > 0 ? $item->price - ($item->price * $item->OffersRate / 100) : $item->price)
                                            : ($item->OffersRate > 0 ? $item->mprice - ($item->mprice * $item->OffersRate / 100) : $item->mprice);
                                        $itemqty = $item->totalqty;
                                        $categoryTotal += $itemqty * $itemprice;
                            ?>
                                        <tr>
                                            <td><?php echo $item->ProductName; ?></td>
                                            <td><?php echo $item->variantName; ?></td>
                                            <td><?php echo $itemqty; ?></td>
                                            <td class="order_total">
                                                <?php echo ($currency->position == 1 ? $currency->curr_icon : '') . ($itemqty * $itemprice) . ($currency->position == 2 ? $currency->curr_icon : ''); ?>
                                            </td>
                                        </tr>
                            <?php
                                    }
                                } else {
                                    foreach ($itemGroup as $item) {
                                        $categoryTotal += $item->totalamount;
                            ?>
                                        <tr>
                                            <td><?php echo $item->ProductName; ?></td>
                                            <td class="total_ammount">
                                                <?php echo ($currency->position == 1 ? $currency->curr_icon : '') . $item->totalamount . ($currency->position == 2 ? $currency->curr_icon : ''); ?>
                                            </td>
                                        </tr>
                            <?php
                                    }
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="<?php echo ($name == "Items Name" ? 3 : 1); ?>" align="right"><b><?php echo display('total_sale'); ?></b></td>
                                <td><b><?php echo ($currency->position == 1 ? $currency->curr_icon : '') . $categoryTotal . ($currency->position == 2 ? $currency->curr_icon : ''); ?></b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php $tableID++; ?>
        <?php endforeach; ?>
        <div class="tab-pane" id="tabAllRecords" role="tabpanel">
            <h3>All Records</h3>
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover datatable" id="respritblAll">
                    <thead>
                        <tr>
                            <th><?php echo $name; ?></th>
                            <?php if ($name == "Items Name") { ?>
                                <th><?php echo display('varient_name'); ?></th>
                                <th><?php echo display('quantity'); ?></th>
                            <?php } ?>
                            <th><?php echo display('total_amount'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($aggregatedItems as $item): ?>
                            <tr>
                                <td><?php echo $item['ProductName']; ?></td>
                                <?php if ($name == "Items Name") { ?>
                                    <td><?php echo $item['variantName']; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                <?php } ?>
                                <td class="order_total">
                                    <?php echo ($currency->position == 1 ? $currency->curr_icon : '') . $item['totalAmount'] . ($currency->position == 2 ? $currency->curr_icon : ''); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="<?php echo ($name == "Items Name" ? 3 : 1); ?>" align="right"><b><?php echo display('total_sale'); ?></b></td>
                            <td><b><?php echo ($currency->position == 1 ? $currency->curr_icon : '') . array_sum(array_column($aggregatedItems, 'totalAmount')) . ($currency->position == 2 ? $currency->curr_icon : ''); ?></b></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.datatable').each(function() {
            $(this).DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true
            });
        });

        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });
    });
</script>
