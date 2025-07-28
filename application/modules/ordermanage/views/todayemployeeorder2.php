<div class="container mt-4">
    <h3>Add Item Expense</h3>
    <form id="expenseForm">
        <div class="form-group">
            <label for="item_id">Item</label>
            <select id="item_id" name="item_id" class="form-control select2">
                <option value="">-- Select Item --</option>
                <?php foreach ($items as $item): ?>
                    <option value="<?= $item->id ?>"><?= $item->item_name ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mt-2">
            <label for="item_name">Or Enter New Item</label>
            <input type="text" id="item_name" name="item_name" class="form-control" placeholder="New item name (optional)">
        </div>

        <div class="form-group mt-3">
            <label for="vendor_id">Vendor</label>
            <select id="vendor_id" name="vendor_id" class="form-control select2">
                <option value="">-- Select Vendor --</option>
                <?php foreach ($vendors as $vendor): ?>
                    <option value="<?= $vendor->id ?>"><?= $vendor->vendor_name ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group mt-2">
            <label for="vendor_name">Or Enter New Vendor</label>
            <input type="text" id="vendor_name" name="vendor_name" class="form-control" placeholder="New vendor name (optional)">
        </div>

        <div class="form-group mt-3">
            <label for="quantity_kg">Quantity (KG)</label>
            <input type="number" step="0.01" id="quantity_kg" name="quantity_kg" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label for="unit_price">Unit Price</label>
            <input type="number" step="0.01" id="unit_price" name="unit_price" class="form-control" required>
        </div>

        <div class="form-group mt-3">
            <label for="payment_status">Payment Status</label>
            <select id="payment_status" name="payment_status" class="form-control">
                <option value="Unpaid">Unpaid</option>
                <option value="Paid">Paid</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-4">Add Expense</button>
    </form>

    <div id="expenseMessage" class="mt-3"></div>

    
</div>

<script>
    const csrfName = '<?= $this->security->get_csrf_token_name(); ?>'; // usually 'csrf_test_name'
    const csrfHash = '<?= $this->security->get_csrf_hash(); ?>';       // dynamic hash
$(document).ready(function () {
  
    $('.select2').select2({
        placeholder: "Select or leave empty"
    });

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
                    $('#expenseMessage').html('<div class="alert alert-success">Expense added successfully!</div>');
                    $('#expenseForm')[0].reset();
                    $('.select2').val(null).trigger('change');
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
});

</script>
