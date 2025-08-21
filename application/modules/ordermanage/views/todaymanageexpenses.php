<?php
// ============================================================================
// expenses.php  (Bootstrap 3.3.7 + jQuery)
// ============================================================================

// Seed data (replace with your DB data as needed)
// $categories = [
//   'milk'        => 'Milk',
//   'employee'    => 'Employee',
//   'electricity' => 'Electricity',
//   'gas'         => 'Gas',
//   'other'       => 'Other Charges',
// ];

$users = [
  'milk' => [
    ['id'=>1, 'name'=>'Milk Vendor 1', 'rate'=>50],
    ['id'=>2, 'name'=>'Milk Vendor 2', 'rate'=>55],
  ],
  'employee' => [
    ['id'=>3, 'name'=>'John Doe',  'rate'=>500], // daily wages
    ['id'=>4, 'name'=>'Jane Smith','rate'=>600],
  ],
  'gas' => [
    ['id'=>5, 'name'=>'Gas Vendor 1', 'rate'=>100],
    ['id'=>6, 'name'=>'Gas Vendor 2', 'rate'=>110],
  ],
  'electricity' => [
    ['id'=>7, 'name'=>'Electricity Board', 'rate'=>0],
  ],
  'other' => [
    ['id'=>8, 'name'=>'Misc Vendor', 'rate'=>0],
  ],
];

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
          <div class="form-group">
            <label>Category Key (unique, lowercase, no spaces)</label>
            <input type="text" class="form-control" id="newCatKey" placeholder="e.g., water" required>
          </div>
          <p class="text-muted m-b-0">Key is used internally; label shows in the dropdown.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Category</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add User/Vendor (fields change by category) -->
<div class="modal fade" id="modalAddUser" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formAddUser">
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
            <input type="text" class="form-control" id="newUserName" placeholder="e.g., Ali Milk Supplier" required>
          </div>
          <div id="dynamicRateFields"></div>
          <p class="text-muted" id="dynamicHint"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save User/Vendor</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- jQuery + Bootstrap JS (remove if already included globally) -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
// ========================== DATA FROM PHP ==========================
var usersByCategory = <?php echo json_encode($users); ?>;
var categoryLabels  = <?php echo json_encode($categories); ?>;

// ========================== STATE ==========================
var expenses = []; // {catKey, catName, userId, userName, rate, qty, amount}
var __nextId = (function(){
  // next integer id after the highest existing
  try {
    var max = 0;
    Object.keys(usersByCategory).forEach(function(k){
      (usersByCategory[k] || []).forEach(function(u){
        max = Math.max(max, Number(u.id)||0);
      });
    });
    return max + 1;
  } catch(e){ return 1000; }
})();

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

function populateUsers(catKey){
  $userEl.prop('disabled', true).empty()
    .append('<option value="">-- Select User/Vendor --</option>');

  $rateEl.val(''); $qtyEl.val('');

  var list = usersByCategory[catKey] || [];
  list.forEach(function(u){
    $userEl.append(
      $('<option>').val(String(u.id)).text(u.name).attr('data-rate', String(u.rate || 0))
    );
  });

  // Always allow "Add new"
  $userEl.append('<option value="__add_user__">➕ Add new user/vendor…</option>');
  $userEl.prop('disabled', false);
}

function renderTable(){
  $rowsEl.empty();
  var grand = 0;

  expenses.forEach(function(e, idx){
    grand += e.amount;
    var $tr = $('<tr>');
    $tr.append('<td class="text-center">'+(idx+1)+'</td>');
    $tr.append('<td class="text-center">'+e.catName+'</td>');
    $tr.append('<td class="text-center">'+e.userName+'</td>');
    $tr.append('<td class="text-right">'+toMoney(e.rate)+'</td>');
    $tr.append('<td class="text-right">'+e.qty+'</td>');
    $tr.append('<td class="text-right">'+toMoney(e.amount)+'</td>');
    $tr.append('<td class="text-center"><button type="button" class="btn btn-danger btn-xs" data-remove="'+idx+'">Remove</button></td>');
    $rowsEl.append($tr);
  });

  $totalEl.text(toMoney(grand));

  $rowsEl.find('[data-remove]').off('click').on('click', function(){
    var i = Number($(this).attr('data-remove'));
    expenses.splice(i, 1);
    renderTable();
  });
}

