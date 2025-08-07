<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>PDF Report</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        h2 { text-align: center; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; border: 1px solid #999; text-align: left; }
    </style>
</head>
<body>

     <div style="text-align: center;">
        <img src="<?= base_url('application/modules/dailyreport/assets/img/logo.png'); ?>" style='margin-bottom: 20px' width="400">
        <h1>Hotel Today Report</h1>
     </div>

    <?php if (!empty($report)) { ?>
        <table>
            <thead>
                <tr>
                    <th>Report Date</th>
                    <th>Today Orders</th>
                    <th>Today Sales</th>
                    <th>Items Expenditure</th>
                    <th>Employees Expenditure</th>
                    <th>Today Profit</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($report as $row) { ?>
                <tr>
                    <td><?= $row->report_date ?></td>
                    <td><?= $row->daily_orders ?></td>
                    <td><?= $row->daily_sales ?></td>
                    <td><?= $row->item_expenses ?></td>
                    <td><?= $row->emp_expenses ?></td>
                    <td><?= $row->profit ?></td>

                </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p>No data available.</p>
    <?php } ?>
</body>
</html>
