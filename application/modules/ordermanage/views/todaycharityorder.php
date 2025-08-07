<meta name="csrf-name" content="<?= $this->security->get_csrf_token_name(); ?>">
<meta name="csrf-hash" content="<?= $this->security->get_csrf_hash(); ?>">
<div class="container-fluid">
    <div class=" d-flex justify-content-between align-items-center mb-3" style="display: flex;
        justify-content: space-between;">
        <h2 class="text-center">Select Employee Salary Expenditure</h2>
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
                            <button type="button" class="btn btn-danger btn-sm delete-employee" data-id="<?= htmlspecialchars($employee->emp_id, ENT_QUOTES, 'UTF-8'); ?>"><i class="fa fa-trash"></i> Delete</button>
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
                    <button type="button" class="btn btn-success btn-sm" id="saveDailyReportBtn"><i class="fa fa-plus"></i> Add To Expenses</button>
                </th>
            </tr>
        </tfoot>
    </table>
</div>

<!-- Add/Edit Employee Modal -->
<div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span a="true">&times;</span>
                </button>
     <h3 class="modal-title" id="employeeModalLabel">Add New Employee</h3>
            </div>
            <div class="modal-body">
                <form id="employee-form" action="" method="post">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                    <div class="form-group">
                        <label for="emp_name">Employee Name</label>
                        <input type="text" class="form-control" id="emp_name" name="emp_name" required>
                    </div>
                    <div class="form-group">
                        <label for="emp_role">Employee Role</label>
                        <select class="form-control" id="emp_role_id" name="emp_role_id" required>
                            <option value="">Select Role</option>
                            <?php if (!empty($roles)) {
                                foreach ($roles as $role) { ?>
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
<script>    

    $(document).ready(function() {
        let selectedEmployeeIds = [];

        function calculateTotal() {
            let totalSalary = 0;
            selectedEmployeeIds = [];


            $('#employee-table .employee-checkbox:checked').each(function() {
                const salary = parseFloat($(this).data('salary'));
                const employeeId = $(this).data('id');

                if (!isNaN(salary)) {
                    totalSalary += salary;
                }
                if (employeeId) {
                    selectedEmployeeIds.push(employeeId);
                }
            });

            $('#total_salary_amount').text(totalSalary.toFixed(2));
        }

        $('#employee-table').on('change', '.employee-checkbox', function() {
            $(this).closest('tr').toggleClass('table-info', this.checked);
            calculateTotal();
        });

        calculateTotal();
    });

    $('#saveDailyReportBtn').on('click', function () {
        const salaryText = $('#total_salary_amount').text().replace(/[^\d.]/g, '');
        const empExpenses = parseFloat(salaryText) || 0;

        const itemExpenses = parseFloat(localStorage.getItem('item_expense_total')) || 0;

        const csrfName = $('meta[name="csrf-name"]').attr('content');
        const csrfHash = $('meta[name="csrf-hash"]').attr('content');


        const payload = {
            employee_expenses: empExpenses,
            item_expenses : itemExpenses
        };
        payload[csrfName] = csrfHash;

        $.ajax({
            url: "<?= base_url('ordermanage/order/save_full_daily_report'); ?>",
            type: 'POST',
            dataType: 'json',
            data: payload,
            success: function (res) {
                console.log('Server Response:', res);
                if (res.success) {
                    alert('✅ Employee expense saved in daily report!');
                } else {
                    alert('❌ Error: ' + (res.message || 'Unknown error'));
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                console.log(data);
                alert('❌ AJAX failed: ' + error);
            }
        });
    });




    $('#save-employee-btn').on('click', function () {
        $('#employee-form').submit();
    });


    $(document).on('click', '.edit-employee', function () {
        const empId = $(this).data('id');
        const empName = $(this).data('name');
        const empSalary = $(this).data('salary');
        const empRoleName = $(this).data('role');

        $('#employeeModalLabel').text('Edit Employee');
        $('#emp_name').val(empName);
        $('#emp_salary').val(empSalary);
        $('#emp_role_id option').filter(function () {
            return $(this).text() === empRoleName;
        }).prop('selected', true);

        // Store employee ID
        $('#employee-form').append(`<input type="hidden" id="emp_id" name="emp_id" value="${empId}">`);
        $('#employeeModal').modal('show');
    });

    

    $('#employee-form').submit(function (e) {
        e.preventDefault();
        const formData = $(this).serialize();
        const isEdit = $('#emp_id').length > 0;
        const url = isEdit
            ? '<?= base_url("ordermanage/order/updateemployee") ?>'
            : '<?= base_url("ordermanage/order/createemployee") ?>';

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    const emp = response.employee;

                    let newRow = `
                        <tr id="employee-row-${emp.emp_id}">
                            <td class="text-center">
                                <input type="checkbox" class="form-check-input mx-auto employee-checkbox"
                                    data-id="${emp.emp_id}" data-salary="${emp.emp_salary}">
                            </td>
                            <td>${emp.emp_name}</td>
                            <td>${emp.emp_role_name}</td>
                            <td class="text-right">₹${parseFloat(emp.emp_salary).toFixed(2)}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-primary btn-sm edit-employee"
                                    data-id="${emp.emp_id}" data-name="${emp.emp_name}" data-role="${emp.emp_role_name}"
                                    data-salary="${emp.emp_salary}">
                                    <i class="fa fa-edit"></i> Edit
                                </button>
                                <button type="button" class="btn btn-danger btn-sm delete-employee"
                                    data-id="${emp.emp_id}">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    `;

                    // Replace existing row or add new
                    if (isEdit) {
                        $('#employee-row-' + emp.emp_id).replaceWith(newRow);
                    } else {
                        $('#employee-table tbody').append(newRow);
                    }

                    $('#employeeModal').modal('hide');
                    $('#employee-form')[0].reset();
                    $('#emp_id').remove(); // Remove hidden ID for future inserts
                    $('#employeeModalLabel').text('Add New Employee');
                    calculateTotal();
                } else {
                    alert(response.message || 'Operation failed');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                alert('Something went wrong.');
            }
        });
    });

    $('#employeeModal').on('hidden.bs.modal', function () {
        $('#employee-form')[0].reset();
        $('#emp_id').remove(); // Remove hidden emp_id input
        $('#employeeModalLabel').text('Add New Employee');
    });


    
    $(document).on('click', '.delete-employee', function () {
        if (!confirm('Are you sure you want to delete this employee?')) return;

        const empId = $(this).data('id');

        $.ajax({
            url: '<?= base_url("ordermanage/order/deleteemployee") ?>',
            type: 'POST',
            data: {
                emp_id: empId,
                '<?= $this->security->get_csrf_token_name(); ?>': '<?= $this->security->get_csrf_hash(); ?>'
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    // Remove row from DOM
                    $('#employee-row-' + empId).fadeOut(300, function () {
                        $(this).remove();
                        calculateTotal(); // Optional: recalculate total salaries
                    });
                } else {
                    alert(response.message);
                }
            },
            error: function () {
                alert('Server error occurred.');
            }
        });
    });

</script>
