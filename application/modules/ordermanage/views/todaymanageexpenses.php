<?php
// ============================================================================
// expenses.php  (Bootstrap 3.3.7 + jQuery)
//  - Beginner-friendly: code split into helpers (API, UI, State, Render, Validate)
//  - Classic mode: add single expense using rate_id -> rate × qty
//  - Product mode (veg/shop): tab shows multiple product rows (product_id, price, qty)
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
  <link rel="stylesheet" href="<?php echo base_url('application/modules/ordermanage/assets/css/todayexpense.css'); ?>" type="text/css">
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

        <!-- ===== Tabs ===== -->
        <ul class="nav nav-tabs" id="expenseTabs" style="margin-bottom:12px;">
          <li class="active"><a href="#tab-classic" data-toggle="tab">Classic</a></li>
          <li><a href="#tab-products" data-toggle="tab">Products</a></li>
        </ul>

        <div class="tab-content">
          <!-- Classic tab -->
          <div class="tab-pane fade in active" id="tab-classic">
            <!-- Rate & Quantity (classic mode) -->
            <div class="form-group">
              <label for="rate" class="col-sm-2 control-label">Rate</label>
              <div class="col-sm-2" id="fg-rate">
                <input type="number" disabled step="0.1" id="rate" name="rate" class="form-control" placeholder="0.00">
                <span class="help-block" id="rateHint"></span>
                <div class="error-text" id="err-rate">Enter a valid rate (≥ 0).</div>
              </div>

              <label for="quantity" class="col-sm-2 control-label">Quantity</label>
              <div class="col-sm-2" id="fg-qty">
                <input type="number" step="0.1" id="quantity" name="quantity" class="form-control" placeholder="1.00" value="1">
                <span class="help-block" id="qtyHint">Enter how many units/days/liters.</span>
                <div class="error-text" id="err-qty">Enter a valid quantity (> 0).</div>
              </div>

              <div class="col-sm-4">
                <button type="submit" id="btnAddExpense" class="btn btn-primary btn-wide" disabled>
                  <span class="glyphicon glyphicon-plus"></span> Add Expense
                </button>
              </div>
            </div>
          </div>

          <!-- Products tab -->
          <div class="tab-pane fade" id="tab-products">
            <!-- Product Mode (Products UI) -->
            <div id="productMode" class="form-group">
              <label class="col-sm-2 control-label">Products</label>
              <div class="col-sm-10">
                <!-- Quick Actions -->
                <div class="quick-actions">
                  <button type="button" id="btnQuickAddCommon" class="btn btn-info btn-sm">
                    <span class="glyphicon glyphicon-flash"></span> Quick Add Common
                  </button>
                  <button type="button" id="btnClearAll" class="btn btn-warning btn-sm">
                    <span class="glyphicon glyphicon-trash"></span> Clear All
                  </button>
                  <button type="button" id="btnDuplicateLast" class="btn btn-default btn-sm">
                    <span class="glyphicon glyphicon-copy"></span> Duplicate Last
                  </button>
                  <button type="button" id="btnManageProducts" class="btn btn-default btn-sm">
                    <span class="glyphicon glyphicon-cog"></span> Manage Products
                  </button>
                  <span class="text-muted" style="margin-left: 15px;">
                    <small>💡 Tip: Use Ctrl+N for new row, Ctrl+S to save</small>
                  </span>
                </div>
                <div class="table-responsive">
                  <table class="table table-bordered" id="productsTable" style="margin-bottom:8px;">
                    <thead>
                      <tr>
                        <th style="width:35%;">Product Name</th>
                        <th class="text-right" style="width:15%;">Price</th>
                        <th class="text-right" style="width:15%;">Qty</th>
                        <th class="text-right" style="width:25%;">Total</th>
                        <th style="width:10%;">Action</th>
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
                <!-- (moved) err-products lives below the Save button -->
              </div>
            </div>

            <!-- Products Save Button + Validation message -->
            <div class="form-group">
              <div class="col-sm-2"></div>
              <div class="col-sm-10">
                <button type="button" class="btn btn-primary btn-wide" id="btnAddExpenseProducts">
                  <span class="glyphicon glyphicon-plus"></span> Save Products
                </button>
                <div class="error-text" id="err-products" style="display:none; margin-top:6px;">
                  Add at least one valid product (select product, price &gt;= 0, qty &gt; 0).
                </div>
                <p class="help-block" style="margin-top:6px;">
                  Each row will be saved as its own expense entry for this vendor.
                </p>
              </div>
            </div>
          </div>
        </div>
        <!-- /tab-content -->

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


<!-- Manage Products Modal -->
<div class="modal fade" id="modalManageProducts" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="formManageProducts">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Manage Products</h4>
        </div>
        <div class="modal-body">
          <!-- Selected Entity Info -->
          <div class="form-group">
            <label>Entity</label>
            <input type="text" class="form-control" id="modalEntityName" readonly>
          </div>

          <!-- Existing Products Table -->
          <div class="table-responsive">
            <table class="table table-bordered" id="existingProductsTable">
              <thead>
                <tr>
                  <th>Product Name</th>
                  <th>Price</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="existingProducts"></tbody>
            </table>
          </div>

          <!-- Add New Product -->
          <hr>
          <div class="form-group" id="fg-newproduct-name">
            <label>Product Name</label>
            <input type="text" class="form-control" id="newProductName" placeholder="Enter product name" required>
            <div class="error-text" id="err-newproduct-name">Please enter a product name.</div>
          </div>

          <div class="form-group" id="fg-newproduct-price">
            <label>Product Price</label>
            <input type="number" class="form-control" id="newProductPrice" step="0.01" placeholder="Enter product price" required>
            <div class="error-text" id="err-newproduct-price">Please enter a valid price.</div>
          </div>
                
          <div class="form-group" id="fg-newproduct-unit">
            <label>Product Unit</label>
            <input type="text" class="form-control" id="newProductUnit" placeholder="Enter product unit" required>
            <div class="error-text" id="err-newproduct-unit">Please enter a valid unit.</div>
          </div>
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


<!-- Confirm Add Expense -->
<div class="modal fade" id="modalConfirmAdd" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-md">
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
<script src="<?php echo base_url('application/modules/ordermanage/assets/js/todayexpense.js'); ?>" type="text/javascript"></script>
</body>
</html>
