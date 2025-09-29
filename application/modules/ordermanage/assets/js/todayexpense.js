// expenses.js
// ============================================================================
// Dependencies: jQuery 1.12+ and Bootstrap 3 (for tabs + modals)
// This script powers the Expenses screen with Classic vs. Products tabs.
// - Classic: add single expense (rate × qty)
// - Products: add multiple product rows (each saved as its own expense)
// - Loads categories, entities, and products via GET endpoints.
// Endpoints used (GET):
//   addexpense, addcategory, getcategories, addCategoryEntity, getCategoryEntities,
//   get_expenses, updateexpense, deleteexpense, getProductsByEntity?entity_id=...
// ============================================================================

(function () {
  'use strict';

  // Disable GET caching in older browsers & add global AJAX error logger
  $.ajaxSetup({ cache: false });
  $(document).ajaxError(function (_evt, xhr, settings, err) {
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
  var $userEl = $('#user');
  var $rateEl = $('#rate');
  var $rateHintEl = $('#rateHint');
  var $qtyEl = $('#quantity');
  var $rowsEl = $('#expenseRows');
  var $totalEl = $('#grandTotal');
  var $statusEl = $('#serverStatus');
  var $btnAdd = $('#btnAddExpense');

  // Tabs
  var $tabs = $('#expenseTabs');
  var $tabClassic = $('#tab-classic');
  var $tabProducts = $('#tab-products');

  // Product mode elements
  var $productRows = $('#productRows');
  var $productsSubtotal = $('#productsSubtotal');

  // Report elements
  var $btnBuildReport = $('#btnBuildReport');
  var $btnPrintReport = $('#btnPrintReport');
  var $printArea = $('#printArea');
  var $reportBody = $('#reportBody');
  var $reportDate = $('#reportDate');
  var $reportGrandTotal = $('#reportGrandTotal');

  // Filters
  var $filterCategory = $('#filterCategory');
  var $searchInput = $('#searchInput');
  var $filterCount = $('#filterCount');
  var $clearFilters = $('#clearFilters');
  var filters = { category: '', search: '' };

  // Error elements
  var $errCategory = $('#err-category');
  var $errUser = $('#err-user');
  var $errRate = $('#err-rate');
  var $errQty = $('#err-qty');

  // ===========================================================================
  // SECTION 3: GENERIC HELPERS
  // ===========================================================================
  function toMoney(n) { return (Number(n || 0)).toFixed(2); }
  function debounce(fn, wait) { var t; return function () { var ctx = this, args = arguments; clearTimeout(t); t = setTimeout(function () { fn.apply(ctx, args); }, wait); }; }
  function todayYmd() {
    var d = new Date();
    var m = (d.getMonth() + 1).toString().padStart(2, '0');
    var day = d.getDate().toString().padStart(2, '0');
    return d.getFullYear() + '-' + m + '-' + day;
  }
  function showMsg(text, kind) {
    var cls = kind === 'ok' ? 'text-success' : (kind === 'err' ? 'text-danger' : 'text-muted');
    $statusEl.removeClass('text-success text-danger text-muted').addClass(cls).text(text || '');
    if (kind === 'err') console.warn('[STATUS:ERR]', text);
  }
  function setLoading($btn, loading) {
    if (!$btn) return;
    if (loading) {
      $btn.prop('disabled', true);
      $btn.data('old-html', $btn.html());
      $btn.html('<span class="glyphicon glyphicon-refresh spin"></span> Working...');
    } else {
      $btn.prop('disabled', false);
      if ($btn.data('old-html')) $btn.html($btn.data('old-html'));
    }
  }
  function clearInlineErrors() {
    $('.is-invalid').removeClass('is-invalid');
    $('.error-text').hide();
  }
  function syncProductsButtonDisabled(disabled) {
    $('#btnAddExpenseProducts').prop('disabled', !!disabled);
  }
  function buildOption(value, text, attrs) {
    var $o = $('<option>').val(String(value)).text(text);
    if (attrs) { Object.keys(attrs).forEach(function (k) { $o.attr(k, attrs[k]); }); }
    return $o;
  }

  // ===========================================================================
  // SECTION 4: API HELPERS (all GET as per your backend)
  // ===========================================================================
  function apiGet(url, data) {
    return $.ajax({ url: url, type: 'GET', data: data || {}, dataType: 'json' });
  }
  function apiGetCategories() { return apiGet('getcategories'); }
  function apiGetEntities(category_id) { return apiGet('getCategoryEntities', { category_id: category_id }); }
  function apiGetProducts(entity_id) { return apiGet('getProductsByEntity', { entity_id: entity_id }); }
  function apiAddExpense(payload) { return apiGet('addexpense', payload); }
  function apiGetExpenses() { return apiGet('get_expenses'); }
  function apiUpdateExpense(p) { return apiGet('updateexpense', p); }
  function apiDeleteExpense(p) { return apiGet('deleteexpense', p); }
  function apiAddCategory(p) { return apiGet('addcategory', p); }
  function apiAddCategoryEntity(p) { return apiGet('addCategoryEntity', p); }

  // ===========================================================================
  // SECTION 5: UI HELPERS (small, focused)
  // ===========================================================================
  function setRateHintByCategoryKey(catKey) {
    var t = (catKey || '').toString().toLowerCase();
    var txt = '';
    if (~t.indexOf('employee') || ~t.indexOf('wage')) txt = 'Rate = daily wages; Quantity = number of days.';
    else if (~t.indexOf('milk')) txt = 'Rate = per liter; Quantity = liters.';
    else if (~t.indexOf('gas')) txt = 'Rate = per unit; Quantity = units consumed.';
    else if (~t.indexOf('electricity') || ~t.indexOf('kwh')) txt = 'Rate = per kWh; Quantity = kWh consumed.';
    else txt = 'Enter a rate appropriate for this expense type.';
    $rateHintEl.text(txt);
  }
  function isVegShopCategoryLabel(label) {
    if (!label) return false;
    var t = String(label).toLowerCase();
    return /(^|\s)(veg|vegetable|vegetables|sabzi|shop|store)(\s|$)/.test(t);
  }
  function setProductMode(on) {
    state.productMode = !!on;

    if (state.productMode) {
      $tabs.find('a[href="#tab-products"]').tab('show');
      if ($productRows.children('tr').length === 0) { addProductRow(); }
      $rateEl.prop('readonly', true); // harmless when classic tab hidden
    } else {
      $tabs.find('a[href="#tab-classic"]').tab('show');
    }

    recalcProducts();
    validateForm();
  }

  // ===========================================================================
  // SECTION 6: PRODUCTS TABLE HELPERS
  // ===========================================================================
  function normalizeProducts(list) {
    return (Array.isArray(list) ? list : []).map(function (p) {
      return {
        product_id: p.product_id != null ? String(p.product_id) : '',
        product_name: p.product_name || '',
        product_price_id: p.product_price_id != null ? String(p.product_price_id)
                          : (p.price_id != null ? String(p.price_id) : ''),
        purchase_price: p.purchase_price != null ? Number(p.purchase_price) : null,
        sale_price: p.sale_price != null ? Number(p.sale_price) : null
      };
    });
  }

  function productRowTemplate(prefill, productsList) {
    var price = prefill && prefill.price != null ? prefill.price : '';
    var qty = prefill && prefill.qty != null ? prefill.qty : 1;

    var $tr = $('<tr class="product-row-enhanced">');

    // Product cell: SELECT only (no search input)
    var $productCell = $('<td>');
    var $visibleSelect = $('<select class="form-control ip-product"><option value="">-- Select Product --</option></select>');
    if (Array.isArray(productsList)) {
      productsList.forEach(function (p) {
        // Default = sale price; fallback to purchase price
        var defSale = (p.sale_price != null ? p.sale_price : (p.purchase_price != null ? p.purchase_price : null));
        $visibleSelect.append(
          buildOption(
            p.product_id,
            p.product_name,
            {
              'data-product_name': p.product_name,
              'data-default_price': defSale != null ? defSale : '',
              'data-sale_price': (p.sale_price != null ? p.sale_price : ''),
              'data-purchase_price': (p.purchase_price != null ? p.purchase_price : ''),
              'data-product_price_id': p.product_price_id || ''
            }
          )
        );
      });
    }
    $productCell.append($visibleSelect);

    // Price Cell (with optional suggestion buttons retained)
    var $priceCell = $('<td class="text-right">');
    var $priceInput = $('<input type="number" disabled step="0.1" class="form-control ip-price text-right" placeholder="0.0" value="' + (price === '' ? '' : Number(price)) + '">');
    // var $priceSuggestions = $('<div class="price-suggestions"></div>');
    // $priceSuggestions.append('<button type="button" class="btn btn-xs btn-default price-suggestion-btn" data-type="suggested">Suggested</button>');
    // $priceSuggestions.append('<button type="button" class="btn btn-xs btn-default price-suggestion-btn" data-type="last">Last Price</button>');
    // $priceCell.append($priceInput).append($priceSuggestions);
    $priceCell.append($priceInput);

    // Quantity Cell
    var $qtyCell = $('<td class="text-right">');
    var $qtyInput = $('<input type="number" step="0.1" class="form-control ip-qty text-right" placeholder="1.00" value="' + Number(qty) + '">');
    $qtyCell.append($qtyInput);

    // Total Cell
    var $totalCell = $('<td class="text-right td-line-total">0.00</td>');

    // Actions Cell
    var $actionCell = $('<td class="text-center">');
    var $removeBtn = $('<button type="button" class="btn btn-xs btn-danger btn-del-line" title="Remove"><span class="glyphicon glyphicon-remove"></span></button>');
    var $duplicateBtn = $('<button type="button" class="btn btn-xs btn-info btn-duplicate" title="Duplicate" style="margin-left:2px;"><span class="glyphicon glyphicon-copy"></span></button>');
    $actionCell.append($removeBtn).append($duplicateBtn);

    $tr.append($productCell, $priceCell, $qtyCell, $totalCell, $actionCell);

    // Add enhanced event handlers
    setupEnhancedRowEvents($tr);

    return $tr;
  }

  function addProductRow(prefill) {
    var entityId = String($userEl.val() || '');
    var list = state.productsCacheByEntity[entityId] || [];
    var $tr = productRowTemplate(prefill, list);
    $productRows.append($tr);
    recalcProducts();
  }

  function setupEnhancedRowEvents($row) {
    var $visibleSelect = $row.find('.ip-product');
    var $priceInput = $row.find('.ip-price');
    var $qtyInput = $row.find('.ip-qty');
    var $priceSuggestions = $row.find('.price-suggestions');

    // Product select -> auto-fill price with SALE price, fallback to PURCHASE, then default
    $visibleSelect.on('change', function () {
      var $opt = $(this).find('option:selected');
      var sale = parseFloat($opt.data('sale_price'));
      var purchase = parseFloat($opt.data('purchase_price'));
      var def = parseFloat($opt.data('default_price'));

      var priceToUse = !isNaN(sale) ? sale : (!isNaN(purchase) ? purchase : (!isNaN(def) ? def : NaN));
      if (!isNaN(priceToUse)) { $priceInput.val(priceToUse); }

      recalcProducts();
    });

    // Price suggestion buttons -> use default (sale preferred) again
    $priceSuggestions.on('click', '.price-suggestion-btn', function () {
      var $opt = $visibleSelect.find('option:selected');
      if ($opt.length && $opt.val()) {
        var sale = parseFloat($opt.data('sale_price'));
        var purchase = parseFloat($opt.data('purchase_price'));
        var defAttr = parseFloat($opt.data('default_price'));
        var suggestedPrice = !isNaN(sale) ? sale : (!isNaN(purchase) ? purchase : (!isNaN(defAttr) ? defAttr : NaN));
        if (!isNaN(suggestedPrice)) {
          $priceInput.val(suggestedPrice);
          recalcProducts();
        }
      }
    });

    // Real-time validation (lightweight here; full validation happens in validateProducts)
    $priceInput.on('input', recalcProducts);
    $qtyInput.on('input', recalcProducts);

    // Duplicate row functionality
    $row.find('.btn-duplicate').on('click', function () {
      var rowData = extractRowData($row);
      addProductRow(rowData);
    });
  }

  function extractRowData($row) {
    return {
      product_id: $row.find('.ip-product').val(),
      product_name: $row.find('.ip-product option:selected').data('product_name') || '',
      price: parseFloat($row.find('.ip-price').val()) || 0,
      qty: parseFloat($row.find('.ip-qty').val()) || 1
    };
  }

  function getProductRows() {
    var items = [];
    $productRows.find('tr').each(function () {
      var $r = $(this);
      var productId = $r.find('.ip-product').val();
      var $optSel = $r.find('.ip-product option:selected');
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
        total: (productId && price >= 0 && qty > 0) ? (price * qty) : NaN
      });
    });
    return items;
  }

  function recalcProducts() {
    var sum = 0;
    $productRows.find('tr').each(function () {
      var $r = $(this);
      var price = Number($r.find('.ip-price').val());
      var qty = Number($r.find('.ip-qty').val());
      var total = (price >= 0 && qty > 0) ? price * qty : 0;
      $r.find('.td-line-total').text(toMoney(total));
      sum += total;
    });
    $productsSubtotal.text(toMoney(sum));
  }

  function validatePriceField($input, $row) {
    var value = parseFloat($input.val());
    clearFieldError($input);

    if (isNaN(value) || value < 0) {
      showFieldError($input, 'Price must be a valid positive number');
      $row.addClass('row-error');
      return false;
    } else {
      $row.removeClass('row-error');
      return true;
    }
  }

  function validateQtyField($input, $row) {
    var value = parseFloat($input.val());
    clearFieldError($input);

    if (isNaN(value) || value <= 0) {
      showFieldError($input, 'Quantity must be greater than 0');
      $row.addClass('row-error');
      return false;
    } else {
      $row.removeClass('row-error');
      return true;
    }
  }

  function showFieldError($input, message) {
    $input.addClass('is-invalid');
    var $error = $input.siblings('.field-error');
    if ($error.length === 0) {
      $error = $('<div class="field-error"></div>');
      $input.after($error);
    }
    $error.text(message).show();
  }

  function clearFieldError($input) {
    $input.removeClass('is-invalid');
    $input.siblings('.field-error').hide();
  }

  function validateProducts() {
    var items = getProductRows().filter(function (it) {
      return it.product_id && (it.price >= 0) && (it.qty > 0);
    });
    var ok = items.length > 0;
    $('#err-products').toggle(!ok);
    return ok;
  }

  // ===========================================================================
  // SECTION 7: DATA FLOW HELPERS (Filters / Apply / Render)
  // ===========================================================================
  function applyFilters(list) {
    var cat = (filters.category || '').toString();
    var q = (filters.search || '').toLowerCase();
    return list.filter(function (r) {
      var okCat = !cat || String(r.category_id) === cat;
      if (!okCat) return false;
      if (!q) return true;
      var hay = [r.category_name, r.entity_name, (r.product_name || r.item_name || '')].join(' ').toLowerCase();
      return hay.indexOf(q) !== -1;
    });
  }
  function updateFilterCount(n) { $filterCount.text(n + ' results'); }

  // ===========================================================================
  // SECTION 8: RENDER
  // ===========================================================================
  function renderExpenses() {
    $rowsEl.empty();
    var rows = applyFilters(state.expenses);
    var total = 0;
    rows.forEach(function (row, idx) {
      total += Number(row.amount) || 0;
      var nameBits = (row.entity_name || '-');
      var label = (row.product_name || row.item_name);
      if (label) { nameBits += ' — ' + label; }

      var $tr = $('<tr>');
      $tr.append('<td class="text-center">' + (idx + 1) + '</td>');
      $tr.append('<td class="text-center">' + (row.category_name || '-') + '</td>');
      $tr.append('<td class="text-center">' + nameBits + '</td>');
      $tr.append('<td class="text-right">' + toMoney(row.rate) + '</td>');
      $tr.append('<td class="text-right">' + toMoney(row.qty) + '</td>');
      $tr.append('<td class="text-right">' + toMoney(row.amount) + '</td>');
      $tr.append(
        '<td class="text-center">' +
        '<button style="display:none" class="btn btn-xs btn-info btn-edit" data-id="' + (row.expense_id || '') + '" title="Edit">' +
        '<span class="glyphicon glyphicon-pencil"></span>' +
        '</button> ' +
        '<button class="btn btn-xs btn-danger btn-delete" data-id="' + (row.expense_id || '') + '" data-amount="' + toMoney(row.amount) + '" title="Delete">' +
        '<span class="glyphicon glyphicon-trash"></span>' +
        '</button>' +
        '</td>'
      );
      $rowsEl.append($tr);
    });
    $totalEl.text(toMoney(total));
    updateFilterCount(rows.length);
  }

  function groupByCategory(list) {
    var map = {};
    list.forEach(function (r) {
      var key = r.category_id || 'uncat';
      if (!map[key]) map[key] = { id: key, name: r.category_name || 'Uncategorized', rows: [], subtotal: 0 };
      map[key].rows.push(r);
      map[key].subtotal += (Number(r.amount) || 0);
    });
    return Object.keys(map).map(function (k) { return map[k]; })
      .sort(function (a, b) { return (a.name || '').localeCompare(b.name || ''); });
  }

  function buildReport() {
    $reportDate.text(todayYmd());
    var bits = [];
    if (filters.category) {
      var label = $('#filterCategory option:selected').text() || 'Category';
      bits.push('Category: ' + label);
    }
    if (($searchInput.val() || '').trim()) {
      bits.push('Search: ' + $searchInput.val().trim());
    }
    $('#reportFilters').text(bits.length ? '(' + bits.join(' | ') + ')' : '');

    var rows = applyFilters(state.expenses);
    $reportBody.empty();

    if (!rows.length) {
      $reportBody.append('<div class="no-data">No data to display.</div>');
      $reportGrandTotal.text('0.00');
      return;
    }

    var grouped = groupByCategory(rows);
    var grand = 0;

    grouped.forEach(function (cat) {
      grand += cat.subtotal;
      var $block = $('<div class="category-block">');
      $block.append('<h4 class="category-title">' + (cat.name || 'Uncategorized') + '</h4>');

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
      cat.rows.forEach(function (r) {
        var nm = (r.entity_name || '-') + (r.product_name ? ' — ' + r.product_name : (r.item_name ? ' — ' + r.item_name : ''));
        $tbody.append(
          '<tr>' +
          '<td>' + nm + '</td>' +
          '<td class="text-right">' + toMoney(r.rate) + '</td>' +
          '<td class="text-right">' + toMoney(r.qty) + '</td>' +
          '<td class="text-right">' + toMoney(r.amount) + '</td>' +
          '</tr>'
        );
      });
      $table.append($tbody);
      $table.append(
        '<tfoot><tr class="category-subtotal">' +
        '<td class="text-right" colspan="3">Subtotal (' + (cat.name || '-') + '):</td>' +
        '<td class="text-right"><strong>' + toMoney(cat.subtotal) + '</strong></td>' +
        '</tr></tfoot>'
      );
      $block.append($table);
      $reportBody.append($block);
    });

    $reportGrandTotal.text(toMoney(grand));
  }

  // When a product is selected, copy its price into the row's ip-price input
$('#productRows').on('change', '.ip-product', function () {
  var $row = $(this).closest('tr');
  var $opt = $(this).find('option:selected');

  // Prefer sale price; fallback to purchase; then default
  var price =
      parseFloat($opt.data('sale_price'));
  if (isNaN(price)) price = parseFloat($opt.data('purchase_price'));
  if (isNaN(price)) price = parseFloat($opt.data('default_price'));

  if (!isNaN(price)) {
    $row.find('.ip-price').val(price).trigger('input'); // update totals if you listen to input
  }

  // Ensure quantity is at least 1 by default
  var $qty = $row.find('.ip-qty');
  if (!$qty.val()) $qty.val(1).trigger('input');
});


  // ===========================================================================
  // SECTION 9: LOADERS (Categories, Entities, Products, Expenses)
  // ===========================================================================
  function loadCategories(selectId) {
    return apiGetCategories().done(function (categories) {
      $categoryEl.empty().append('<option value="">-- Select Category --</option>');
      $filterCategory.empty().append('<option value="">All categories</option>');
      if (Array.isArray(categories)) {
        $.each(categories, function (_idx, c) {
          var opt = buildOption(c.category_id, c.category_name);
          $categoryEl.append(opt.clone());
          $filterCategory.append(opt);
        });
      }
      $categoryEl.append('<option value="__add_category__">➕ Add new category…</option>');
      if (selectId) { $categoryEl.val(String(selectId)).change(); }
    }).fail(function () { showMsg('Failed to reload categories.', 'err'); });
  }

  function loadEntities(category_id) {
    return apiGetEntities(category_id).done(function (resp) {
      $('#addUserBtn').prop('disabled', false);
      $userEl.empty().append('<option value="">-- Select User/Vendor --</option>').prop('disabled', false);
      if (Array.isArray(resp)) {
        resp.forEach(function (u) {
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
    }).fail(function () { showMsg('Failed to load users/vendors for this category.', 'err'); });
  }

  function loadProductsForEntity(entity_id) {
    var key = String(entity_id || '');
    if (!key) { return $.Deferred().resolve([]); }
    if (state.productsCacheByEntity[key]) {
      refreshAllProductSelects(key);
      return $.Deferred().resolve(state.productsCacheByEntity[key]);
    }
    return apiGetProducts(key).done(function (list) {
      var safe = normalizeProducts(list);
      state.productsCacheByEntity[key] = safe;
      refreshAllProductSelects(key);
    }).fail(function () {
      showMsg('Failed to load products for this vendor.', 'err');
      state.productsCacheByEntity[key] = [];
      refreshAllProductSelects(key);
    });
  }

  function refreshAllProductSelects(entity_id) {
    // Rebuild each product select with the entity’s product list
    var list = state.productsCacheByEntity[String(entity_id)] || [];
    $productRows.find('tr').each(function () {
      var $r = $(this);
      var currentVal = $r.find('.ip-product').val() || '';
      var $sel = $('<select class="form-control ip-product"><option value="">-- Select Product --</option></select>');
      list.forEach(function (p) {
        // Default price = sale_price; fallback to purchase_price
        var defPrice = (p.sale_price != null ? p.sale_price : (p.purchase_price != null ? p.purchase_price : null));
        var $opt = buildOption(p.product_id, p.product_name, {
          'data-product_name': p.product_name,
          'data-default_price': defPrice != null ? defPrice : '',
          'data-sale_price': (p.sale_price != null ? p.sale_price : ''),
          'data-purchase_price': (p.purchase_price != null ? p.purchase_price : ''),
          'data-product_price_id': p.product_price_id || ''
        });
        $sel.append($opt);
      });
      $r.find('.ip-product').replaceWith($sel);
      if (currentVal) { $sel.val(currentVal).trigger('change'); }
    });
  }

  function loadTodayExpenses() {
    return apiGetExpenses().done(function (resp) {
      if (Array.isArray(resp)) {
        state.expenses = resp.map(function (e) {
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
          state.expenses.forEach(function (r) { if (!seen[r.category_id]) { seen[r.category_id] = r.category_name; } });
          Object.keys(seen).forEach(function (id) {
            $filterCategory.append(buildOption(id, seen[id]));
          });
        }
        renderExpenses();
      }
    }).fail(function (xhr) {
      var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
      showMsg('Error loading expenses: ' + msg, 'err');
    });
  }

  // ===========================================================================
  // SECTION 10: VALIDATION
  // ===========================================================================
  function validateForm() {
    clearInlineErrors();
    var ok = true;
    var catVal = $categoryEl.val();
    var userVal = $userEl.val();
    var rate = Number($rateEl.val());
    var qty = Number($qtyEl.val() || 1);

    if (!catVal) { $categoryEl.addClass('is-invalid'); $errCategory.show(); ok = false; }
    if (!userVal) { $userEl.addClass('is-invalid'); $errUser.show(); ok = false; }

    if (state.productMode) {
      var ok2 = (!!catVal && !!userVal && validateProducts());
      $btnAdd.prop('disabled', !ok2); // classic button (not visible) kept in sync
      syncProductsButtonDisabled(!ok2);
      return ok2;
    }

    if (!(rate >= 0)) { $rateEl.addClass('is-invalid'); $errRate.show(); ok = false; }
    if (!(qty > 0)) { $qtyEl.addClass('is-invalid'); $errQty.show(); ok = false; }

    $btnAdd.prop('disabled', !ok);
    syncProductsButtonDisabled(!ok); // keep products button visual parity when switching
    return ok;
  }

  // ===========================================================================
  // SECTION 11: EVENT HANDLERS
  // ===========================================================================
  // Tabs: keep state in sync if user switches manually
  $tabs.on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
    var target = $(e.target).attr('href');
    var isProducts = (target === '#tab-products');
    state.productMode = isProducts;
    validateForm();

    // If products tab and we have a vendor selected, ensure products are loaded
    var val = $userEl.val();
    if (isProducts && val) { loadProductsForEntity(val); }
  });

  // Category change
  $categoryEl.on('change', function () {
    var val = $(this).val();
    var catName = $(this).find('option:selected').text();

    // Toggle product mode from category label (veg/shop)
    var catLabel = catName || '';
    setProductMode(isVegShopCategoryLabel(catLabel));

    if (val && val !== '__add_category__') {
      loadEntities(val);
      $('#addUserBtn').prop('disabled', false);
    } else if (val === '__add_category__') {
      $('#modalAddCategory').modal('show');
      $categoryEl.val('');
    } else {
      $userEl.prop('disabled', true).empty().append('<option value="">-- Select User/Vendor --</option>');
      setRateHintByCategoryKey(catLabel);
    }
    validateForm();
  });

  // User change
  $userEl.on('change', function () {
    var val = $(this).val();
    var $opt = $(this).find('option:selected');
    var itemName = $opt.data('item_name') || '';
    var unit = $opt.data('unit') || '';
    var price = $opt.data('price');

    if (!state.productMode) {
      if (price !== undefined && price !== '') { $rateEl.val(price); } else { $rateEl.val(''); }
      if (unit) $qtyEl.attr('placeholder', unit); else $qtyEl.attr('placeholder', '1.00');
      if (itemName) $rateHintEl.text('Item: ' + itemName + (unit ? ' (' + unit + ')' : '')); else setRateHintByCategoryKey(($categoryEl.find('option:selected').text() || ''));
    }

    if (val === '__add_user__') {
      $('#modalAddEntity').modal('show');
      $userEl.val('');
    } else {
      if (state.productMode && val) {
        loadProductsForEntity(val);
      }
    }
    validateForm();
  });

  // Save Category
  $('#addCategory').on('click', function (e) {
    e.preventDefault();
    var $btn = $(this);
    var label = $('#newCatLabel').val().trim();
    if (!label) { $('#fg-newcat input').addClass('is-invalid'); $('#err-newcat').show(); return; }

    setLoading($btn, true);
    apiAddCategory({ category_name: label })
      .done(function (resp) {
        if (resp && resp.success) {
          loadCategories(resp.new_id);
          $('#modalAddCategory').modal('hide');
          $('#newCatLabel').val('');
          showMsg('Category added.', 'ok');
        } else {
          showMsg((resp && resp.message) || 'Failed to add category.', 'err');
        }
      })
      .fail(function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
        showMsg('Error: ' + msg, 'err');
      })
      .always(function () { setLoading($btn, false); });
  });

  // Save User/Vendor
  $('#addUserVendor').on('click', function (e) {
    e.preventDefault();
    var $btn = $(this);
    var catId = $categoryEl.val();
    var name = ($('#entity-name').val() || '').trim();
    var item_name = ($('#entity-item-name').val() || '').trim();
    var unit = ($('#entity-item-unit').val() || '').trim();
    var price = Number($('#entity-item-price').val());

    clearInlineErrors();
    var ok = true;
    if (!catId) { $categoryEl.addClass('is-invalid'); $errCategory.show(); ok = false; }
    if (!name) { $('#entity-name').addClass('is-invalid'); $('#err-entity-name').show(); ok = false; }
    if (!item_name) { $('#entity-item-name').addClass('is-invalid'); $('#err-item-name').show(); ok = false; }
    if (!unit) { $('#entity-item-unit').addClass('is-invalid'); $('#err-item-unit').show(); ok = false; }
    if (!(price >= 0)) { $('#entity-item-price').addClass('is-invalid'); $('#err-item-price').show(); ok = false; }
    if (!ok) return;

    setLoading($btn, true);
    apiAddCategoryEntity({ category_id: catId, name: name, item_name: item_name, unit: unit, price: price })
      .done(function (resp) {
        if (resp && resp.success) {
          loadEntities(catId);
          $('#modalAddEntity').modal('hide');
          $('#formAddEntity')[0].reset();
          showMsg('User/Vendor added.', 'ok');
        } else {
          showMsg((resp && resp.message) || 'Failed to add user/vendor.', 'err');
        }
      })
      .fail(function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
        showMsg('Error: ' + msg, 'err');
      })
      .always(function () { setLoading($btn, false); });
  });

  // Products tab -> trigger same submit
  $('#btnAddExpenseProducts').on('click', function () {
    $('#expenseForm').submit();
  });

  // Form submit -> open confirm modal
  $('#expenseForm').on('submit', function (e) {
    e.preventDefault();
    if (!validateForm()) return;

    var catId = $categoryEl.val();
    var catName = $categoryEl.find('option:selected').text().trim();
    var userId = $userEl.val();
    var $opt = $userEl.find('option:selected');
    var userName = ($opt.text() || '').replace(/— Rate:.*/, '').trim();
    var rate = Number($rateEl.val());
    var qty = Number($qtyEl.val() || 1);
    var rate_id = $opt.data('rate_id') || '';

    if (state.productMode) {
      if (!validateProducts()) return;

      var items = getProductRows().filter(function (it) {
        return it.product_id && (it.price >= 0) && (it.qty > 0);
      });
      var batchTotal = items.reduce(function (s, it) { return s + (it.price * it.qty); }, 0);

      state.pendingAdd = {
        mode: 'products',
        category_id: catId,
        catName: catName,
        entity_id: Number(userId),
        userName: userName,
        items: items.map(function (it) {
          return {
            product_id: it.product_id,
            product_price_id: it.product_price_id,
            product_name: it.product_name,
            qty: it.qty,
            rate: it.price,
            amount: it.price * it.qty,
            expense_date: todayYmd(),
            rate_id: 0 // backend can ignore in product mode
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
      // Classic mode
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
  $('#confirmAddBtn').on('click', function () {
    if (!state.pendingAdd) return;
    setLoading($btnAdd, true);
    $('#modalConfirmAdd').modal('hide');

    function sendAdd(p) { return apiAddExpense(p); }

    if (state.pendingAdd.mode === 'products') {
      var reqs = state.pendingAdd.items.map(function (it) {
        return sendAdd({
          category_id: state.pendingAdd.category_id,
          entity_id: state.pendingAdd.entity_id,
          product_id: it.product_id,
          product_price_id: it.product_price_id,
          rate_id: it.rate_id,
          rate: it.rate,
          qty: it.qty,
          amount: it.amount,
          expense_date: it.expense_date
        });
      });

      $.when.apply($, reqs)
        .done(function () {
          showMsg('Products added successfully.', 'ok');
          $productRows.empty();
          addProductRow();
          recalcProducts();
          loadTodayExpenses();
          $(document).trigger('expenseSaved');
        })
        .fail(function (xhr) {
          var msg = (xhr && xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : (xhr && xhr.statusText) || 'Failed.';
          showMsg('Error adding products: ' + msg, 'err');
        })
        .always(function () {
          setLoading($btnAdd, false);
          state.pendingAdd = null;
        });

    } else {
      var payload = {
        category_id: state.pendingAdd.category_id,
        entity_id: state.pendingAdd.entity_id,
        rate_id: state.pendingAdd.item_id,
        rate: state.pendingAdd.rate,
        qty: state.pendingAdd.qty,
        amount: state.pendingAdd.amount,
        expense_date: state.pendingAdd.expense_date
      };
      sendAdd(payload)
        .done(function (resp) {
          if (resp && resp.success) {
            showMsg('Expense added successfully.', 'ok');
            $qtyEl.val('1');
            validateForm();
            loadTodayExpenses();
            $(document).trigger('expenseSaved');
          } else {
            showMsg((resp && resp.message) || 'Failed to add expense.', 'err');
          }
        })
        .fail(function (xhr) {
          var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
          showMsg('Error: ' + msg, 'err');
        })
        .always(function () {
          setLoading($btnAdd, false);
          state.pendingAdd = null;
        });
    }
  });

  // Edit/Delete actions
  $('#expenseRows').on('click', '.btn-edit', function () {
    var id = $(this).data('id');
    var row = state.expenses.find(function (r) { return String(r.expense_id || '') === String(id); });
    if (!row) { showMsg('Row not found.', 'err'); return; }
    $('#edit-expense-id').val(id);
    $('#edit-rate').val(row.rate);
    $('#edit-qty').val(row.qty);
    $('#modalEditExpense').modal('show');
  });

  $('#formEditExpense').on('submit', function (e) {
    e.preventDefault();
    var id = $('#edit-expense-id').val();
    var rate = Number($('#edit-rate').val());
    var qty = Number($('#edit-qty').val());
    if (!(rate >= 0) || !(qty > 0)) { showMsg('Enter valid rate and quantity.', 'err'); return; }

    apiUpdateExpense({ expense_id: id, rate: rate, qty: qty, amount: rate * qty })
      .done(function (resp) {
        if (resp && resp.success) {
          showMsg('Expense updated.', 'ok');
          $('#modalEditExpense').modal('hide');
          loadTodayExpenses();
        } else {
          showMsg((resp && resp.message) || 'Failed to update expense.', 'err');
        }
      })
      .fail(function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
        showMsg('Error: ' + msg, 'err');
      });
  });

  $('#expenseRows').on('click', '.btn-delete', function () {
    var id = $(this).data('id');
    if (!id) { showMsg('Missing expense ID.', 'err'); return; }
    var amount = $(this).data('amount');
    $('#delete-expense-id').val(id);
    $('#delete-amount').text(amount);
    $('#delete-reason').val('');
    $('#delete-reason').removeClass('is-invalid');
    $('#err-del-reason').hide();
    $('#modalDeleteReason').modal('show');
  });

  $('#formDeleteExpense').on('submit', function (e) {
    e.preventDefault();
    var id = $('#delete-expense-id').val();
    var reason = ($('#delete-reason').val() || '').trim();
    if (reason.replace(/\s+/g, '').length < 5) {
      $('#delete-reason').addClass('is-invalid');
      $('#err-del-reason').show();
      return;
    }
    apiDeleteExpense({ expense_id: id, reason: reason })
      .done(function (resp) {
        if (resp && resp.success) {
          showMsg('Expense deleted.', 'ok');
          $('#modalDeleteReason').modal('hide');
          loadTodayExpenses();
        } else {
          showMsg((resp && resp.message) || 'Failed to delete expense.', 'err');
        }
      })
      .fail(function (xhr) {
        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : xhr.statusText;
        showMsg('Error: ' + msg, 'err');
      });
  });

  // Product table events
  $('#btnAddProductRow').on('click', function () { addProductRow(); });
  $('#productRows')
    .on('input', '.ip-price, .ip-qty', recalcProducts)
    .on('click', '.btn-del-line', function () {
      $(this).closest('tr').remove();
      if ($productRows.children('tr').length === 0) { addProductRow(); }
      recalcProducts();
    });

  // Quick Actions Event Handlers
  $('#btnQuickAddCommon').on('click', function () {
    var commonProducts = [
      { name: 'Tomato', price: 40 },
      { name: 'Onion', price: 30 },
      { name: 'Potato', price: 25 },
      { name: 'Rice', price: 60 },
      { name: 'Oil', price: 120 }
    ];

    commonProducts.forEach(function (product) {
      addProductRow({ price: product.price, qty: 1 });
      // With dropdown-only UI, user will select the actual product if needed.
    });
  });

  $('#btnClearAll').on('click', function () {
    if (confirm('Are you sure you want to clear all products?')) {
      $productRows.empty();
      addProductRow();
      recalcProducts();
    }
  });

  $('#btnDuplicateLast').on('click', function () {
    var $lastRow = $productRows.find('tr:last');
    if ($lastRow.length) {
      var rowData = extractRowData($lastRow);
      addProductRow(rowData);
    }
  });

  // Keyboard shortcuts
  $(document).on('keydown', function (e) {
    if (e.ctrlKey) {
      switch (e.key) {
        case 'n': // Ctrl+N = New product row
          e.preventDefault();
          addProductRow();
          break;
        case 's': // Ctrl+S = Save
          e.preventDefault();
          if (!$('#btnAddExpenseProducts').prop('disabled') || !$btnAdd.prop('disabled')) {
            $('#expenseForm').submit();
          }
          break;
      }
    }
  });

  // Filters
  $filterCategory.on('change', function () { filters.category = $(this).val(); renderExpenses(); });
  $searchInput.on('input', debounce(function () { filters.search = $(this).val(); renderExpenses(); }, 150));
  $clearFilters.on('click', function () { filters = { category: '', search: '' }; $filterCategory.val(''); $searchInput.val(''); renderExpenses(); });

  // Report buttons
  $btnBuildReport.on('click', function () {
    buildReport();
    $('#printArea').addClass('dense');
    $('html, body').animate({ scrollTop: $printArea.offset().top - 10 }, 200);
  });
  $btnPrintReport.on('click', function () {
    buildReport();
    $('#printArea').addClass('dense');
    window.print();
  });

  // ===========================================================================
  // SECTION 12: AUTO-SAVE FUNCTIONALITY
  // ===========================================================================
  function addAutoSave() {
    var draftKey = 'expense_draft_' + todayYmd();

    function saveDraft() {
      var formData = {
        category: $categoryEl.val(),
        user: $userEl.val(),
        products: getProductRows()
      };
      try {
        localStorage.setItem(draftKey, JSON.stringify(formData));
      } catch (e) {
        console.warn('Could not save draft:', e);
      }
    }

    function loadDraft() {
      try {
        var draft = localStorage.getItem(draftKey);
        if (draft) {
          var data = JSON.parse(draft);
          if (data.category) {
            $categoryEl.val(data.category).change();
            setTimeout(function () {
              if (data.user) {
                $userEl.val(data.user).change();
                setTimeout(function () {
                  if (data.products && data.products.length > 0) {
                    $productRows.empty();
                    data.products.forEach(function (product) {
                      addProductRow(product);
                    });
                    recalcProducts();
                  }
                }, 500);
              }
            }, 500);
          }
        }
      } catch (e) {
        console.warn('Could not load draft:', e);
      }
    }

    function clearDraft() {
      try {
        localStorage.removeItem(draftKey);
      } catch (e) {
        console.warn('Could not clear draft:', e);
      }
    }

    // Auto-save every 30 seconds
    setInterval(saveDraft, 30000);

    // Save on form changes
    $categoryEl.add($userEl).on('change', saveDraft);
    $productRows.on('input change', '.ip-product, .ip-price, .ip-qty', saveDraft);

    // Clear draft on successful save
    $(document).on('expenseSaved', clearDraft);

    // Load draft on page load
    loadDraft();
  }

  // ===========================================================================
  // SECTION 13: INIT
  // ===========================================================================
  function initCategoriesAndFilters() {
    var hasServerCats = $('#category option').length > 2; // includes default + "Add new"
    if (!hasServerCats) {
      loadCategories();
    } else {
      // Copy existing options to filter (skip default and add-new)
      $filterCategory.empty().append('<option value="">All categories</option>');
      $('#category option').each(function () {
        var v = $(this).val(); var t = $(this).text();
        if (v && v !== '__add_category__') $filterCategory.append(buildOption(v, t));
      });
    }
  }

  // Init
  initCategoriesAndFilters();
  loadTodayExpenses();
  validateForm();
  addAutoSave();
})();
