<?php
// ============================================================================
// expenses.php  (Bootstrap 3.3.7 + jQuery)
// ============================================================================
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Expenses</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 3.3.7 + jQuery (remove if your layout already includes them) -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <style>
    /* Professional polish */
    body { background:#f7f7f9; }
    .panel { border-radius:6px; }
    .panel-heading { font-weight:600; }
    .help-block { margin-bottom:0; }
    .form-horizontal .control-label { text-align:left; }
    .is-invalid { border-color:#d9534f; }
    .table thead tr th { background:#f0f7ff; }
    .table tfoot tr th { background:#fafafa; }
    .text-right { text-align:right; }
    .m-t-10 { margin-top:10px; }
    .m-b-0 { margin-bottom:0; }
  </style>
</head>
<body>

<div class="container" style="max-width:1100px; padding-top:20px;">

  <!-- ========================= Header ========================= -->
  <div class="page-header" style="margin-top:0;">
    <h3 class="m-b-0">Manage Expenses <small class="text-muted">Professional & Clear UI</small></h3>
  </div>

  <!-- ========================= Entry Form ========================= -->
  <div class="panel panel-default">
    <div class="panel-heading">Add Expense</div>
    <div class="panel-body">
      <form id="expenseForm" class="form-horizontal">

        <!-- Category -->
        <div class="form-group">
          <label for="category" class="col-sm-2 control-label">Category</label>
          <div class="col-sm-4">
            <select id="category" name="category" class="form-control" required>
              <option value="">-- Select Category --</option>
              <?php foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category->category_id); ?>">
                  <?php echo htmlspecialchars($category->category_name); ?>
                </option>
              <?php endforeach; ?>
              <option value="__add_category__">➕ Add new category…</option>
            </select>
            <span class="help-block">Choose the type of expense.</span>
          </div>
        </div>

        <!-- User/Vendor -->
        <div class="form-group">
          <label for="user" class="col-sm-2 control-label">User / Vendor</label>
          <div class="col-sm-4">
            <select id="user" name="user" class="form-control" required disabled>
              <option value="">-- Select User/Vendor --</option>
            </select>
            <span class="help-block">Select who this expense is for.</span>
          </div>
          <button disabled="true" type="button" id="addUserBtn" class="btn btn-primary">➕ Add User/Vendor</button>

        </div>

        <!-- Rate & Quantity -->
        <div class="form-group">
          <label for="rate" class="col-sm-2 control-label">Rate</label>
          <div class="col-sm-2">
            <input type="number" step="0.01" id="rate" name="rate" class="form-control" readonly>
            <span class="help-block" id="rateHint"></span>
          </div>

          <label for="quantity" class="col-sm-2 control-label">Quantity</label>
          <div class="col-sm-2">
            <input type="number" id="quantity" name="quantity" class="form-control" min="1" placeholder="1">
            <span class="help-block">Enter how many units/days/liters.</span>
          </div>

          <div class="col-sm-4">
            <button type="submit" class="btn btn-primary">
              <span class="glyphicon glyphicon-plus"></span> Add Expense
            </button>
          </div>
        </div>

      </form>
    </div>
  </div>

  <!-- ========================= Today Expenses ========================= -->
  <div class="panel panel-default">
    <div class="panel-heading">Today’s Expenses</div>
    <div class="panel-body">
      <div class="table-responsive">
        <table class="table table-bordered table-striped" id="expensesTable">
          <thead>
            <tr>
              <th class="text-center" style="width:60px;">#</th>
              <th class="text-center">Category</th>
              <th class="text-center">User/Vendor</th>
              <th class="text-right" style="width:120px;">Rate</th>
              <th class="text-right" style="width:100px;">Qty</th>
              <th class="text-right" style="width:140px;">Amount</th>
              <th class="text-center" style="width:110px;">Action</th>
            </tr>
          </thead>
          <tbody id="expenseRows"></tbody>
          <tfoot>
            <tr>
              <th colspan="5" class="text-right">Total:</th>
              <th class="text-right" id="grandTotal">0.00</th>
              <th></th>
            </tr>
          </tfoot>
        </table>
      </div>
      <!-- Optional: place for server errors/success -->
      <p id="serverStatus" class="text-muted m-t-10"></p>
    </div>
  </div>

</div><!-- /container -->

<!-- ========================= Modals (Bootstrap 3) ========================= -->

<!-- Add Category -->
<div class="modal fade" id="modalAddCategory" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formAddCategory">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add Category</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Category Name (label)</label>
            <input type="text" class="form-control" id="newCatLabel" placeholder="e.g., Water" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" id="closeAddCategoryModal" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" id="addCategory" class="btn btn-primary">Save Category</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add User/Vendor (fields change by category) -->
<div class="modal fade" id="modalAddEntity" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formAddEntity">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Add User/Vendor</h4>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label>Category</label>
            <input type="text" class="form-control" id="modalCatName" readonly>
          </div>
          <div class="form-group">
            <label>User/Vendor Name</label>
            <input type="text" class="form-control" id="user-name" placeholder="e.g., Ali Milk Supplier" required>
          </div>
          <div class="form-group">
            <label>item/Name</label>
            <input type="text" class="form-control" id="user-item-name" placeholder="e.g., Milk" required>
          </div>
         <div class="form-group">
            <label>Unit</label>
            <input type="text" class="form-control" id="user-item-unit" placeholder="e.g., Litre" required>
          </div>
          <div class="form-group">
            <label>Price</label>
            <input type="text" class="form-control" id="user-item-price" placeholder="e.g., 169.0000" required>
          </div>
          <div id="dynamicRateFields"></div>
          <p class="text-muted" id="dynamicHint"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" id="addUserVendor" class="btn btn-primary">Save User/Vendor</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- jQuery + Bootstrap JS (remove if already included globally) -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
var expenses = []; // {catKey, catName, userId, userName, rate, qty, amount}
var __nextId = 1000; // for new users/vendors added client-side
// ========================== ELEMENTS ==========================
var $categoryEl = $('#category');
var $userEl     = $('#user');
var $rateEl     = $('#rate');
var $rateHintEl = $('#rateHint');
var $qtyEl      = $('#quantity');
var $rowsEl     = $('#expenseRows');
var $totalEl    = $('#grandTotal');
var $statusEl   = $('#serverStatus');

var $modalAddCategory = $('#modalAddCategory');
var $modalAddUser     = $('#modalAddUser');

var $newCatKey   = $('#newCatKey');
var $newCatLabel = $('#newCatLabel');

var $modalCatName = $('#modalCatName');
var $newUserName  = $('#newUserName');
var $dynFields    = $('#dynamicRateFields');
var $dynHint      = $('#dynamicHint');

// ========================== HELPERS ==========================
function toMoney(n){ return (Number(n || 0)).toFixed(2); }
function clearInvalid(){ $categoryEl.add($userEl).add($rateEl).add($qtyEl).removeClass('is-invalid'); }

function setRateHint(catKey){
  var txt = '';
  if (catKey === 'employee')          txt = 'Rate = daily wages; Quantity = number of days.';
  else if (catKey === 'milk')         txt = 'Rate = per liter; Quantity = liters.';
  else if (catKey === 'gas')          txt = 'Rate = per unit; Quantity = units consumed.';
  else if (catKey === 'electricity')  txt = 'Rate = per kWh; Quantity = kWh consumed.';
  else                                txt = 'Enter a rate appropriate for this expense type.';
  $rateHintEl.text(txt);
}


$categoryEl.on('change', function(){
  var val = $(this).val();
  var catName = $(this).find('option:selected').text();
  $modalCatName.val(catName);

  if(val && val !== '__add_category__'){
    getCategoryEntities(val);
    $('#addUserBtn').prop('disabled', false);
  } else if(val === '__add_category__') {
    $modalAddCategory.modal('show');
    // Reset selection
    $categoryEl.val('');
  } else {
    $userEl.prop('disabled', true).empty()
      .append('<option value="">-- Select User/Vendor --</option>');
    setRateHint('');
  }
});


 $userEl.on('change', function(){
    var val = $(this).val();
    // Get selected option's data attributes
    var $opt = $(this).find('option:selected');
    var itemName = $opt.data('item_name') || '';
    var unit = $opt.data('unit') || '';
    var price = $opt.data('price') || '';

    // Set rate field if price is available
    if (price !== '') {
      $rateEl.val(price);
    } else {
      $rateEl.val('');
    }

    // Optionally, show hint or set placeholder for quantity/unit
    if (unit) {
      $qtyEl.attr('placeholder', unit);
    } else {
      $qtyEl.attr('placeholder', '1');
    }

    // Optionally, show item name in rate hint
    if (itemName) {
      $rateHintEl.text('Item: ' + itemName + (unit ? ' (' + unit + ')' : ''));
    } else {
      setRateHint($categoryEl.val());
    }

    // If "Add new user/vendor…" is selected, open modal
    if (val === '__add_user__') {
      $('#modalAddEntity').modal('show');
      // Reset selection
      $userEl.val('');
    }
  });
  



// Add Expense
$('#expenseForm').on('submit', function(e){
  e.preventDefault();
  clearInvalid();

  var catKey  = $categoryEl.val();
  var usrVal  = $userEl.val();
  var rate    = Number($rateEl.val());
  var qty     = Number($qtyEl.val());
  var rate_id = $userEl.find('option:selected').data('rate_id') || '';

  var ok = true;
  if (!catKey){ $categoryEl.addClass('is-invalid'); ok = false; }
  if (!usrVal){ $userEl.addClass('is-invalid'); ok = false; }
  if (!(rate >= 0)){ $rateEl.addClass('is-invalid'); ok = false; }
  if (!(qty > 0)){ $qtyEl.addClass('is-invalid'); ok = false; }
  if (!ok) return;

  var catName  = $categoryEl.find('option:selected').text();
  var userName = $userEl.find('option:selected').text();
  // remove "Add new user…" suffix if present visually
  userName = userName.replace(/— Rate:.*$/,'').trim();
  var amount = rate * qty;
  data = { category_id: catKey, catName: catName.trim(), entity_id: Number(usrVal), userName: userName, rate_id: rate_id, rate: rate, qty: qty, amount: amount };
  addExpense(data);
});

function addExpense(data) {
    $.ajax({
    url: 'addexpense',
    type: 'GET',
    data: data,
    dataType: 'json',
    success: function(resp) {
      if (resp.success) {
        console.log(resp);
      } 
    },
    error: function(xhr){
      alert('Error: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.statusText));
    },
    complete: function() {
    }
  });
}

$('#addUserBtn').on('click', function(){
  $('#modalAddEntity').modal('show');
});
// Save Category via Ajax on button click
$('#addCategory').on('click', function(e){
  e.preventDefault();
  var label = $('#newCatLabel').val().trim();
  if (!label) {
    alert('Please enter a category name.');
    return;
  }
  // Optionally, disable button to prevent double submit
  $(this).prop('disabled', true);

  $.ajax({
    url: 'addcategory',
    type: 'GET',
    data: { category_name: label },
    dataType: 'json',
    success: function(resp) {
      if (resp.success) {
        // Optionally, reload categories from server or append new one
        $.getJSON('getcategories', function(categories){
          // console.log(categories);
          categoryLabels = categories;
          $categoryEl.empty().append('<option value="">-- Select Category --</option>');
          $.each(categoryLabels, function(id,category){
            $categoryEl.append($('<option>').val(category.category_id).text(category.category_name));
          });
          $categoryEl.append('<option value="__add_category__">➕ Add new category…</option>');
          $categoryEl.val(resp.new_id).change();
        });
        $('#closeAddCategoryModal').click();
      } else {
        alert(resp.message || 'Failed to add category.');
      }
    },
    error: function(xhr){
      alert('Error: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.statusText));
    },
    complete: function() {
      $('#addCategory').prop('disabled', false);
    }
  });
});



$('#addUserVendor').on('click', function(e){
  e.preventDefault();
  var catKey = $categoryEl.val();
  var name   = ($('#user-name').val() || '').trim();
  var price   = Number($('#user-item-price').val());
  var item_name = ($('#user-item-name').val() || '').trim();
  var unit = ($('#user-item-unit').val() || '').trim();

  if (!catKey || !name || !item_name || !unit || !price) {
    alert('Please fill all required fields.');
    return;
  }

  $.ajax({
    url: 'addCategoryEntity',
    type: 'GET',
    data: { category_id: catKey, name: name, item_name: item_name, unit: unit, price: price },
    dataType: 'json',
    success: function(resp) {
      if (resp.success) {
        // Optionally reload users/entities for this category
        getCategoryEntities(catKey);
        $('#modalAddEntity').modal('hide');
      } else {
        alert(resp.message || 'Failed to add user/vendor.');
      }
    },
    error: function(xhr){
      alert('Error: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.statusText));
    }
  });
});


function getCategoryEntities(id) {
  $.ajax({
    url: 'getCategoryEntities',
    type: 'GET',
    data: { category_id: id },
    dataType: 'json',
    success: function(resp){
      // resp should be an array of users/entities: [{id, name, rate}, ...]
      $('#addUserBtn').prop('disabled', false);
      $userEl.empty().append('<option value="">-- Select User/Vendor --</option>').prop('disabled', false);
      if (Array.isArray(resp)) {
        resp.forEach(function(u){
          $userEl.append(
            $('<option>')
              .val(String(u.entity_id))
              .text(u.entity_name)
              .attr('data-item_name', u.item_name || '')
              .attr('data-unit', u.unit || '')
              .attr('data-rate_id', u.rate_id || '')
              .attr('data-price', u.price || '')
          );
        });
      }
    },
    error: function(){
      alert('Failed to load users/vendors for this category.');
    }
  });
}


function getTodayExpenses() {
  $.ajax({
    url: 'getexpenses',
    type: 'GET',
    dataType: 'json',
    success: function(resp) {
      if (Array.isArray(resp)) {
        expenses = resp.map(function(e){
          return {
            catKey: e.category_id,
            catName: e.category_name,
            userId: e.entity_id,
            userName: e.entity_name,
            rate_id: e.rate_id || '',
            rate: Number(e.price) || 0,
            qty: Number(e.quantity) || 0,
            amount: Number(e.total_amount) || 0
          };
        });
        renderExpenses();
      }
    },
    error: function(xhr){
      alert('Error loading expenses: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.statusText));
    }
  });
}
</script>

</body>
</html>
