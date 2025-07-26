<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="m-0">Select Employee Salary Expenditure</h2>
        <button type="button" class="btn btn-success" id="add-employee-btn" data-toggle="modal" data-target="#employeeModal">
            <i class="fa fa-plus"></i> Add New Employee
        </button>
    </div>
    <table class="table table-fixed table-bordered table-hover bg-white wpr_100" id="employee-table">
        <thead class="thead-dark">
            <tr>
                <th class="text-center">Select</th>
                <th class="text-center">Employee Name</th>
                <th class="text-center">Employee Role</th>
                <th class="text-center">Employee Salary</th>
                <th class="text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($employees)) {
                foreach ($employees as $employee) { ?>
                    <tr id="employee-row-<?php echo htmlspecialchars($employee->emp_id, ENT_QUOTES, 'UTF-8'); ?>">
                        <td class="text-center">
                            <input type="checkbox" class="form-check-input mx-auto employee-checkbox" data-id="<?php echo htmlspecialchars($employee->emp_id, ENT_QUOTES, 'UTF-8'); ?>" data-salary="<?php echo htmlspecialchars($employee->emp_salary, ENT_QUOTES, 'UTF-8'); ?>">
                        </td>
                        <td><?php echo htmlspecialchars($employee->emp_name, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($employee->emp_role_name, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-right">₹<?php echo htmlspecialchars(number_format($employee->emp_salary, 2), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-primary btn-sm edit-employee" data-id="<?php echo htmlspecialchars($employee->emp_id, ENT_QUOTES, 'UTF-8'); ?>" data-name="<?php echo htmlspecialchars($employee->emp_name, ENT_QUOTES, 'UTF-8'); ?>" data-role="<?php echo htmlspecialchars($employee->emp_role_name, ENT_QUOTES, 'UTF-8'); ?>" data-salary="<?php echo htmlspecialchars($employee->emp_salary, ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-edit"></i> Edit</button>
                            <button type="button" class="btn btn-danger btn-sm delete-employee" data-id="<?php echo htmlspecialchars($employee->emp_id, ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-trash"></i> Delete</button>
                        </td>
                    </tr>
            <?php }
            } else { ?>
                <tr>
                    <td colspan="5" class="text-center">No employees found.</td>
                </tr>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="text-right">Total Employees Expenses</th>
                <th id="total_salary" class="text-right">
                    ₹<span id="total_salary_amount">0.00</span>
                </th>
                <th>
                    <button type="button" class="btn btn-primary btn-sm edit-employee"><i class="fa fa-edit"></i>Add To Expenses</button>
                    <!-- <button type="button" class="btn btn-danger btn-sm delete-employee"><i class="fa fa-trash"></i> Delete</button> -->
                </th>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Add/Edit Employee Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">Add New Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="employee-form" action="<?php echo base_url('ordermanage/order/create_employee'); ?>" method="post">
                    <!-- CSRF Token -->
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label for="emp_name">Employee Name</label>
                        <input type="text" class="form-control" id="emp_name" name="emp_name" required>
                    </div>
                    <div class="form-group">
                        <label for="emp_role_id">Employee Role</label>
                        <select class="form-control" id="emp_role_id" name="emp_role_id" required>
                            <option value="">Select Role</option>
                            <?php if (!empty($employee_roles)) {
                                foreach ($employee_roles as $role) { ?>
                                    <option value="<?php echo htmlspecialchars($role->emp_role_id, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($role->emp_role_name, ENT_QUOTES, 'UTF-8'); ?></option>
                            <?php }
                            } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="emp_salary">Salary</label>
                        <input type="number" class="form-control" id="emp_salary" name="emp_salary" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="save-employee-btn">Save Employee</button>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url('application/modules/ordermanage/assets/js/todaycharityorder.js'); ?>" type="text/javascript"></script>