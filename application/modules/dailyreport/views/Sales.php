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
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">Calculate Profit</button>
                <button type="button" class="btn btn-warning ">Print Report</button>
            </div> 
                <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">Modal Title</h4>
                        </div>
                        <div class="modal-body">
                            <div class="">
                                <div class="">
                                    <h2>Enter Employees Expenditure</h2>
                                    <form id="expenditureForm">
                                        <div class="form-group">
                                                <option value="">Select Employees</option>
                                                <?php foreach ($employees as $employee) { ?>
                                                    <option value="<?php echo $employee['eid']; ?>">
                                                        <?php echo $employee['ename']; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                            <script>
                                                $('#employeeSelect').change(function() {
                                                    var empId = $(this).val();
                                                    if (empId) {
                                                        // Simulate fetching employee details
                                                        var selectedEmployee = <?php echo json_encode($employees); ?>.find(function(emp) {
                                                            return emp.emp_id == empId;
                                                        });
                                                        if (selectedEmployee) {
                                                            $('#empRole').text(selectedEmployee.erole);
                                                            $('#empSalary').text(selectedEmployee.esalary);
                                                            $('#employeeDetails').show();
                                                        }
                                                    } else {
                                                        $('#employeeDetails').hide();
                                                    }
                                                });
                                            </script>
                                        </div>
                                        <div class="form-group">
                                            <label for="expenditureAmount">Expenditure Amount</label>
                                            <input type="number" class="form-control" id="expenditureAmount" name="expenditureAmount" required>
                                        </div>
                                </div>
                                <div class="">
                                    <h2>Enter Items Expenditure</h2>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="saveData()">Save Changes</button>
                        </div>
                        </div>
                    </div>
                </div>   
            </div>
    <script src="<?php echo base_url('/assets/js/jquery-3.3.1.min.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('application/modules/dailyreport/assets/js/bootstrap.min.js') ?>" type="text/javascript"></script>
    <script>
        $(document).ready(function() {
            $('[data-toggle="modal"]').click(function() {
            var modalId = $(this).data('target');
            var id = $(this).data('id'); 

            if (id) {
                $.ajax({
                    url: 'get_modal_data.php',
                    type: 'POST',
                    data: {id: id},
                    success: function(response) {
                        $(modalId + ' .modal-body').html(response);
                    }
                });
            }
            });

            // Function to handle saving data (example)
            window.saveData = function() {
            var modalContent = $('#myModal .modal-body').text();
            $.ajax({
                url: 'save_data.php',
                type: 'POST',
                data: {content: modalContent},
                success: function(response) {
                alert('Data saved!');
                $('#myModal').modal('hide'); // Close the modal
                }
            });
            };
        });
    </script>
</body>
</html>
