<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo base_url('application/modules/dailyreport/assets/css/bootstrap.min.css'); ?>">
    <title>Sales</title>
    <link rel="stylesheet" href="<?php echo base_url('application/modules/dailyreport/assets/css/sales_styles.css') ?>">
</head>
<body>
    <div class="container mt-5">
        <div class="title">Hotel Report</div>
        <div class="summary-row d-flex gap">
            <div class="summary-card torders">
                <div class="summary-label">Today Orders</div>
                <div class="summary-value" id="today-orders"><?php echo isset($todayOrders) ? number_format($todayOrders) : 0; ?></div>
            </div>
            <div class="summary-card tsales">
                <div class="summary-label">Today Sales</div>
                <div class="summary-value" id="today-sales">₹<?php echo isset($todaySales) ? number_format($todaySales) : 0; ?></div>
            </div>
            <div class="summary-card morders">
                <div class="summary-label">Monthly Orders</div>
                <div class="summary-value" id="monthly-orders"><?php echo isset($monthlyOrders) ? $monthlyOrders : 0; ?></div>
            </div>
            <div class="summary-card msales">
                <div class="summary-label">Monthly Sales</div>
                <div class="summary-value" id="monthly-sales">₹<?php echo isset($monthlySales) ? number_format($monthlySales) : 0; ?></div>
            </div>
            </div>
            <div class='d-grid gap-2'>
                <button type="button" class="btn btn-primary">Calculate Profit</button>
                <button type="button" class="btn btn-warning">Print Report</button>
            </div> 
        </div>   
    </div>
    <div>
        <table class="table ">
            <th>
                <td>Select Employee</td>
                <td>Employee Name</td>
                <td>Employee Role</td>
                <td>Employee Salary</td>
                <td>Employee Action</td>
            </th>
            <tr>
                <td><input type="checkbox" class="" name="" id=""></td>
                <td>Wadal Shah</td>
                <td>Senior Manager</td>
                <td>1500 </td>
                <td>1500 </td>
                <td>
                    <a href="" class="btn">Edit</i></a>
                    <a href="" class="btn btn-danger">Delete</a>
                </td>
            </tr>
        </table>
    </div>

    <script src="<?php echo base_url('/assets/js/jquery-3.3.1.min.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('application/modules/dailyreport/assets/js/bootstrap.min.js') ?>" type="text/javascript"></script>
</body>
</html>
