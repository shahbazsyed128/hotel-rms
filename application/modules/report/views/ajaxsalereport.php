<link href="<?php echo base_url('application/modules/report/assets/css/ajaxsalereport.css'); ?>" rel="stylesheet" type="text/css"/>

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<?php
$totalprice = 0;
$tableID = 1;
$categories = [
    'Guest' => [],
    'Charity' => [],
    'Employee' => [],
    'All Records' => []
];
$allRecords = [];

// Organize the records by customer type and collect all records
if ($preports) {
    foreach ($preports as $preport) {
        foreach ($preport as $pitem) {
            $categories[$pitem->customer_type][] = $pitem;
            $allRecords[] = $pitem;
        }
    }
    $categories['All Records'] = $allRecords;
}

function toKebabCase($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}
?>

<!-- Bootstrap Tabs for Categorized Reports -->
<div class="tabbable">
    <ul class="nav nav-tabs" role="tablist">
        <?php foreach ($categories as $category => $records): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo $tableID === 1 ? 'active' : ''; ?> <?php echo toKebabCase($category); ?>" data-toggle="tab" href="#tab<?php echo $tableID; ?>" role="tab">
                    <?php echo ucfirst($category); ?>
                </a>
            </li>
            <?php $tableID++; ?>
        <?php endforeach; ?>
    </ul>
    <div class="tab-content">
        <?php $tableID = 1; ?>
        <?php foreach ($categories as $category => $records): ?>
            <div class="tab-pane <?php echo $tableID === 1 ? 'active' : ''; ?>" id="tab<?php echo $tableID; ?>" role="tabpanel">
                <h3><?php echo ucfirst($category); ?></h3>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover datatable" id="respritbl<?php echo toKebabCase($category); ?>">
                        <thead>
                            <tr>
                                <th><?php echo display('Sale_date'); ?></th>
                                <th><?php echo display('invoice_no'); ?></th>
                                <th><?php echo display('customer_name'); ?></th>
                                <th><?php echo display('paymd'); ?></th>
                                <th><?php echo display('order_total'); ?></th>
                                <th><?php echo display('vat_tax1'); ?></th>
                                <th><?php echo display('service_chrg'); ?></th>
                                <th><?php echo display('discount'); ?></th>
                                <th><?php echo display('total_ammount'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $categoryTotal = 0;
                            foreach ($records as $pitem) {
                                $categoryTotal += $pitem->bill_amount;
                            ?>
                                <tr>
                                    <td><?php echo date("d-M-Y", strtotime($pitem->order_date)); ?></td>
                                    <td><a href="<?php echo base_url("ordermanage/order/orderdetails/" . $pitem->order_id); ?>" target="_blank">
                                            <?php echo $pitem->saleinvoice; ?>
                                        </a></td>
                                    <td><?php echo $pitem->customer_name; ?></td>
                                    <td><?php echo $pitem->payment_method; ?></td>
                                    <td class="order_total"><?php echo ($currency->position == 1 ? $currency->curr_icon : '') . $pitem->total_amount . ($currency->position == 2 ? $currency->curr_icon : ''); ?></td>
                                    <td><?php echo $pitem->VAT; ?></td>
                                    <td><?php echo $pitem->service_charge; ?></td>
                                    <td><?php echo $pitem->discount; ?></td>
                                    <td class="total_ammount"><?php echo ($currency->position == 1 ? $currency->curr_icon : '') . $pitem->bill_amount . ($currency->position == 2 ? $currency->curr_icon : ''); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8" align="right"><b><?php echo display('total_sale'); ?></b></td>
                                <td><b><?php echo ($currency->position == 1 ? $currency->curr_icon : '') . $categoryTotal . ($currency->position == 2 ? $currency->curr_icon : ''); ?></b></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <?php $tableID++; ?>
        <?php endforeach; ?>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('.datatable').each(function() {
            $(this).DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                autoWidth: false
            });
        });

        // Activate Bootstrap tabs
        $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
            $.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
        });

        
    });
</script>
