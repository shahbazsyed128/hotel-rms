<!-- application/modules/report/views/ajaxbeveragereport.php -->
<div class="container">
    <h3><?php echo $title; ?></h3>
    
    <!-- Display settings info if needed -->
    <p>Currency: <?php echo $currency->currency_name; ?></p>

    <table id="respritbl" class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Order Date</th>
                <th>Product Name</th>
                <th>Quantity</th>
                <th>Total Price</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($preport)) : ?>
                <?php foreach ($preport as $report) : ?>
                    <tr>
                        <td><?php echo $report->order_id; ?></td>
                        <td><?php echo $report->order_date; ?></td>
                        <td><?php echo $report->product_name; ?></td>
                        <td><?php echo $report->quantity; ?></td>
                        <td><?php echo $report->total_price; ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5">No records found for the selected date range.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
