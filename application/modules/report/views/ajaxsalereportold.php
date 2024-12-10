<link href="<?php echo base_url('application/modules/report/assets/css/ajaxsalereport.css'); ?>" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<?php
$totalprice = 0;
$tableID = 1; // Initialize a unique identifier for each table instance
if ($preports) {
    foreach ($preports as $preport) {
?>
<div class="table-responsive">
    <table class="table table-bordered table-striped table-hover datatable" id="respritbl<?php echo $tableID; ?>">
        <thead>
            <tr>
                <th><?php echo display('Sale_date') ?></th>
                <th><?php echo display('invoice_no') ?></th>
                <th><?php echo display('customer_name') ?></th>
                <th><?php echo display('paymd'); ?></th>
                <th><?php echo display('order_total'); ?></th>
                <th><?php echo display('vat_tax1') ?></th>
                <th><?php echo display('service_chrg') ?></th>
                <th><?php echo display('discount') ?></th>
                <th><?php echo display('total_ammount'); ?></th>
            </tr>
        </thead>
        <tbody class="ajaxsalereport">
            <?php
            $totalprice = 0;
            if ($preport) {
                foreach ($preport as $pitem) {
                    $totalprice += $pitem->bill_amount;
                    if (!in_array($pitem->customer_type, $printedCustomerTypes)) {
                        echo "<h4>".$pitem->customer_type."</h4>";
                        $printedCustomerTypes[] = $pitem->customer_type;
                    }
            ?>
                    <tr>
                        <td><?php echo date("d-M-Y", strtotime($pitem->order_date)); ?></td>
                        <td><a href="<?php echo base_url("ordermanage/order/orderdetails/" . $pitem->order_id) ?>" target="_blank">
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
            <?php }
            }
            ?>
        </tbody>
        <tfoot class="ajaxsalereport-footer">
            <tr>
                <td colspan="8" align="right"><b><?php echo display('total_sale') ?></b></td>
                <td><b><?php echo ($currency->position == 1 ? $currency->curr_icon : '') . $totalprice . ($currency->position == 2 ? $currency->curr_icon : ''); ?></b></td>
            </tr>
        </tfoot>
    </table>
</div>
<?php $tableID++; } } ?>


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
    });
</script>
