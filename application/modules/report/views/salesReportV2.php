<?php
// ============================================================================
// sales_report.php (Bootstrap 3.3.7 + jQuery)
// - SALES: Sales / Charity / Guest / Employee -> shops -> product counts + amounts
// - EXPENSES: Matches your Expenses page layout (#, Category, User/Vendor, Rate, Qty, Amount)
//   wired to your exact get_expenses response keys (strings: "price","quantity","total_amount")
// - Totals: Grand Sales, Total Expenses, Net + Previous Balance = Total Balance Available
// - Compact A4 print with tiny spacings
// - Endpoints (GET):
//     get_sales?date=YYYY-MM-DD
//     get_expenses?date=YYYY-MM-DD  -> array of:
//       {"expense_id":"13","category_id":"2","entity_id":"5","rate_id":"0","price":"1600","quantity":"1","total_amount":"1600","description":null,"reason":null,"status":"1","expense_date":"2025-09-11","created_at":"2025-09-11 06:22:06","updated_at":"2025-09-11 06:22:06","category_name":"Employees","entity_name":"Maryam Bibi - Cashier"}
// ============================================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Sales & Expenses Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 3.3.7 -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

  <style>
    body { background:#f7f7f9; }
    .page-header { margin:15px 0 10px; }
    .panel { border-radius:6px; }
    .panel-heading { font-weight:600; }
    .spin { animation:spin 1s linear infinite; }
    @keyframes spin { from {transform:rotate(0)} to {transform:rotate(360deg)} }

    .filter-bar { background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:10px 12px; margin-bottom:12px; box-shadow:0 1px 1px rgba(0,0,0,.03); }
    .filter-label { font-weight:600; margin-right:6px; }
    .help-inline { color:#888; font-size:12px; }

    /* SALES */
    .group-block { margin-bottom:14px; page-break-inside: avoid; }
    .group-title { margin:0 0 6px; font-weight:700; }
    .shop-title { margin:6px 0 4px; font-weight:600; }
    .table thead th { background:#f7fbff; }
    .subtotal-row { background:#fafafa; font-weight:700; }
    .grand-row { background:#eef6ff; font-weight:700; }

    /* EXPENSES (same columns/look as your page, compact) */
    .exp-table thead th { background:#f7fbff; }
    .exp-total-row { background:#fafafa; font-weight:700; }
    .subline { color:#777; font-size:11px; line-height:1.2; margin-top:2px; }

    .totals-card { background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:10px; }
    .totals-card .row { margin-bottom:6px; }
    .totals-card .label-t { color:#666; }
    .totals-card .val-t { font-weight:700; text-align:right; }

    /* two-column layout on desktop */
    @media (min-width: 992px) {
      .col-sales { width: 66.6666%; float:left; padding-right:7px; }
      .col-exp   { width: 33.3333%; float:left; padding-left:7px; }
    }

    /* Print: only print content area + compact spacing for A4 */
    @media print {
      @page { size: A4 portrait; margin: 8mm; }
      body * { visibility: hidden !important; }
      #printRoot, #printRoot * { visibility: visible !important; }
      #printRoot { position:absolute; left:0; top:0; width:100%; }

      #printRoot { font-size:11px; line-height:1.15; }
      .page-header { margin:0 0 6px; }
      .page-header h3 { font-size:16px; margin:0; }
      .filter-bar, .no-print { display:none !important; }

      .group-block { margin-bottom:6px; page-break-inside:avoid; }
      .group-title { font-size:13px; margin:0 0 4px; }
      .shop-title { font-size:12px; margin:4px 0; }

      table.table { margin-bottom:6px; border-collapse:collapse !important; table-layout:fixed; width:100%; }
      table.table > thead > tr > th,
      table.table > tbody > tr > td,
      table.table > tfoot > tr > th,
      table.table > tfoot > tr > td {
        padding:2px 4px !important;
        line-height:1.1 !important;
        border-width:0.5px !important;
        vertical-align:middle;
        font-size:11px;
      }
      table.table thead th { background:#fff !important; }
      .subtotal-row, .grand-row, .exp-total-row { background:#fff !important; font-weight:700; }

      .totals-card { border:none; padding:0; }
      .totals-card .row { margin-bottom:4px; }
      .subline { font-size:10px; }
    }
  </style>
</head>
<body>
<div class="container" id="printRoot" style="max-width:1200px;">

  <!-- Header -->
  <div class="page-header">
    <h3 class="m-b-0">Sales & Expenses Report <small class="text-muted">Daily — compact A4</small></h3>
  </div>

  <!-- Filters -->
  <div class="filter-bar row no-print">
    <div class="col-sm-3">
      <label class="filter-label" for="reportDate">Date</label>
      <input id="reportDate" type="date" class="form-control">
      <div class="help-inline">Report for selected day</div>
    </div>
    <div class="col-sm-3">
      <label class="filter-label" for="prevBalance">Previous Balance</label>
      <input id="prevBalance" type="number" step="0.01" class="form-control" placeholder="0.00" value="0">
      <div class="help-inline">Carried from last period</div>
    </div>
    <div class="col-sm-3" style="margin-top:24px;">
      <button id="btnLoad" class="btn btn-primary">
        <span class="glyphicon glyphicon-refresh"></span> Load Data
      </button>
      <button id="btnPrint" class="btn btn-default">
        <span class="glyphicon glyphicon-print"></span> Print
      </button>
    </div>
    <div class="col-sm-3" style="margin-top:28px;">
      <span id="statusMsg" class="text-muted"></span>
    </div>
  </div>

  <!-- Two-column: Sales (left) / Expenses (right) -->
  <div class="row">
    <!-- SALES COLUMN -->
    <div class="col-sales">
      <div class="panel panel-default">
        <div class="panel-heading">Sales Breakdown</div>
        <div class="panel-body">
          <p class="text-muted m-b-10">Grouped by <strong>Sales / Charity / Guest / Employee</strong>, then by <strong>Shop</strong>, with per-product counts.</p>
          <div id="salesBlocks">
            <div class="text-center text-muted" id="noSales">No sales loaded.</div>
          </div>
        </div>
        <table class="table table-bordered m-b-0">
          <tfoot>
            <tr class="grand-row">
              <td><strong>Grand Total Sales</strong></td>
              <td style="width:180px;" class="text-right"><strong id="grandSales">0.00</strong></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <!-- EXPENSES COLUMN (same structure as expense page) -->
    <div class="col-exp">
      <div class="panel panel-default">
        <div class="panel-heading">Expenses (Today)</div>
        <div class="panel-body" style="padding-bottom:6px;">
          <div class="table-responsive" style="max-height:420px; overflow:auto;">
            <table class="table table-bordered exp-table" id="expensesTable">
              <thead>
                <tr>
                  <th class="text-center" style="width:44px;">#</th>
                  <th class="text-center">Category</th>
                  <th class="text-center">User/Vendor</th>
                  <th class="text-right" style="width:110px;">Rate</th>
                  <th class="text-right" style="width:90px;">Qty</th>
                  <th class="text-right" style="width:120px;">Amount</th>
                </tr>
              </thead>
              <tbody id="expenseRows">
                <tr><td colspan="6" class="text-center text-muted">No expenses loaded.</td></tr>
              </tbody>
              <tfoot>
                <tr class="exp-total-row">
                  <td class="text-right" colspan="5"><strong>Total Expenses</strong></td>
                  <td class="text-right"><strong id="expensesTotal">0.00</strong></td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>

      <!-- TOTALS & BALANCE CARD -->
      <div class="totals-card">
        <div class="row">
          <div class="col-xs-6 label-t">Grand Total Sales</div>
          <div class="col-xs-6 val-t" id="tSales">0.00</div>
        </div>
        <div class="row">
          <div class="col-xs-6 label-t">Total Expenses</div>
          <div class="col-xs-6 val-t" id="tExpenses">0.00</div>
        </div>
        <hr style="margin:8px 0;">
        <div class="row">
          <div class="col-xs-6 label-t">(Sales − Expenses)</div>
          <div class="col-xs-6 val-t" id="tNet">0.00</div>
        </div>
        <div class="row">
          <div class="col-xs-6 label-t">+ Previous Balance</div>
          <div class="col-xs-6 val-t" id="tPrev">0.00</div>
        </div>
        <div class="row" style="margin-top:6px;">
          <div class="col-xs-6 label-t"><strong>Total Balance Available</strong></div>
          <div class="col-xs-6 val-t" id="tAvail"><strong>0.00</strong></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- jQuery + Bootstrap JS -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
(function(){
  'use strict';

  // ---------- Utilities ----------
  function toMoney(n){ return (Number(n || 0)).toFixed(2); }
  function todayYmd(){
    var d = new Date();
    var m = (d.getMonth()+1).toString().padStart(2,'0');
    var day = d.getDate().toString().padStart(2,'0');
    return d.getFullYear() + '-' + m + '-' + day;
  }
  function setStatus(msg, kind){
    var $s = $('#statusMsg');
    $s.removeClass('text-success text-danger text-muted');
    $s.addClass(kind==='ok'?'text-success':(kind==='err'?'text-danger':'text-muted'));
    $s.text(msg||'');
  }

  // ---------- State ----------
  var salesRows = [];   // { group, shop, product, qty, amount }
  var expenseRows = []; // from get_expenses (exact keys you shared)

  // Group labels map
  var GROUP_LABELS = {
    sales: 'Sales',
    charity: 'Charity Sales',
    guest: 'Guest Sales',
    employee: 'Employee Sales'
  };

  // ---------- Data Loading ----------
  function loadAll(){
    var date = $('#reportDate').val() || todayYmd();
    setStatus('Loading…', 'info');
    $('#noSales').show();
    $('#salesBlocks').children('.group-block').remove();

    var p1 = $.getJSON('get_sales', { date: date }).then(function(resp){
      if(Array.isArray(resp)) salesRows = sanitizeSales(resp); else salesRows = [];
    }, function(){ salesRows = []; });

    var p2 = $.getJSON('/hotel-rms/ordermanage/order/get_expenses', { date: date }).then(function(resp){
      if(Array.isArray(resp)) expenseRows = sanitizeExpenses(resp); else expenseRows = [];
    }, function(){ expenseRows = []; });

    $.when(p1, p2).always(function(){
      renderSales();
      renderExpenses();
      computeTotals();
      setStatus('Loaded for '+date, 'ok');
    });
  }

  function sanitizeSales(arr){
    return arr.map(function(r){
      var g = (r.group||'').toString().toLowerCase();
      if(['sales','charity','guest','employee'].indexOf(g)===-1){ g = 'sales'; }
      return {
        group: g,
        shop: r.shop || 'Unknown Shop',
        product: r.product || 'Unknown Product',
        qty: Number(r.qty) || 0,
        amount: Number(r.amount) || 0
      };
    });
  }

  // EXACT mapping for your get_expenses payload
  // We also sort by created_at DESC for "today's details"
  function sanitizeExpenses(arr){
    var rows = arr.map(function(e){
      var rate = parseFloat(e.price || 0);
      var qty  = parseFloat(e.quantity || 0);
      var amt  = parseFloat(e.total_amount || (rate * qty));
      return {
        expense_id: e.expense_id,
        category_name: e.category_name || '-',
        entity_name:   e.entity_name   || '-',
        price: isFinite(rate) ? rate : 0,
        quantity: isFinite(qty) ? qty : 0,
        total_amount: isFinite(amt) ? amt : 0,
        expense_date: e.expense_date || '',
        created_at:   e.created_at   || '',
        description:  e.description,
        reason:       e.reason
      };
    });
    // Newest first (created_at desc, fallback to expense_id desc)
    rows.sort(function(a,b){
      var da = a.created_at || '';
      var db = b.created_at || '';
      if (da && db) return (db > da) ? 1 : (db < da ? -1 : 0);
      return (parseInt(b.expense_id||0,10) - parseInt(a.expense_id||0,10));
    });
    return rows;
  }

  // ---------- Sales Rendering ----------
  function renderSales(){
    var $root = $('#salesBlocks');
    $root.children('.group-block').remove();
    if(!salesRows.length){ $('#noSales').show(); $('#grandSales').text('0.00'); return; }
    $('#noSales').hide();

    var byGroup = {};
    salesRows.forEach(function(r){
      if(!byGroup[r.group]) byGroup[r.group] = { shops:{}, totals:{}, amount:0 };
      var g = byGroup[r.group];
      if(!g.shops[r.shop]) g.shops[r.shop] = { counts:{}, amount:0 };
      var s = g.shops[r.shop];

      s.counts[r.product] = (s.counts[r.product]||0) + Number(r.qty||0);
      s.amount += Number(r.amount||0);

      g.totals[r.product] = (g.totals[r.product]||0) + Number(r.qty||0);
      g.amount += Number(r.amount||0);
    });

    var grand = 0;

    ['sales','charity','guest','employee'].forEach(function(key){
      if(!byGroup[key]) return;
      var g = byGroup[key];
      grand += g.amount;

      var prodSet = {};
      Object.keys(g.shops).forEach(function(shop){
        var c = g.shops[shop].counts;
        Object.keys(c).forEach(function(p){ prodSet[p]=true; });
      });
      var products = Object.keys(prodSet).sort(function(a,b){ return a.localeCompare(b); });

      var $block = $('<div class="group-block">');
      $block.append('<h4 class="group-title">'+ (GROUP_LABELS[key]||key) +'</h4>');

      Object.keys(g.shops).sort(function(a,b){ return a.localeCompare(b); }).forEach(function(shop){
        var s = g.shops[shop];
        $block.append('<div class="shop-title">'+ shop +'</div>');
        var $table = $('<table class="table table-bordered table-condensed">');
        var $thead = $('<thead><tr><th>Product</th><th class="text-right" style="width:140px;">Quantity</th></tr></thead>');
        var $tbody = $('<tbody>');
        products.forEach(function(p){
          var qty = s.counts[p]||0;
          $tbody.append('<tr><td>'+ p +'</td><td class="text-right">'+ qty +'</td></tr>');
        });
        var $tfoot = $('<tfoot><tr class="subtotal-row"><td class="text-right"><strong>Shop Total ('+shop+')</strong></td><td class="text-right"><strong>'+ toMoney(s.amount) +'</strong></td></tr></tfoot>');
        $table.append($thead).append($tbody).append($tfoot);
        $block.append($table);
      });

      var $gt = $('<table class="table table-bordered">');
      var $gtBody = $('<tbody>');
      Object.keys(g.totals).sort(function(a,b){ return a.localeCompare(b); }).forEach(function(p){
        $gtBody.append('<tr><td class="text-right"><strong>Total '+ p +' ('+(GROUP_LABELS[key]||key)+')</strong></td><td class="text-right" style="width:180px;"><strong>'+ (g.totals[p]||0) +'</strong></td></tr>');
      });
      $gtBody.append('<tr class="subtotal-row"><td class="text-right"><strong>'+ (GROUP_LABELS[key]||key) +' Amount</strong></td><td class="text-right"><strong>'+ toMoney(g.amount) +'</strong></td></tr>');
      $gt.append($gtBody);
      $block.append($gt);

      $('#salesBlocks').append($block);
    });

    $('#grandSales').text(toMoney(grand));
  }

  // ---------- Expenses Rendering (same as your page) ----------
  function renderExpenses(){
    var $rows = $('#expenseRows');
    $rows.empty();
    var total = 0;
    if(!expenseRows.length){
      $rows.append('<tr><td colspan="6" class="text-center text-muted">No expenses loaded.</td></tr>');
      $('#expensesTotal').text('0.00');
      return;
    }
    expenseRows.forEach(function(e, i){
      total += Number(e.total_amount)||0;
      var idx = i+1;

      var $tr = $('<tr>');
      $tr.append('<td class="text-center">'+ idx +'</td>');

      // Category (main) + created_at subline to show "all details today" compactly
      var catCell = '<div>'+ (e.category_name || '-') +'</div>';
      if(e.created_at){
        catCell += '<div class="subline">'+ e.created_at +'</div>';
      }
      $tr.append('<td class="text-center">'+ catCell +'</td>');

      // Vendor + optional description/reason subline if present
      var venCell = '<div>'+ (e.entity_name || '-') +'</div>';
      var extras = [];
      if(e.description) extras.push('Desc: '+ e.description);
      if(e.reason) extras.push('Reason: '+ e.reason);
      if(extras.length){ venCell += '<div class="subline">'+ extras.join(' | ') +'</div>'; }
      $tr.append('<td class="text-center">'+ venCell +'</td>');

      $tr.append('<td class="text-right">'+ toMoney(e.price) +'</td>');
      $tr.append('<td class="text-right">'+ toMoney(e.quantity) +'</td>');
      $tr.append('<td class="text-right">'+ toMoney(e.total_amount) +'</td>');
      $rows.append($tr);
    });
    $('#expensesTotal').text(toMoney(total));
  }

  // ---------- Totals & Balance ----------
  function computeTotals(){
    var grandSales = Number($('#grandSales').text()) || 0;
    var expensesTotal = Number($('#expensesTotal').text()) || 0;
    var prevBal = Number($('#prevBalance').val()) || 0;

    var net = grandSales - expensesTotal;
    var avail = net + prevBal;

    $('#tSales').text(toMoney(grandSales));
    $('#tExpenses').text(toMoney(expensesTotal));
    $('#tNet').text(toMoney(net));
    $('#tPrev').text(toMoney(prevBal));
    $('#tAvail').text(toMoney(avail));
  }

  // ---------- Events ----------
  $('#btnLoad').on('click', function(){
    var $btn = $(this);
    $btn.prop('disabled', true).html('<span class="glyphicon glyphicon-refresh spin"></span> Loading…');
    loadAll();
    setTimeout(function(){ $btn.prop('disabled', false).html('<span class="glyphicon glyphicon-refresh"></span> Load Data'); }, 600);
  });

  $('#btnPrint').on('click', function(){
    computeTotals();
    window.print();
  });

  $('#prevBalance').on('input', computeTotals);

  // Init defaults (today)
  $('#reportDate').val(todayYmd());
  loadAll();

  // ---------- OPTIONAL DEMO (uncomment to preview without backend) ----------
  
  $.getJSON = function(url, data){
    return $.Deferred(function(dfd){
      if(url.indexOf('get_sales')>=0){
        dfd.resolve([
          {group:'sales', shop:'Canteen 1', product:'Tea', qty:15, amount:750},
          {group:'sales', shop:'Canteen 1', product:'Naan', qty:30, amount:600},
          {group:'guest', shop:'Kitchen A', product:'Sabzi', qty:10, amount:900},
          {group:'charity', shop:'Kitchen B', product:'Tea', qty:12, amount:0},
          {group:'employee', shop:'Canteen 2', product:'Naan', qty:25, amount:375},
          {group:'sales', shop:'Kitchen A', product:'Sabzi', qty:8, amount:720},
          {group:'guest', shop:'Canteen 2', product:'Tea', qty:5, amount:250}
        ]);
      } else if(url.indexOf('get_expenses')>=0){
        dfd.resolve([
          {"expense_id":"13","category_id":"2","entity_id":"5","rate_id":"0","price":"1600","quantity":"1","total_amount":"1600","description":null,"reason":null,"status":"1","expense_date":"2025-09-11","created_at":"2025-09-11 06:22:06","updated_at":"2025-09-11 06:22:06","category_name":"Employees","entity_name":"Maryam Bibi - Cashier"},
          {"expense_id":"12","category_id":"2","entity_id":"4","rate_id":"0","price":"2000","quantity":"1","total_amount":"2000","description":null,"reason":null,"status":"1","expense_date":"2025-09-11","created_at":"2025-09-11 06:22:03","updated_at":"2025-09-11 06:22:03","category_name":"Employees","entity_name":"John Doe - Salesman"},
          {"expense_id":"11","category_id":"1","entity_id":"2","rate_id":"0","price":"168","quantity":"7","total_amount":"1176","description":null,"reason":null,"status":"1","expense_date":"2025-09-11","created_at":"2025-09-11 06:21:56","updated_at":"2025-09-11 06:21:56","category_name":"Milk","entity_name":"Nazeer Milk Vendor"}
        ]);
      } else { dfd.resolve([]); }
    }).promise();
  };
})();
</script>
</body>
</html>
