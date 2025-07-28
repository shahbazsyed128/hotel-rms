<div class="container mt-4">
    <h3>Add Item Expense</h3>
    <form id="expenseForm">
        <div class="row">
            <div class="col-md-6">
                <label for="item_id">Item</label>
                <select id="item_id" name="item_id" class="form-control select2">
                    <option value="">-- Select Item --</option>
                    <?php foreach ($items as $item): ?>
                        <option value="<?= $item->id ?>"><?= $item->item_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="item_name">Or Enter New Item</label>
                <input type="text" id="item_name" name="item_name" class="form-control" placeholder="New item name (optional)">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-6">
                <label for="vendor_id">Vendor</label>
                <select id="vendor_id" name="vendor_id" class="form-control select2">
                    <option value="">-- Select Vendor --</option>
                    <?php foreach ($vendors as $vendor): ?>
                        <option value="<?= $vendor->id ?>"><?= $vendor->vendor_name ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6">
                <label for="vendor_name">Or Enter New Vendor</label>
                <input type="text" id="vendor_name" name="vendor_name" class="form-control" placeholder="New vendor name (optional)">
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-4">
                <label for="quantity_kg">Quantity (KG)</label>
                <input type="number" step="0.01" id="quantity_kg" name="quantity_kg" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="unit_price">Unit Price</label>
                <input type="number" step="0.01" id="unit_price" name="unit_price" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label for="payment_status">Payment Status</label>
                <select id="payment_status" name="payment_status" class="form-control">
                    <option value="Unpaid">Unpaid</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>
        </div>

        <div class="text-end mt-6">
            <button type="submit" class="btn btn-primary">Add Expense</button>
        </div>
    </form>

    <div id="expenseMessage" class="mt-3"></div>

    <div class="mt-5">
        <h3 class="text-center">Item Expenses Preview</h3>
        <table class="table table-bordered" id="expenseTable">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Item</th>
                    <th>Vendor</th>
                    <th>Qty (KG)</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" class="text-end">Grand Total</th>
                    <th id="grandTotal">0.00</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
const csrfName = '<?= $this->security->get_csrf_token_name(); ?>';
const csrfHash = '<?= $this->security->get_csrf_hash(); ?>';

$(document).ready(function () {
    $('.select2').select2({ placeholder: "Select or leave empty" });
    loadExpenses(); // Load on page load

    $('#expenseForm').on('submit', function (e) {
        e.preventDefault();

        const formData = {
            item_id: $('#item_id').val(),
            item_name: $('#item_name').val().trim(),
            vendor_id: $('#vendor_id').val(),
            vendor_name: $('#vendor_name').val().trim(),
            quantity_kg: $('#quantity_kg').val(),
            unit_price: $('#unit_price').val(),
            payment_status: $('#payment_status').val()
        };

        formData[csrfName] = csrfHash;

        $.ajax({
            url: '<?= base_url("ordermanage/order/save_expense_ajax") ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function (res) {
                if (res.success) {
                    // $('#expenseMessage').html('<div class="alert alert-success">Expense added successfully!</div>');
                    $('#expenseForm')[0].reset();
                    $('.select2').val(null).trigger('change');
                    loadExpenses(); // Reload the table
                } else {
                    $('#expenseMessage').html('<div class="alert alert-danger">Error: ' + (res.message || 'Unknown error') + '</div>');
                    console.log('Debug:', res.debug);
                }
            },
            error: function () {
                $('#expenseMessage').html('<div class="alert alert-danger">AJAX request failed.</div>');
            }
        });
    });

    function loadExpenses() {
        $.ajax({
            url: '<?= base_url("ordermanage/order/get_expense_data_ajax") ?>',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                let tbody = '';
                let grandTotal = 0;
                if (data.length > 0) {
                    $.each(data, function (index, row) {
                        const total = parseFloat(row.quantity_kg * row.unit_price).toFixed(2);
                        grandTotal += parseFloat(total);
                        tbody += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${row.item_name}</td>
                                <td>${row.vendor_name}</td>
                                <td>${row.quantity_kg}</td>
                                <td>${row.unit_price}</td>
                                <td>${total}</td>
                                <td>${row.payment_status}</td>
                            </tr>`;
                    });
                } else {
                    tbody = `<tr><td colspan="7" class="text-center">No expenses found.</td></tr>`;
                }

                $('#expenseTable tbody').html(tbody);
                $('#grandTotal').text(grandTotal.toFixed(2));
            },
            error: function () {
                $('#expenseTable tbody').html('<tr><td colspan="7" class="text-center text-danger">Failed to load expenses.</td></tr>');
            }
        });
    }
});
</script>