function buildDynamicRateFields(catKey){
  $dynFields.empty();
  $dynHint.text('');
  function group(label, innerHtml){
    return $('<div class="form-group">').append('<label>'+label+'</label>').append(innerHtml);
  }

  if (catKey === 'employee'){
    $dynFields
      .append(group('Daily Wages (Rate)', '<input type="number" step="0.01" class="form-control" id="newUserRate" placeholder="e.g., 800" required>'))
      .append(group('Designation (optional)', '<input type="text" class="form-control" id="newUserDesignation" placeholder="e.g., Waiter">'));
    $dynHint.text('Daily wages will be used as the Rate. Quantity on the main form = number of days.');
  }
  else if (catKey === 'milk'){
    $dynFields
      .append(group('Rate per Liter', '<input type="number" step="0.01" class="form-control" id="newUserRate" placeholder="e.g., 55" required>'))
      .append(group('Milk Type (optional)', '<input type="text" class="form-control" id="milkType" placeholder="e.g., Cow/Buffalo">'))
      .append(group('Fat % (optional)', '<input type="number" step="0.01" class="form-control" id="milkFat" placeholder="e.g., 4.5">'));
    $dynHint.text('Rate is per liter. Quantity on the main form = liters.');
  }
  else if (catKey === 'gas'){
    $dynFields
      .append(group('Rate per Unit', '<input type="number" step="0.01" class="form-control" id="newUserRate" placeholder="e.g., 105" required>'))
      .append(group('Account No. (optional)', '<input type="text" class="form-control" id="gasAccount" placeholder="e.g., 123-456-789">'));
    $dynHint.text('Rate per unit. Quantity on the main form = units consumed.');
  }
  else if (catKey === 'electricity'){
    $dynFields
      .append(group('Rate per kWh', '<input type="number" step="0.01" class="form-control" id="newUserRate" placeholder="e.g., 30" required>'))
      .append(group('Meter No. (optional)', '<input type="text" class="form-control" id="meterNo" placeholder="e.g., K-E-123456">'));
    $dynHint.text('Rate per kWh. Quantity on the main form = kWh consumed.');
  }
  else {
    $dynFields
      .append(group('Rate', '<input type="number" step="0.01" class="form-control" id="newUserRate" placeholder="e.g., 0" required>'))
      .append(group('Notes (optional)', '<input type="text" class="form-control" id="otherNotes" placeholder="Any notes">'));
    $dynHint.text('Generic rate; Quantity meaning depends on the expense.');
  }
}

// ========================== EVENTS ==========================
$categoryEl.on('change', function(){
  var val = $(this).val();

  if (val === '__add_category__'){
    // open modal for new category
    $newCatKey.val('');
    $newCatLabel.val('');
    $modalAddCategory.modal('show');

    // reset selection while modal is open
    $(this).val('');
    $userEl.prop('disabled', true).empty().append('<option value="">-- Select User/Vendor --</option>');
    $rateEl.val(''); $rateHintEl.text(''); $qtyEl.val('');
    return;
  }

  setRateHint(val || '');
  if (val){
    populateUsers(val);
  } else {
    $userEl.prop('disabled', true).empty().append('<option value="">-- Select User/Vendor --</option>');
    $rateEl.val(''); $qtyEl.val('');
  }
});

$userEl.on('change', function(){
  var selected = this.options[this.selectedIndex];
  if (!selected) return;

  if (selected.value === '__add_user__'){
    var catKey = $categoryEl.val();
    if (!catKey){ alert('Please select a category first.'); this.value = ''; return; }
    $modalCatName.val(categoryLabels[catKey] || catKey);
    buildDynamicRateFields(catKey);
    $newUserName.val('');
    $modalAddUser.modal('show');

    // reset drop-down until user saved
    this.value = '';
    $rateEl.val('');
    return;
  }

  var rate = $(selected).attr('data-rate') || '';
  $rateEl.val(rate);
  $qtyEl.val('');
});

