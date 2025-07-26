$(document).ready(function() {
        // This variable will hold the IDs of selected employees for later use.
        // You can access it from other scripts if you need to submit this data.
        let selectedEmployeeIds = [];

        // This function calculates the total salary and updates the list of selected employees.
        function calculateTotal() {
            let totalSalary = 0;
            selectedEmployeeIds = []; // Reset the array each time to get a fresh list

            // Iterate over each checked employee checkbox
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

            // Update the total salary display in the footer, formatted to 2 decimal places
            $('#total_salary_amount').text(totalSalary.toFixed(2));

            // For debugging or later use, you can see the list of selected IDs
            // console.log('Selected Employee IDs:', selectedEmployeeIds);
        }

        // Listen for changes on any employee checkbox within the table
        $('#employee-table').on('change', '.employee-checkbox', function() {
            // Toggle a 'table-info' class on the parent row for styling
            // The second argument to toggleClass is a boolean that adds the class if true, and removes if false.
            $(this).closest('tr').toggleClass('table-info', this.checked);

            // Recalculate everything when a checkbox state changes
            calculateTotal();
        });

        calculateTotal();
    });
