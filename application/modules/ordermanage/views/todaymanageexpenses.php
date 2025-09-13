<?php
// ============================================================================
// expenses.php  (Bootstrap 3.3.7 + jQuery)
//  - Beginner-friendly: code split into helpers (API, UI, State, Render, Validate)
//  - Classic mode: add single expense using rate_id -> rate × qty
//  - Product mode (veg/shop): add multiple product rows (product_id, price, qty)
//  - Product Name is a SELECT that loads via API: getProductsByEntity?entity_id=...
//  - Endpoints used (GET):
//      addexpense, addcategory, getcategories, addCategoryEntity, getCategoryEntities,
//      get_expenses, updateexpense, deleteexpense, getProductsByEntity?entity_id=...
//  - Print Report groups by Category and shows Grand Total
//  - No FK constraints required on DB side
// ============================================================================
?>
<!DOCTYPE html>
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
    .error-text { color:#d9534f; font-size:12px; margin-top:5px; display:none; }
    .table thead tr th { background:#f0f7ff; }
    .table tfoot tr th { background:#fafafa; }
    .text-right { text-align:right; }
    .m-t-10 { margin-top:10px; }
    .m-b-0 { margin-bottom:0; }
    .btn-wide { min-width:140px; }
    .spin { animation:spin 1s linear infinite; }
    @keyframes spin { from {transform:rotate(0)} to {transform:rotate(360deg)} }
    /* Filter bar */
    .filter-bar { background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:10px 12px; margin-bottom:12px; box-shadow:0 1px 1px rgba(0,0,0,.03); }
    .filter-bar .form-control { height:34px; }
    .filter-label { font-weight:600; margin-right:6px; }
    .badge-soft { background:#eef6ff; color:#3178c6; border:1px solid #d6e8ff; }

    /* Report styles */
    .report-header { margin-bottom: 15px; }
    .report-meta { color:#666; }
    .category-block { margin-bottom: 22px; page-break-inside: avoid; }
    .category-title { margin:0 0 8px; font-weight:700; }
    .category-subtotal { background:#fafafa; font-weight:700; }
    .grand-total { background:#eef6ff; font-weight:700; }
    .report-table > thead > tr > th { background:#f7fbff; }
    .no-data { text-align:center; color:#999; padding:25px 0; }

    /* Dense / print tweaks */
    #printArea.dense { font-size:11px; line-height:1.15; }
    #printArea.dense .report-header { margin-bottom:6px; }
    #printArea.dense .report-header h3 { font-size:16px; margin:0 0 4px; }
    #printArea.dense .report-meta { font-size:10px; margin:0; }
    #printArea.dense .category-block { margin:6px 0; }
    #printArea.dense .category-title { font-size:12px; margin:0 0 4px; }
    #printArea.dense .table { margin-bottom:6px; table-layout:fixed; }
    #printArea.dense .table > thead > tr > th,
    #printArea.dense .table > tbody > tr > td,
    #printArea.dense .table > tfoot > tr > th,
    #printArea.dense .table > tfoot > tr > td {
      padding:2px 4px;
      line-height:1.1;
      border-width:0.5px;
      vertical-align:middle;
    }

    @media print {
      @page { size: A4 portrait; margin: 8mm; }
      body * { visibility: hidden !important; }
      #printArea, #printArea * { visibility: visible !important; }
      #printArea { position: absolute; left: 0; top: 0; width: 100%; }

      #printArea { font-size:11px; line-height:1.15; }
      #printArea .report-header { margin-bottom:6px; }
      #printArea .report-header h3 { font-size:16px; margin:0 0 4px; }
      #printArea .report-meta { font-size:10px; margin:0; }

      #printArea .category-block { margin:6px 0; page-break-inside: avoid; page-break-after: avoid; }
      #printArea .category-title { font-size:12px; margin:0 0 4px; font-weight:700; }

      #printArea .table {
        margin-bottom:6px;
        border-collapse: collapse !important;
        table-layout: fixed;
        width:100%;
      }
      #printArea .table > thead > tr > th,
      #printArea .table > tbody > tr > td,
      #printArea .table > tfoot > tr > th,
      #printArea .table > tfoot > tr > td {
        padding:2px 4px !important;
        line-height:1.1 !important;
        vertical-align:middle;
        border-width:0.5px !important;
      }
      #printArea .report-table > thead > tr > th,
      #printArea .table thead tr th,
      #printArea .table tfoot tr th { background:#fff !important; }
      #printArea .category-subtotal,
      #printArea .grand-total { background:#fff !important; font-weight:700; }
      #printArea .grand-total td,
      #printArea .category-subtotal td { padding:3px 4px !important; }
      #printArea > table.table { margin-top:6px; }
      #printArea td.text-right { font-variant-numeric: tabular-nums; }
      .no-print { display:none !important; }
    }

    /* Product table input height (small polish) */
    #productsTable input, #productsTable select { height:30px; }
  </style>
</head>
<body>

<div class="container" style="max-width:1100px; padding-top:20px;">

  <!-- ========================= Header ========================= -->
  <div class="page-header" style="margin-top:0;">
    <h3 class="m-b-0">Manage Expenses <small class="text-muted">Beginner-friendly helpers</small></h3>
  </div>

  <!-- ========================= Entry Form ========================= -->
  <div class="panel panel-default">
    <div class="panel-heading">Add Expense</div>
    <div class="panel-body">
      <form id="expenseForm" class="form-horizontal" autocomplete="off">

        <!-- Category -->
        <div class="form-group" id="fg-category">
          <label for="category" class="col-sm-2 control-label">Category</label>
          <div class="col-sm-4">
            <select id="category" name="category" class="form-control" required>
              <option value="">-- Select Category --</option>
              <?php if (!empty($categories)) foreach ($categories as $category): ?>
                <option value="<?php echo htmlspecialchars($category->category_id); ?>">
                  <?php echo htmlspecialchars($category->category_name); ?>
                </option>
              <?php endforeach; ?>
              <option value="__add_category__">➕ Add new category…</option>
            </select>
            <span class="help-block">Choose the type of expense.</span>
            <div class="error-text" id="err-category">Please select a category.</div>
          </div>
        </div>

        <!-- User/Vendor -->
        <div class="form-group" id="fg-user">
          <label for="user" class="col-sm-2 control-label">User / Vendor</label>
          <div class="col-sm-4">
            <select id="user" name="user" class="form-control" required disabled>
              <option value="">-- Select User/Vendor --</option>
            </select>
            <span class="help-block">Select who this expense is for.</span>
            <div class="error-text" id="err-user">Please select a user/vendor.</div>
          </div>
          <div class="col-sm-4">
            <button type="button" id="addUserBtn" class="btn btn-default btn-wide" disabled>
              <span class="glyphicon glyphicon-user"></span> Add User/Vendor
            </button>
          </div>
        </div>

        <!-- Rate & Quantity (classic mode) -->
        <div class="form-group">
          <label for="rate" class="col-sm-2 control-label">Rate</label>
          <div class="col-sm-2" id="fg-rate">
            <input type="number" step="0.01" id="rate" name="rate" class="form-control" placeholder="0.00" readonly>
            <span class="help-block" id="rateHint"></span>
            <div class="error-text" id="err-rate">Enter a valid rate (≥ 0).</div>
          </div>

          <label for="quantity" class="col-sm-2 control-label">Quantity</label>
          <div class="col-sm-2" id="fg-qty">
            <input type="number" step="0.01" id="quantity" name="quantity" class="form-control" placeholder="1.00" value="1">
            <span class="help-block" id="qtyHint">Enter how many units/days/liters.</span>
            <div class="error-text" id="err-qty">Enter a valid quantity (> 0).</div>
          </div>

          <div class="col-sm-4">
            <button type="submit" id="btnAddExpense" class="btn btn-primary btn-wide" disabled>
              <span class="glyphicon glyphicon-plus"></span> Add Expense
            </button>
          </div>
        </div>

        <!-- Product Mode (shown for "veg"/"shop" categories) -->
        <div id="productMode" class="form-group" style="display:none;">
          <label class="col-sm-2 control-label">Products</label>
          <div class="col-sm-10">
            <div class="table-responsive">
              <table class="table table-bordered" id="productsTable" style="margin-bottom:8px;">
                <thead>
                  <tr>
                    <th style="width:38%;">Product Name</th>
                    <th class="text-right" style="width:20%;">Price</th>
                    <th class="text-right" style="width:20%;">Qty</th>
                    <th class="text-right" style="width:20%;">Total</th>
                    <th style="width:2%;"></th>
                  </tr>
                </thead>
                <tbody id="productRows"></tbody>
                <tfoot>
                  <tr>
                    <th colspan="3" class="text-right">Products Subtotal:</th>
                    <th class="text-right" id="productsSubtotal">0.00</th>
                    <th></th>
                  </tr>
                </tfoot>
              </table>
            </div>
            <button type="button" id="btnAddProductRow" class="btn btn-default btn-sm">
              <span class="glyphicon glyphicon-plus"></span> Add product
            </button>
            <div class="error-text" id="err-products" style="display:none; margin-top:6px;">
              Add at least one valid product (select product, price &gt;= 0, qty &gt; 0).
            </div>
            <p class="help-block" style="margin-top:6px;">
              Each row will be saved as its own expense entry for this vendor.
            </p>
          </div>
        </div>

      </form>
      <p id="serverStatus" class="text-muted m-t-10"></p>
    </div>
  </div>

  <!-- ========================= Today Expenses ========================= -->
  <div class="panel panel-default">
    <div class="panel-heading">Today’s Expenses</div>
    <div class="panel-body">
      <!-- Filters -->
      <div class="filter-bar row">
        <div class="col-sm-4">
          <label for="filterCategory" class="filter-label">Category</label>
          <select id="filterCategory" class="form-control">
            <option value="">All categories</option>
          </select>
        </div>
        <div class="col-sm-4">
          <label for="searchInput" class="filter-label">Search</label>
          <input id="searchInput" type="text" class="form-control" placeholder="Search vendor / product...">
        </div>
        <div class="col-sm-4 text-right" style="margin-top:24px;">
          <span id="filterCount" class="badge badge-soft">0 results</span>
          <button type="button" id="clearFilters" class="btn btn-default">Clear</button>
        </div>
      </div>
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
              <th class="text-center" style="width:130px;">Action</th>
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
    </div>
  </div>

  <!-- ========================= Report / Print ========================= -->
  <div class="panel panel-default no-print">
    <div class="panel-heading">Report</div>
    <div class="panel-body">
      <div class="row">
        <div class="col-sm-6">
          <p class="text-muted m-b-0">Generate a printable report grouped by category.</p>
        </div>
        <div class="col-sm-6 text-right">
          <button type="button" id="btnBuildReport" class="btn btn-default">
            <span class="glyphicon glyphicon-eye-open"></span> Preview Report
          </button>
          <button type="button" id="btnPrintReport" class="btn btn-primary">
            <span class="glyphicon glyphicon-print"></span> Print Report
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Printable Area -->
  <div id="printArea" style="background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:18px; margin-bottom:40px;">
    <div class="report-header">
      <h3 class="m-b-0">Expenses Report</h3>
      <p class="report-meta m-b-0">
        Date: <span id="reportDate"></span>
        <span id="reportFilters" style="margin-left:10px;"></span>
      </p>
    </div>
    <div id="reportBody">
      <div class="no-data">No data to display.</div>
    </div>
    <table class="table table-bordered" style="margin-top:15px;">
      <tfoot>
        <tr class="grand-total">
          <td class="text-right"><strong>Grand Total:</strong></td>
          <td style="width:180px;" class="text-right"><strong id="reportGrandTotal">0.00</strong></td>
        </tr>
      </tfoot>
    </table>
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
          <div class="form-group" id="fg-newcat">
            <label>Category Name (label)</label>
            <input type="text" class="form-control" id="newCatLabel" placeholder="e.g., Water" required>
            <div class="error-text" id="err-newcat">Please enter a category name.</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" id="addCategory" class="btn btn-primary btn-wide">
            <span class="glyphicon glyphicon-ok"></span> Save Category
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add User/Vendor -->
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
          <div class="form-group" id="fg-entity-name">
            <label>User/Vendor Name</label>
            <input type="text" class="form-control" id="entity-name" placeholder="e.g., Ali Milk Supplier" required>
            <div class="error-text" id="err-entity-name">Enter a name.</div>
          </div>
          <div class="form-group" id="fg-item-name">
            <label>Item/Name</label>
            <input type="text" class="form-control" id="entity-item-name" placeholder="e.g., Milk" required>
            <div class="error-text" id="err-item-name">Enter an item name.</div>
          </div>
          <div class="form-group" id="fg-item-unit">
            <label>Unit</label>
            <input type="text" class="form-control" id="entity-item-unit" placeholder="e.g., Litre" required>
            <div class="error-text" id="err-item-unit">Enter a unit.</div>
          </div>
          <div class="form-group" id="fg-item-price">
            <label>Price</label>
            <input type="number" step="0.01" class="form-control" id="entity-item-price" placeholder="e.g., 169.00" required>
            <div class="error-text" id="err-item-price">Enter a valid price (≥ 0).</div>
          </div>
          <div id="dynamicRateFields"></div>
          <p class="text-muted" id="dynamicHint"></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="button" id="addUserVendor" class="btn btn-primary btn-wide">
            <span class="glyphicon glyphicon-ok"></span> Save User/Vendor
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Confirm Add Expense -->
<div class="modal fade" id="modalConfirmAdd" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Confirm Add</h4>
      </div>
      <div class="modal-body">
        <p class="m-b-0"><strong>Category:</strong> <span id="confCat"></span></p>
        <p class="m-b-0"><strong>User/Vendor:</strong> <span id="confUser"></span></p>
        <p class="m-b-0"><strong>Rate × Qty / Items:</strong> <span id="confRateQty"></span></p>
        <p class="m-b-0"><strong>Total:</strong> <span id="confTotal"></span></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" id="confirmAddBtn" class="btn btn-primary">
          <span class="glyphicon glyphicon-ok"></span> Confirm
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Expense -->
<div class="modal fade" id="modalEditExpense" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formEditExpense">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Edit Expense</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" id="edit-expense-id">
          <div class="form-group">
            <label>Rate</label>
            <input type="number" step="0.01" class="form-control" id="edit-rate" required>
          </div>
          <div class="form-group">
            <label>Quantity</label>
            <input type="number" step="0.01" class="form-control" id="edit-qty" required>
          </div>
          <p class="text-muted">Total will be recalculated on save.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <span class="glyphicon glyphicon-ok"></span> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Reason -->
<div class="modal fade" id="modalDeleteReason" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formDeleteExpense">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Delete Expense</h4>
        </div>
        <div class="modal-body">
          <input type="hidden" id="delete-expense-id">
          <p><strong>Amount:</strong> <span id="delete-amount"></span></p>
          <div class="form-group" id="fg-del-reason">
            <label>Reason for deletion <small class="text-muted">(required)</small></label>
            <textarea class="form-control" id="delete-reason" rows="3" placeholder="Enter a proper reason…"></textarea>
            <div class="error-text" id="err-del-reason">Please provide a proper reason (at least 5 characters).</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">
            <span class="glyphicon glyphicon-trash"></span> Delete
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- jQuery + Bootstrap JS (remove if already included globally) -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
(function(){
  'use strict';

  // Disable GET caching in older browsers & add global AJAX error logger
  $.ajaxSetup({ cache:false });
  $(document).ajaxError(function(_evt, xhr, settings, err){
    console.error('AJAX ERROR @', settings && settings.url, err, xhr && xhr.responseText);
  });

  // ===========================================================================
  // SECTION 1: STATE
  // ===========================================================================
  var state = {
    expenses: [],
    productMode: false,
    pendingAdd: null,
    productsCacheByEntity: {} // entity_id (string) -> [ {product_id, product_name, purchase_price, sale_price, product_price_id} ]
  };

  // ===========================================================================
  // SECTION 2: ELEMENTS
  // ===========================================================================
  var $categoryEl = $('#category');
  var $userEl     = $('#user');
  var $rateEl     = $('#rate');
  var $rateHintEl = $('#rateHint');
  var $qtyEl      = $('#quantity');
  var $rowsEl     = $('#expenseRows');
  var $totalEl    = $('#grandTotal');
  var $statusEl   = $('#serverStatus');
  var $btnAdd     = $('#btnAddExpense');

  var $modalAddCategory = $('#modalAddCategory');
  var $modalAddEntity   = $('#modalAddEntity');
  var $modalCatName     = $('#modalCatName');

  // Product mode elements
  var $productModeWrap  = $('#productMode');
  var $productRows      = $('#productRows');
  var $productsSubtotal = $('#productsSubtotal');

  // Report elements
  var $btnBuildReport    = $('#btnBuildReport');
  var $btnPrintReport    = $('#btnPrintReport');
  var $printArea         = $('#printArea');
  var $reportBody        = $('#reportBody');
  var $reportDate        = $('#reportDate');
  var $reportGrandTotal  = $('#reportGrandTotal');

  // Filters
  var $filterCategory = $('#filterCategory');
  var $searchInput    = $('#searchInput');
  var $filterCount    = $('#filterCount');
  var $clearFilters   = $('#clearFilters');
  var filters = { category: '', search: '' };

  // Error elements
  var $errCategory = $('#err-category');
  var $errUser     = $('#err-user');
  var $errRate     = $('#err-rate');
  var $errQty      = $('#err-qty');

  // ===========================================================================
  // SECTION 3: GENERIC HELPERS
  // ===========================================================================
  function toMoney(n){ return (Number(n || 0)).toFixed(2); }
  function debounce(fn, wait){ var t; return function(){ var ctx=this, args=arguments; clearTimeout(t); t=setTimeout(function(){ fn.apply(ctx,args); }, wait); }; }
  function todayYmd(){
    var d = new Date();
    var m = (d.getMonth()+1).toString().padStart(2,'0');
    var day = d.getDate().toString().padStart(2,'0');
    return d.getFullYear() + '-' + m + '-' + day;
  }
  function showMsg(text, kind){
    var cls = kind === 'ok' ? 'text-success' : (kind === 'err' ? 'text-danger' : 'text-muted');
    $statusEl.removeClass('text-success text-danger text-muted').addClass(cls).text(text || '');
    if(kind === 'err') console.warn('[STATUS:ERR]', text);
  }
  function setLoading($btn, loading){
    if(!$btn) return;
    if(loading){
      $btn.prop('disabled', true);
      $btn.data('old-html', $btn.html());
      $btn.html('<span class="glyphicon glyphicon-refresh spin"></span> Working...');
    } else {
      $btn.prop('disabled', false);
      if($btn.data('old-html')) $btn.html($btn.data('old-html'));
    }
  }
  function clearInlineErrors(){
    $('.is-invalid').removeClass('is-invalid');
    $('.error-text').hide();
  }

  // ===========================================================================
  // SECTION 4: API HELPERS (all GET as per your backend)
  // ===========================================================================
  function apiGet(url, data){
    return $.ajax({ url: url, type: 'GET', data: data || {}, dataType: 'json' });
  }
  function apiGetCategories(){ return apiGet('getcategories'); }
  function apiGetEntities(category_id){ return apiGet('getCategoryEntities', { category_id: category_id }); }
  function apiGetProducts(entity_id){ return apiGet('getProductsByEntity', { entity_id: entity_id }); }
  function apiAddExpense(payload){ return apiGet('addexpense', payload); }
  function apiGetExpenses(){ return apiGet('get_expenses'); }
  function apiUpdateExpense(p){ return apiGet('updateexpense', p); }
  function apiDeleteExpense(p){ return apiGet('deleteexpense', p); }
  function apiAddCategory(p){ return apiGet('addcategory', p); }
  function apiAddCategoryEntity(p){ return apiGet('addCategoryEntity', p); }

  // ===========================================================================
  // SECTION 5: UI HELPERS (small, focused)
  // ===========================================================================
  function setRateHintByCategoryKey(catKey){
    var t = (catKey||'').toString().toLowerCase();
    var txt = '';
    if (~t.indexOf('employee') || ~t.indexOf('wage'))          txt = 'Rate = daily wages; Quantity = number of days.';
    else if (~t.indexOf('milk'))                                txt = 'Rate = per liter; Quantity = liters.';
    else if (~t.indexOf('gas'))                                 txt = 'Rate = per unit; Quantity = units consumed.';
    else if (~t.indexOf('electricity') || ~t.indexOf('kwh'))    txt = 'Rate = per kWh; Quantity = kWh consumed.';
    else                                                        txt = 'Enter a rate appropriate for this expense type.';
    $rateHintEl.text(txt);
  }
  function isVegShopCategoryLabel(label){
    if(!label) return false;
    var t = String(label).toLowerCase();
    return /(^|\s)(veg|vegetable|vegetables|sabzi|shop|store)(\s|$)/.test(t);
  }
  function setProductMode(on){
    state.productMode = !!on;
    if(state.productMode){
      $('#fg-rate, #fg-qty').hide();
      $productModeWrap.show();
      $rateEl.prop('readonly', true);
      if($productRows.children('tr').length === 0){ addProductRow(); }
      $btnAdd.prop('disabled', false);
    } else {
      $productModeWrap.hide();
      $('#fg-rate, #fg-qty').show();
      $productRows.empty();
      $productsSubtotal.text('0.00');
    }
  }
  function buildOption(value, text, attrs){
    var $o = $('<option>').val(String(value)).text(text);
    if(attrs){ Object.keys(attrs).forEach(function(k){ $o.attr(k, attrs[k]); }); }
    return $o;
  }

  // ===========================================================================
  // SECTION 6: PRODUCTS TABLE HELPERS
  // ===========================================================================
  function normalizeProducts(list){
    return (Array.isArray(list) ? list : []).map(function(p){
      return {
        product_id: p.product_id != null ? String(p.product_id) : '',
        product_name: p.product_name || '',
        product_price_id: p.product_price_id != null ? String(p.product_price_id) : '',
        purchase_price: p.purchase_price != null ? Number(p.purchase_price) : null,
        sale_price: p.sale_price != null ? Number(p.sale_price) : null
      };
    });
  }

  function productRowTemplate(prefill, productsList){
    var rowId = 'pr_' + Date.now() + '_' + Math.floor(Math.random()*1000);
    var price = prefill && prefill.price != null ? prefill.price : '';
    var qty   = prefill && prefill.qty   != null ? prefill.qty   : 1;

    var $tr = $('<tr>').attr('data-rowid', rowId);

    // Product select
    var $sel = $('<select class="form-control ip-product"><option value="">-- Select Product --</option></select>');
    if(Array.isArray(productsList)){
      productsList.forEach(function(p){
        var defPrice = (p.purchase_price != null ? p.purchase_price : (p.sale_price != null ? p.sale_price : null));
        $sel.append(
          buildOption(
            p.product_id,
            p.product_name,
            {
              'data-product_name': p.product_name,
              'data-default_price': defPrice != null ? defPrice : '',
              'data-product_price_id': p.product_price_id || ''
            }
          )
        );
      });
    }

    $tr.append($('<td>').append($sel));
    $tr.append('<td class="text-right"><input type="number" step="0.01" class="form-control ip-price text-right" placeholder="0.00" value="'+ (price===''? '' : Number(price)) +'"></td>');
    $tr.append('<td class="text-right"><input type="number" step="0.01" class="form-control ip-qty text-right" placeholder="1.00" value="'+ Number(qty) +'"></td>');
    $tr.append('<td class="text-right td-line-total">0.00</td>');
    $tr.append('<td class="text-center"><button type="button" class="btn btn-xs btn-danger btn-del-line" title="Remove"><span class="glyphicon glyphicon-remove"></span></button></td>');

    return $tr;
  }

  function addProductRow(prefill){
    var entityId = String($userEl.val() || '');
    var list = state.productsCacheByEntity[entityId] || [];
    var $tr = productRowTemplate(prefill, list);
    $productRows.append($tr);
    recalcProducts();
  }

  function getProductRows(){
    var items = [];
    $productRows.find('tr').each(function(){
      var $r = $(this);
      var productId = $r.find('.ip-product').val();
      var $optSel   = $r.find('.ip-product option:selected');
      var productName = $optSel.data('product_name') || '';
      var productPriceId = $optSel.data('product_price_id') || '';
      var price = Number($r.find('.ip-price').val());
      var qty = Number($r.find('.ip-qty').val());
      items.push({
        product_id: productId ? Number(productId) : null,
        product_price_id: productPriceId ? Number(productPriceId) : null,
        product_name: productName,
        price: price,
        qty: qty,
        total: (productId && price>=0 && qty>0) ? (price*qty) : NaN
      });
    });
    return items;
  }

  function recalcProducts(){
    var sum = 0;
    $productRows.find('tr').each(function(){
      var $r = $(this);
      var price = Number($r.find('.ip-price').val());
      var qty = Number($r.find('.ip-qty').val());
      var total = (price>=0 && qty>0) ? price*qty : 0;
      $r.find('.td-line-total').text(toMoney(total));
      sum += total;
    });
    $productsSubtotal.text(toMoney(sum));
  }

  function validateProducts(){
    var items = getProductRows().filter(function(it){
      return it.product_id && (it.price>=0) && (it.qty>0);
    });
    var ok = items.length>0;
    $('#err-products').toggle(!ok);
    return ok;
  }

  // When product changes, auto-fill price from option data
  $productRows.on('change', '.ip-product', function(){
    var $opt = $(this).find('option:selected');
    var def = Number($opt.data('default_price'));
    var $row = $(this).closest('tr');
    if(!isNaN(def)){ $row.find('.ip-price').val(def); }
    recalcProducts();
  });

  // Product table events
  $('#btnAddProductRow').on('click', function(){ addProductRow(); });
  $productRows
    .on('input', '.ip-price, .ip-qty', recalcProducts)
    .on('click', '.btn-del-line', function(){
      $(this).closest('tr').remove();
      if($productRows.children('tr').length===0){ addProductRow(); }
      recalcProducts();
    });

  // ===========================================================================
  // SECTION 7: DATA FLOW HELPERS (Filters / Apply / Render)
  // ===========================================================================
  function applyFilters(list){
    var cat = (filters.category||'').toString();
    var q = (filters.search||'').toLowerCase();
    return list.filter(function(r){
      var okCat = !cat || String(r.category_id)===cat;
      if(!okCat) return false;
      if(!q) return true;
      var hay = [r.category_name, r.entity_name, (r.product_name||r.item_name||'')].join(' ').toLowerCase();
      return hay.indexOf(q) !== -1;
    });
  }
  function updateFilterCount(n){ $filterCount.text(n + ' results'); }

  // ===========================================================================
  // SECTION 8: RENDER
  // ===========================================================================
  function renderExpenses(){
    $rowsEl.empty();
    var rows = applyFilters(state.expenses);
    var total = 0;
    rows.forEach(function(row, idx){
      total += Number(row.amount)||0;
      var nameBits = (row.entity_name || '-');
      var label = (row.product_name || row.item_name);
      if(label){ nameBits += ' — ' + label; }

      var $tr = $('<tr>');
      $tr.append('<td class="text-center">' + (idx+1) + '</td>');
      $tr.append('<td class="text-center">' + (row.category_name || '-') + '</td>');
      $tr.append('<td class="text-center">' + nameBits + '</td>');
      $tr.append('<td class="text-right">' + toMoney(row.rate) + '</td>');
      $tr.append('<td class="text-right">' + toMoney(row.qty) + '</td>');
      $tr.append('<td class="text-right">' + toMoney(row.amount) + '</td>');
      $tr.append(
        '<td class="text-center">' +
          '<button style="display:none" class="btn btn-xs btn-info btn-edit" data-id="'+ (row.expense_id||'') +'" title="Edit">' +
            '<span class="glyphicon glyphicon-pencil"></span>' +
          '</button> ' +
          '<button class="btn btn-xs btn-danger btn-delete" data-id="'+ (row.expense_id||'') +'" data-amount="'+ toMoney(row.amount) +'" title="Delete">' +
            '<span class="glyphicon glyphicon-trash"></span>' +
          '</button>' +
        '</td>'
      );
      $rowsEl.append($tr);
    });
    $totalEl.text(toMoney(total));
    updateFilterCount(rows.length);
  }

  function groupByCategory(list){
    var map = {};
    list.forEach(function(r){
      var key = r.category_id || 'uncat';
      if(!map[key]) map[key] = { id: key, name: r.category_name || 'Uncategorized', rows: [], subtotal: 0 };
      map[key].rows.push(r);
      map[key].subtotal += (Number(r.amount)||0);
    });
    return Object.keys(map).map(function(k){ return map[k]; })
             .sort(function(a,b){ return (a.name||'').localeCompare(b.name||''); });
  }

  function buildReport(){
    $reportDate.text(todayYmd());
    var bits = [];
    if(filters.category){
      var label = $('#filterCategory option:selected').text() || 'Category';
      bits.push('Category: ' + label);
    }
    if(($searchInput.val()||'').trim()){
      bits.push('Search: ' + $searchInput.val().trim());
    }
    $('#reportFilters').text(bits.length ? '('+bits.join(' | ')+')' : '');

    var rows = applyFilters(state.expenses);
    $reportBody.empty();

    if(!rows.length){
      $reportBody.append('<div class="no-data">No data to display.</div>');
      $reportGrandTotal.text('0.00');
      return;
    }

    var grouped = groupByCategory(rows);
    var grand = 0;

    grouped.forEach(function(cat){
      grand += cat.subtotal;
      var $block = $('<div class="category-block">');
      $block.append('<h4 class="category-title">'+ (cat.name || 'Uncategorized') +'</h4>');

      var $table = $('<table class="table table-bordered report-table">');
      $table.append(
        '<thead><tr>' +
          '<th style="width:50%;">Name</th>' +
          '<th class="text-right" style="width:16%;">Rate</th>' +
          '<th class="text-right" style="width:16%;">Qty</th>' +
          '<th class="text-right" style="width:18%;">Total</th>' +
        '</tr></thead>'
      );

      var $tbody = $('<tbody>');
      cat.rows.forEach(function(r){
        var nm = (r.entity_name || '-') + (r.product_name ? ' — ' + r.product_name : (r.item_name ? ' — ' + r.item_name : ''));
        $tbody.append(
          '<tr>' +
            '<td>'+ nm +'</td>' +
            '<td class="text-right">'+ toMoney(r.rate) +'</td>' +
            '<td class="text-right">'+ toMoney(r.qty) +'</td>' +
            '<td class="text-right">'+ toMoney(r.amount) +'</td>' +
          '</tr>'
        );
      });
      $table.append($tbody);
      $table.append(
        '<tfoot><tr class="category-subtotal">' +
          '<td class="text-right" colspan="3">Subtotal ('+ (cat.name || '-') +'):</td>' +
          '<td class="text-right"><strong>'+ toMoney(cat.subtotal) +'</strong></td>' +
        '</tr></tfoot>'
      );
      $block.append($table);
      $reportBody.append($block);
    });

    $reportGrandTotal.text(toMoney(grand));
  }

  // ===========================================================================
  // SECTION 9: LOADERS (Categories, Entities, Products, Expenses)
  // ===========================================================================
  function loadCategories(selectId){
    return apiGetCategories().done(function(categories){
      $categoryEl.empty().append('<option value="">-- Select Category --</option>');
      $filterCategory.empty().append('<option value="">All categories</option>');
      if (Array.isArray(categories)) {
        $.each(categories, function(_, c){
          var opt = buildOption(c.category_id, c.category_name);
          $categoryEl.append(opt.clone());
          $filterCategory.append(opt);
        });
      }
      $categoryEl.append('<option value="__add_category__">➕ Add new category…</option>');
      if(selectId){ $categoryEl.val(String(selectId)).change(); }
    }).fail(function(){ showMsg('Failed to reload categories.', 'err'); });
  }

  function loadEntities(category_id){
    return apiGetEntities(category_id).done(function(resp){
      $('#addUserBtn').prop('disabled', false);
      $userEl.empty().append('<option value="">-- Select User/Vendor --</option>').prop('disabled', false);
      if (Array.isArray(resp)) {
        resp.forEach(function(u){
          $userEl.append(
            buildOption(
              String(u.entity_id),
              u.entity_name + (u.price ? ' — Rate: ' + toMoney(u.price) : ''),
              {
                'data-item_name': u.item_name || '',
                'data-unit': u.unit || '',
                'data-rate_id': u.rate_id || '',
                'data-price': (u.price !== undefined ? u.price : '')
              }
            )
          );
        });
        $userEl.append('<option value="__add_user__">➕ Add new user/vendor…</option>');
      }
    }).fail(function(){ showMsg('Failed to load users/vendors for this category.', 'err'); });
  }

  function loadProductsForEntity(entity_id){
    var key = String(entity_id||'');
    if(!key){ return $.Deferred().resolve([]); }
    if(state.productsCacheByEntity[key]){
      refreshAllProductSelects(key);
      return $.Deferred().resolve(state.productsCacheByEntity[key]);
    }
    return apiGetProducts(key).done(function(list){
      var safe = normalizeProducts(list);
      state.productsCacheByEntity[key] = safe;
      refreshAllProductSelects(key);
    }).fail(function(){
      showMsg('Failed to load products for this vendor.', 'err');
      state.productsCacheByEntity[key] = [];
      refreshAllProductSelects(key);
    });
  }

  function refreshAllProductSelects(entity_id){
    // Rebuild each product select with the entity’s product list
    var list = state.productsCacheByEntity[String(entity_id)] || [];
    $productRows.find('tr').each(function(){
      var $r = $(this);
      var currentVal = $r.find('.ip-product').val() || '';
      var $sel = $('<select class="form-control ip-product"><option value="">-- Select Product --</option></select>');
      list.forEach(function(p){
        var defPrice = (p.purchase_price != null ? p.purchase_price : (p.sale_price != null ? p.sale_price : null));
        var $opt = buildOption(p.product_id, p.product_name, {
          'data-product_name': p.product_name,
          'data-default_price': defPrice != null ? defPrice : '',
          'data-product_price_id': p.product_price_id || ''
        });
        $sel.append($opt);
      });
      $r.find('.ip-product').replaceWith($sel);
      if(currentVal){ $sel.val(currentVal); }
    });
  }

  function loadTodayExpenses(){
    return apiGetExpenses().done(function(resp){
      if (Array.isArray(resp)) {
        state.expenses = resp.map(function(e){
          return {
            expense_id: e.expense_id || e.id || null,
            category_id: e.category_id,
            category_name: e.category_name,
            entity_id: e.entity_id,
            entity_name: e.entity_name,
            product_name: e.product_name || '',  // if backend returns product label
            item_name: e.item_name || '',        // for classic mode rows
            rate_id: e.rate_id || '',
            rate: Number(e.price) || Number(e.rate) || 0,
            qty: Number(e.quantity) || Number(e.qty) || 0,
            amount: Number(e.total_amount) || Number(e.amount) || 0
          };
        });
        // Ensure filter dropdown has items if categories endpoint wasn’t called
        if ($filterCategory.children('option').length <= 1) {
          var seen = {};
          state.expenses.forEach(function(r){ if(!seen[r.category_id]){ seen[r.category_id]=r.category_name; } });
          Object.keys(seen).forEach(function(id){
            $filterCategory.append(buildOption(id, seen[id]));
          });
        }
        renderExpenses();
      }
    }).fail(function(xhr){
      var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
      showMsg('Error loading expenses: ' + msg, 'err');
    });
  }

  // ===========================================================================
  // SECTION 10: VALIDATION
  // ===========================================================================
  function validateForm(){
    clearInlineErrors();
    var ok = true;
    var catVal = $categoryEl.val();
    var userVal = $userEl.val();
    var rate = Number($rateEl.val());
    var qty  = Number($qtyEl.val() || 1);

    if (!catVal){ $categoryEl.addClass('is-invalid'); $errCategory.show(); ok = false; }
    if (!userVal){ $userEl.addClass('is-invalid'); $errUser.show(); ok = false; }

    if(state.productMode){
      var ok2 = (!!catVal && !!userVal && validateProducts());
      $btnAdd.prop('disabled', !ok2);
      return ok2;
    }

    if (!(rate >= 0)) { $rateEl.addClass('is-invalid'); $errRate.show(); ok = false; }
    if (!(qty > 0))   { $qtyEl.addClass('is-invalid'); $errQty.show(); ok = false; }

    $btnAdd.prop('disabled', !ok);
    return ok;
  }

  // ===========================================================================
  // SECTION 11: EVENT HANDLERS
  // ===========================================================================
  // Category change
  $categoryEl.on('change', function(){
    var val = $(this).val();
    var catName = $(this).find('option:selected').text();
    $modalCatName.val(catName);

    // Toggle product mode from category label (veg/shop)
    var catLabel = catName || '';
    setProductMode(isVegShopCategoryLabel(catLabel));

    if(val && val !== '__add_category__'){
      loadEntities(val);
      $('#addUserBtn').prop('disabled', false);
    } else if(val === '__add_category__') {
      $modalAddCategory.modal('show');
      $categoryEl.val('');
    } else {
      $userEl.prop('disabled', true).empty().append('<option value="">-- Select User/Vendor --</option>');
      setRateHintByCategoryKey(catLabel);
    }
    validateForm();
  });

  // User change
  $userEl.on('change', function(){
    var val = $(this).val();
    var $opt = $(this).find('option:selected');
    var itemName = $opt.data('item_name') || '';
    var unit = $opt.data('unit') || '';
    var price = $opt.data('price');

    if (!state.productMode) {
      if (price !== undefined && price !== '') { $rateEl.val(price); } else { $rateEl.val(''); }
      if (unit) $qtyEl.attr('placeholder', unit); else $qtyEl.attr('placeholder', '1.00');
      if (itemName) $rateHintEl.text('Item: ' + itemName + (unit ? ' (' + unit + ')' : '')); else setRateHintByCategoryKey(($categoryEl.find('option:selected').text()||''));
    }

    if (val === '__add_user__') {
      $modalAddEntity.modal('show');
      $userEl.val('');
    } else {
      // Load products for this entity when in product mode
      if(state.productMode && val){
        loadProductsForEntity(val);
      }
    }
    validateForm();
  });

  // Add Category Save
  $('#addCategory').on('click', function(e){
    e.preventDefault();
    var $btn = $(this);
    var label = $('#newCatLabel').val().trim();
    if (!label) { $('#fg-newcat input').addClass('is-invalid'); $('#err-newcat').show(); return; }

    setLoading($btn, true);
    apiAddCategory({ category_name: label })
      .done(function(resp){
        if (resp && resp.success) {
          loadCategories(resp.new_id);
          $('#modalAddCategory').modal('hide');
          $('#newCatLabel').val('');
          showMsg('Category added.', 'ok');
        } else {
          showMsg((resp && resp.message) || 'Failed to add category.', 'err');
        }
      })
      .fail(function(xhr){
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
        showMsg('Error: ' + msg, 'err');
      })
      .always(function(){ setLoading($btn, false); });
  });

  // Save User/Vendor
  $('#addUserVendor').on('click', function(e){
    e.preventDefault();
    var $btn = $(this);
    var catId = $categoryEl.val();
    var name   = ($('#entity-name').val() || '').trim();
    var item_name = ($('#entity-item-name').val() || '').trim();
    var unit  = ($('#entity-item-unit').val() || '').trim();
    var price = Number($('#entity-item-price').val());

    clearInlineErrors();
    var ok = true;
    if(!catId){ $categoryEl.addClass('is-invalid'); $errCategory.show(); ok=false; }
    if(!name){ $('#entity-name').addClass('is-invalid'); $('#err-entity-name').show(); ok=false; }
    if(!item_name){ $('#entity-item-name').addClass('is-invalid'); $('#err-item-name').show(); ok=false; }
    if(!unit){ $('#entity-item-unit').addClass('is-invalid'); $('#err-item-unit').show(); ok=false; }
    if(!(price >= 0)){ $('#entity-item-price').addClass('is-invalid'); $('#err-item-price').show(); ok=false; }
    if(!ok) return;

    setLoading($btn, true);
    apiAddCategoryEntity({ category_id: catId, name: name, item_name: item_name, unit: unit, price: price })
      .done(function(resp){
        if (resp && resp.success) {
          loadEntities(catId);
          $('#modalAddEntity').modal('hide');
          $('#formAddEntity')[0].reset();
          showMsg('User/Vendor added.', 'ok');
        } else {
          showMsg((resp && resp.message) || 'Failed to add user/vendor.', 'err');
        }
      })
      .fail(function(xhr){
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
        showMsg('Error: ' + msg, 'err');
      })
      .always(function(){ setLoading($btn, false); });
  });

  // Add Expense (form submit) -> open confirm modal
  $('#expenseForm').on('submit', function(e){
    e.preventDefault();
    if(!validateForm()) return;

    var catId  = $categoryEl.val();
    var catName  = $categoryEl.find('option:selected').text().trim();
    var userId = $userEl.val();
    var $opt   = $userEl.find('option:selected');
    var userName = $opt.text().replace(/— Rate:.*/, '').trim();
    var rate    = Number($rateEl.val());
    var qty     = Number($qtyEl.val() || 1);
    var rate_id = $opt.data('rate_id') || '';

    if(state.productMode){
      if(!validateProducts()) return;

      var items = getProductRows().filter(function(it){
        return it.product_id && (it.price>=0) && (it.qty>0);
      });
      var batchTotal = items.reduce(function(s,it){ return s + (it.price*it.qty); }, 0);

      state.pendingAdd = {
        mode: 'products',
        category_id: catId,
        catName: catName,
        entity_id: Number(userId),
        userName: userName,
        items: items.map(function(it){
          return {
            product_id: it.product_id,
            product_price_id: it.product_price_id, // optional, if backend expects price record id
            product_name: it.product_name, // just for local display/log
            qty: it.qty,
            rate: it.price,
            amount: it.price * it.qty,
            expense_date: todayYmd(),
            rate_id: 0 // your DB requires NOT NULL; backend can ignore this in product mode
          };
        }),
        grand: batchTotal
      };

      $('#confCat').text(catName);
      $('#confUser').text(userName);
      $('#confRateQty').text(items.length + ' product(s)');
      $('#confTotal').text(toMoney(batchTotal));
      $('#modalConfirmAdd').modal('show');

    } else {
      // Classic mode: do NOT hard-block if rate_id is missing; send what we have
      state.pendingAdd = {
        mode: 'single',
        category_id: catId,
        entity_id: Number(userId),
        item_id: rate_id || null,
        qty: qty,
        rate: rate,
        amount: rate * qty,
        expense_date: todayYmd(),
        catName: catName,
        userName: userName
      };

      $('#confCat').text(catName);
      $('#confUser').text(userName);
      $('#confRateQty').text(toMoney(rate) + ' × ' + toMoney(qty));
      $('#confTotal').text(toMoney(rate * qty));
      $('#modalConfirmAdd').modal('show');
    }
  });

  // Confirm Add -> perform request(s)
  $('#confirmAddBtn').on('click', function(){
    if(!state.pendingAdd) return;
    setLoading($btnAdd, true);
    $('#modalConfirmAdd').modal('hide');

    function sendAdd(p){ return apiAddExpense(p); }

    if(state.pendingAdd.mode === 'products'){
      var reqs = state.pendingAdd.items.map(function(it){
        return sendAdd({
          category_id: state.pendingAdd.category_id,
          entity_id: state.pendingAdd.entity_id,
          product_id: it.product_id,
          product_price_id: it.product_price_id, // include if backend uses it
          rate_id: it.rate_id, // 0 (ignored by backend in product mode)
          rate: it.rate,
          qty: it.qty,
          amount: it.amount,
          expense_date: it.expense_date
        });
      });

      $.when.apply($, reqs)
        .done(function(){
          showMsg('Products added successfully.', 'ok');
          $productRows.empty();
          addProductRow();
          recalcProducts();
          loadTodayExpenses();
        })
        .fail(function(xhr){
          var msg = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (xhr && xhr.statusText) || 'Failed.';
          showMsg('Error adding products: ' + msg, 'err');
        })
        .always(function(){
          setLoading($btnAdd, false);
          state.pendingAdd = null;
        });

    } else {
      var payload = {
        category_id: state.pendingAdd.category_id,
        entity_id: state.pendingAdd.entity_id,
        rate_id: state.pendingAdd.item_id, // may be null; let backend infer item if needed
        rate: state.pendingAdd.rate,
        qty: state.pendingAdd.qty,
        amount: state.pendingAdd.amount,
        expense_date: state.pendingAdd.expense_date
      };
      sendAdd(payload)
        .done(function(resp){
          if(resp && resp.success){
            showMsg('Expense added successfully.', 'ok');
            $qtyEl.val('1');
            validateForm();
            loadTodayExpenses();
          } else {
            showMsg((resp && resp.message) || 'Failed to add expense.', 'err');
          }
        })
        .fail(function(xhr){
          var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
          showMsg('Error: ' + msg, 'err');
        })
        .always(function(){
          setLoading($btnAdd, false);
          state.pendingAdd = null;
        });
    }
  });

  // Edit/Delete actions (same as your original, with a small guard)
  $('#expenseRows').on('click', '.btn-edit', function(){
    var id = $(this).data('id');
    var row = state.expenses.find(function(r){ return String(r.expense_id||'') === String(id); });
    if(!row){ showMsg('Row not found.', 'err'); return; }
    $('#edit-expense-id').val(id);
    $('#edit-rate').val(row.rate);
    $('#edit-qty').val(row.qty);
    $('#modalEditExpense').modal('show');
  });

  $('#formEditExpense').on('submit', function(e){
    e.preventDefault();
    var id = $('#edit-expense-id').val();
    var rate = Number($('#edit-rate').val());
    var qty  = Number($('#edit-qty').val());
    if(!(rate>=0) || !(qty>0)){ showMsg('Enter valid rate and quantity.', 'err'); return; }

    apiUpdateExpense({ expense_id: id, rate: rate, qty: qty, amount: rate*qty })
      .done(function(resp){
        if(resp && resp.success){
          showMsg('Expense updated.', 'ok');
          $('#modalEditExpense').modal('hide');
          loadTodayExpenses();
        } else {
          showMsg((resp && resp.message) || 'Failed to update expense.', 'err');
        }
      })
      .fail(function(xhr){
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
        showMsg('Error: ' + msg, 'err');
      });
  });

  $('#expenseRows').on('click', '.btn-delete', function(){
    var id = $(this).data('id');
    if(!id){ showMsg('Missing expense ID.', 'err'); return; }
    var amount = $(this).data('amount');
    $('#delete-expense-id').val(id);
    $('#delete-amount').text(amount);
    $('#delete-reason').val('');
    $('#delete-reason').removeClass('is-invalid');
    $('#err-del-reason').hide();
    $('#modalDeleteReason').modal('show');
  });

  $('#formDeleteExpense').on('submit', function(e){
    e.preventDefault();
    var id = $('#delete-expense-id').val();
    var reason = ($('#delete-reason').val()||'').trim();
    if(reason.replace(/\s+/g,'').length < 5){
      $('#delete-reason').addClass('is-invalid');
      $('#err-del-reason').show();
      return;
    }
    apiDeleteExpense({ expense_id: id, reason: reason })
      .done(function(resp){
        if(resp && resp.success){
          showMsg('Expense deleted.', 'ok');
          $('#modalDeleteReason').modal('hide');
          loadTodayExpenses();
        } else {
          showMsg((resp && resp.message) || 'Failed to delete expense.', 'err');
        }
      })
      .fail(function(xhr){
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
        showMsg('Error: ' + msg, 'err');
      });
  });

  // Filters
  $filterCategory.on('change', function(){ filters.category = $(this).val(); renderExpenses(); });
  $searchInput.on('input', debounce(function(){ filters.search = $(this).val(); renderExpenses(); }, 150));
  $clearFilters.on('click', function(){ filters = {category:'', search:''}; $filterCategory.val(''); $searchInput.val(''); renderExpenses(); });

  // Report buttons
  $btnBuildReport.on('click', function(){
    buildReport();
    $('#printArea').addClass('dense');
    $('html, body').animate({ scrollTop: $printArea.offset().top - 10 }, 200);
  });
  $btnPrintReport.on('click', function(){
    buildReport();
    $('#printArea').addClass('dense');
    window.print();
  });

  // ===========================================================================
  // SECTION 12: INIT
  // ===========================================================================
  <?php if (empty($categories)): ?>
  loadCategories();
  <?php else: ?>
  (function copyServerCatsToFilter(){
    $filterCategory.empty().append('<option value="">All categories</option>');
    $('#category option').each(function(){
      var v = $(this).val(); var t = $(this).text();
      if(v && v !== '__add_category__') $filterCategory.append(buildOption(v, t));
    });
  })();
  <?php endif; ?>

  loadTodayExpenses();
  validateForm();

})();
</script>

</body>
</html>