// Add Expense
$('#expenseForm').on('submit', function(e){
  e.preventDefault();
  clearInvalid();

  var catKey  = $categoryEl.val();
  var usrVal  = $userEl.val();
  var rate    = Number($rateEl.val());
  var qty     = Number($qtyEl.val());

  var ok = true;
  if (!catKey){ $categoryEl.addClass('is-invalid'); ok = false; }
  if (!usrVal){ $userEl.addClass('is-invalid'); ok = false; }
  if (!(rate >= 0)){ $rateEl.addClass('is-invalid'); ok = false; }
  if (!(qty > 0)){ $qtyEl.addClass('is-invalid'); ok = false; }
  if (!ok) return;

  var catName  = categoryLabels[catKey] || catKey;
  var userName = $userEl.find('option:selected').text();
  // remove "Add new user…" suffix if present visually
  userName = userName.replace(/— Rate:.*$/,'').trim();

  var amount = rate * qty;

  expenses.push({
    catKey: catKey,
    catName: catName,
    userId: Number(usrVal),
    userName: userName,
    rate: rate,
    qty: qty,
    amount: amount
  });

  renderTable();

  // reset quantity only for faster multiple entries
  $qtyEl.val('');

  // ================= (Optional) Persist to server =================
  /*
  $.ajax({
    url: '<?= /* base_url("expenses/store") */ "" ?>',
    method: 'POST',
    contentType: 'application/json',
    data: JSON.stringify({ category: catKey, user_id: Number(usrVal), rate: rate, quantity: qty, amount: amount }),
    success: function(resp){ $statusEl.text('Saved.'); },
    error: function(xhr){ $statusEl.text('Save failed: ' + (xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : xhr.statusText)); }
  });
  */
});

// ========================== MODAL SUBMITS ==========================

// Save Category
$('#formAddCategory').on('submit', function(e){
  e.preventDefault();
  var key   = ($newCatKey.val() || '').trim().toLowerCase().replace(/\s+/g, '_');
  var label = ($newCatLabel.val() || '').trim();

  if (!key || !label) return;
  if (categoryLabels[key]){
    alert('Category key already exists. Choose another.');
    return;
  }

  // Update in-memory
  categoryLabels[key] = label;
  usersByCategory[key] = [];

  // Update dropdown before Add new option
  var $addOpt = $categoryEl.find('option[value="__add_category__"]');
  $('<option>').val(key).text(label).insertBefore($addOpt);
  $categoryEl.val(key).change();

  $modalAddCategory.modal('hide');

  // (Optional) persist category
  /*
  $.post('<?= /* base_url("categories/store") */ "" ?>', { key:key, label:label });
  */
});

// Save User/Vendor
$('#formAddUser').on('submit', function(e){
  e.preventDefault();

  var catKey = $categoryEl.val();
  var name   = ($newUserName.val() || '').trim();
  var $rate  = $('#newUserRate');
  if (!catKey || !name || !$rate.length) return;

  var rate = Number($rate.val());
  if (!(rate >= 0)){
    $rate.addClass('is-invalid');
    return;
  }

  var newUser = { id: __nextId++, name: name, rate: rate };
  usersByCategory[catKey] = usersByCategory[catKey] || [];
  usersByCategory[catKey].push(newUser);

  // Rebuild user dropdown and select the new one
  populateUsers(catKey);
  $userEl.val(String(newUser.id));
  $rateEl.val(String(rate));
  $modalAddUser.modal('hide');

  // (Optional) persist vendor
  /*
  $.ajax({
    url: '<?= /* base_url("vendors/store") */ "" ?>',
    method: 'POST',
    data: { category: catKey, name: name, rate: rate },
    success: function(){}, error: function(){}
  });
  */
});

// ========================== INIT ==========================
renderTable();
</script>

</body>
</html>
