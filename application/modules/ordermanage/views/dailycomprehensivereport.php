<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Daily Comprehensive Report</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 3.3.7 + jQuery (matching todaymanageexpenses) -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <link rel="stylesheet" href="<?php echo base_url('application/modules/ordermanage/assets/css/todayexpense.css'); ?>" type="text/css">
  
  <!-- Print Styles - Preserve Preview Design (same as todaymanageexpenses) -->
  <style>
    @media print {
      /* Hide non-essential elements for printing */
      .no-print, .btn, .form-control, .modal, 
      .page-header, .panel, .panel-heading, .panel-body, .form-group {
        display: none !important;
      }
      
      /* Show all printable sections */
      #printArea, #expensesPrintArea, .printable-section {
        display: block !important;
      }
      
      /* Page setup for A4 - 2 pages max */
      @page {
        margin: 0.4in !important;
        size: A4 !important;
      }
      
      /* Body and HTML setup - smaller font for more content */
      html, body {
        height: auto !important;
        background: white !important;
        font-size: 10px !important;
        line-height: 1.2 !important;
        color: #000 !important;
      }
      
      /* Container reset */
      .container {
        width: 100% !important;
        max-width: none !important;
        padding: 0 !important;
        margin: 0 !important;
      }
      
      /* All printable areas */
      #printArea, #expensesPrintArea, #additionalReportArea, .printable-section {
        display: block !important;
        background: #fff !important;
        border: none !important;
        border-radius: 0 !important;
        padding: 8px !important;
        margin: 0 0 10px 0 !important;
        width: 100% !important;
        max-width: 100% !important;
        box-shadow: none !important;
        page-break-inside: avoid !important;
      }
      
      /* Force page break after main comprehensive report */
      #printArea {
        page-break-after: always !important;
      }
      
      /* Report header - compact */
      .report-header {
        margin-bottom: 8px !important;
      }
      
      .report-header h3 {
        font-size: 14px !important;
        margin-bottom: 3px !important;
        color: #000 !important;
        font-weight: bold !important;
      }
      
      .report-meta {
        font-size: 9px !important;
        color: #666 !important;
        margin-bottom: 8px !important;
      }
      
      /* Table styling - compact for 2 pages */
      .table {
        border-collapse: collapse !important;
        width: 100% !important;
        margin: 5px 0 !important;
        font-size: 9px !important;
        border: 1px solid #333 !important;
      }
      
      .table td, .table th {
        border: 1px solid #333 !important;
        padding: 3px 4px !important;
        font-size: 9px !important;
        color: #000 !important;
        background-color: #fff !important;
        line-height: 1.1 !important;
      }
      
      .table th {
        background-color: #f0f0f0 !important;
        font-weight: bold !important;
        font-size: 9px !important;
      }
      
      .table-bordered {
        border: 1px solid #ddd !important;
      }
      
      .table-bordered td, .table-bordered th {
        border: 1px solid #ddd !important;
      }
      
      /* Text alignment preserved */
      .text-right {
        text-align: right !important;
      }
      
      .text-center {
        text-align: center !important;
      }
      
      /* Report body */
      #reportBody {
        margin: 0 !important;
      }
      
      /* No data message */
      .no-data {
        padding: 10px 0 !important;
        font-size: 14px !important;
        text-align: center !important;
        color: #666 !important;
      }
      
      /* Grand total row */
      .grand-total {
        page-break-inside: avoid !important;
        background-color: #f9f9f9 !important;
      }
      
      .grand-total td, .grand-total th {
        font-weight: bold !important;
        background-color: #f9f9f9 !important;
      }
      
      /* Category headers in report */
      .category-header {
        background-color: #f0f0f0 !important;
        font-weight: bold !important;
      }
      
      /* Preserve strong/bold text */
      strong, b {
        font-weight: bold !important;
        color: #000 !important;
      }
      
      /* Remove shadows and effects but preserve layout */
      * {
        text-shadow: none !important;
        box-shadow: none !important;
      }
      
      /* Prevent widows and orphans */
      p, h1, h2, h3, h4, h5, h6 {
        orphans: 3 !important;
        widows: 3 !important;
      }
      
      /* Ensure proper spacing */
      .m-b-0 {
        margin-bottom: 0 !important;
      }
      
      /* Category Report Print Styles */
      .category-header {
        background-color: #f0f0f0 !important;
        font-weight: bold !important;
        page-break-after: avoid !important;
        font-size: 10px !important;
        padding: 2px 4px !important;
        margin: 3px 0 !important;
      }
      
      /* Category report body */
      #categoryReportBody {
        margin: 0 !important;
      }
      
      /* Prevent category sections from breaking across pages when possible */
      #categoryReportBody > div {
        page-break-inside: avoid !important;
        margin-bottom: 8px !important;
      }
      
      /* Force page break after first page content */
      .page-break {
        page-break-before: always !important;
      }
      
      /* Summary section at end */
      .summary-section {
        margin-top: 8px !important;
        border-top: 1px solid #333 !important;
        padding-top: 5px !important;
      }
      
      /* Two column layout for print */
      .col-md-6 {
        width: 48% !important;
        float: left !important;
        margin-right: 2% !important;
      }
      
      /* Category block styling */
      .category-block {
        margin-bottom: 15px !important;
        page-break-inside: avoid !important;
      }
      
      .category-title {
        background-color: #f8f9fa !important;
        padding: 8px 12px !important;
        margin: 0 0 5px 0 !important;
        font-size: 12px !important;
        font-weight: bold !important;
        border-left: 3px solid #007bff !important;
        color: #333 !important;
      }
      
      .report-table {
        margin-bottom: 0 !important;
        font-size: 10px !important;
      }
      
      .category-subtotal {
        background-color: #f8f9fa !important;
        font-weight: bold !important;
      }
      
      .grand-total {
        background-color: #e9ecef !important;
        font-weight: bold !important;
        font-size: 11px !important;
      }
      
      .col-md-6:last-child {
        margin-right: 0 !important;
      }
      
      /* Clear floats after columns */
      .row:after {
        content: "" !important;
        display: table !important;
        clear: both !important;
      }
    }
    
    /* Regular CSS for category styling */
    .category-block {
      margin-bottom: 0px;
    }
    
    .category-title {
      background-color: #f8f9fa;
      padding: 4px 8px;
      margin: 0 0 8px 0;
      font-size: 14px;
      font-weight: bold;
      border-left: 4px solid #007bff;
      color: #333;
    }
    
    .report-table {
      margin-bottom: 10px;
    }
    
    .category-subtotal {
      background-color: #f8f9fa;
      font-weight: bold;
    }
    
    .grand-total {
      background-color: #e9ecef;
      font-weight: bold;
    }
    
    .dense {
      font-size: 14px;
    }
  </style>
</head>
<body>

<div class="container" style="max-width:1100px; padding-top:20px;">
  <!-- ========================= Header ========================= -->
  <div class="page-header no-print" style="margin-top:0;">
    <div class="row">
      <div class="col-md-8">
        <h3 class="m-b-0">Daily Comprehensive Report <small class="text-muted">Complete business overview</small></h3>
      </div>
      <div class="col-md-4 text-right">
        <button type="button" id="btnPrintReport" class="btn btn-primary">
          <span class="glyphicon glyphicon-print"></span> Print Report
        </button>
      </div>
    </div>
  </div>

  <!-- ========================= Report Filters ========================= -->
  <div class="panel panel-default no-print" style="margin-bottom: 20px;">
    <div class="panel-heading">
      <h4 class="panel-title">
        <span class="glyphicon glyphicon-filter"></span> Report Filters
      </h4>
    </div>
    <div class="panel-body">
      <form method="GET" action="<?php echo current_url(); ?>" class="form-horizontal" style="background: #f9f9f9; padding: 20px; border-radius: 8px; border: 1px solid #e0e0e0;">
        <div class="row" style="margin-bottom: 15px;">
          <div class="col-md-3" style="margin-bottom: 15px;">
            <div class="form-group" style="margin-bottom: 10px; margin-right:0px;">
              <label class="control-label" style="font-weight: 600; margin-bottom: 5px; display: block;">Filter Type:</label>
              <select name="filter_type" id="filter_type" class="form-control" onchange="toggleFilterOptions()" style="padding: 8px 12px;">
                <option value="current" <?php echo (!isset($filterType) || $filterType == 'current') ? 'selected' : ''; ?>>Current Register</option>
                <option value="date_range" <?php echo (isset($filterType) && $filterType == 'date_range') ? 'selected' : ''; ?>>Date Range</option>
                <option value="cash_register" <?php echo (isset($filterType) && $filterType == 'cash_register') ? 'selected' : ''; ?>>Specific Cash Register</option>
                <option value="day_sales" <?php echo (isset($filterType) && $filterType == 'day_sales') ? 'selected' : ''; ?>>Day Sales (6AM-6PM)</option>
                <option value="night_sales" <?php echo (isset($filterType) && $filterType == 'night_sales') ? 'selected' : ''; ?>>Night Sales (6PM-6AM)</option>
                <option value="all_dates" <?php echo (isset($filterType) && $filterType == 'all_dates') ? 'selected' : ''; ?>>All Time</option>
              </select>
            </div>
          </div>
          
          <div class="col-md-3" id="date_range_filters" style="display: none; margin-bottom: 15px;">
            <div class="form-group" style="margin-bottom: 10px; margin-right:0px;">
              <label class="control-label" style="font-weight: 600; margin-bottom: 5px; display: block;">Start Date:</label>
              <input type="date" name="start_date" class="form-control" value="<?php echo isset($startDate) ? $startDate : ''; ?>" style="padding: 8px 12px;">
            </div>
          </div>
          
          <div class="col-md-3" id="date_range_filters2" style="display: none; margin-bottom: 15px;">
            <div class="form-group" style="margin-bottom: 10px; margin-right:0px;">
              <label class="control-label" style="font-weight: 600; margin-bottom: 5px; display: block;">End Date:</label>
              <input type="date" name="end_date" class="form-control" value="<?php echo isset($endDate) ? $endDate : ''; ?>" style="padding: 8px 12px;">
            </div>
          </div>
          
          <div class="col-md-4" id="cash_register_filter" style="display: none; margin-bottom: 15px;">
            <div class="form-group" style="margin-bottom: 10px; margin-right:0px;">
              <label class="control-label" style="font-weight: 600; margin-bottom: 5px; display: block;">Cash Register:</label>
              <select name="cash_register_id" class="form-control" style="padding: 8px 12px;">
                <option value="">Select Cash Register</option>
                <?php if (!empty($cashRegisters)): ?>
                  <?php foreach ($cashRegisters as $register): ?>
                    <option value="<?php echo $register->id; ?>" <?php echo (isset($cashRegisterId) && $cashRegisterId == $register->id) ? 'selected' : ''; ?>>
                      <?php echo !empty($register->counter_name) ? $register->counter_name : 'Counter #' . $register->counter_no; ?> - <?php echo $register->firstname . ' ' . $register->lastname; ?> 
                      (<?php echo date('M d, Y H:i', strtotime($register->opendate)); ?> - <?php echo date('M d, Y H:i', strtotime($register->closedate)); ?>)
                    </option>
                  <?php endforeach; ?>
                <?php endif; ?>
              </select>
            </div>
          </div>
          
          <div class="col-md-2" style="margin-bottom: 15px;">
            <div class="form-group" style="margin-bottom: 10px; margin-right:0px;">
              <label class="control-label" style="font-weight: 600; margin-bottom: 0px; display: block;">&nbsp;</label>
              <button type="submit" class="btn btn-success form-control" style="padding: 5px 15px; font-weight: 600; margin-top: 5px;">
                <span class="glyphicon glyphicon-search"></span> Apply Filter
              </button>
            </div>
          </div>
        </div>
        
        <div class="row">
          <div class="col-md-12">
            <small class="text-muted">
              <strong>Current Filter:</strong> 
              <?php 
              if (!isset($filterType) || $filterType == 'current') {
                echo 'Current Open Register';
              } elseif ($filterType == 'date_range') {
                echo 'Date Range: ' . (isset($startDate) ? $startDate : 'Not set') . ' to ' . (isset($endDate) ? $endDate : 'Not set');
              } elseif ($filterType == 'cash_register') {
                echo 'Specific Cash Register' . (isset($selectedRegister) ? ' (Counter ' . $selectedRegister->counter_no . ')' : '');
              } elseif ($filterType == 'all_dates') {
                echo 'All Time Data';
              }
              ?>
            </small>
          </div>
        </div>
      </form>
    </div>
  </div>

  <script>
    function toggleFilterOptions() {
      var filterType = document.getElementById('filter_type').value;
      var dateRangeFilters = document.getElementById('date_range_filters');
      var dateRangeFilters2 = document.getElementById('date_range_filters2');
      var cashRegisterFilter = document.getElementById('cash_register_filter');
      
      // Hide all filters first
      dateRangeFilters.style.display = 'none';
      dateRangeFilters2.style.display = 'none';
      cashRegisterFilter.style.display = 'none';
      
      // Show relevant filters
      if (filterType == 'date_range') {
        dateRangeFilters.style.display = 'block';
        dateRangeFilters2.style.display = 'block';
      } else if (filterType == 'cash_register') {
        cashRegisterFilter.style.display = 'block';
      }
    }
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
      toggleFilterOptions();
    });
  </script>

  <!-- Debug Info (Remove in production) -->
  <?php if (isset($debugInfo)): ?>
    <div class="alert alert-info no-print" style="margin-bottom: 10px;">
      <strong>Debug Info:</strong> 
      Filter: <?php echo $debugInfo['filterType']; ?> | 
      Expenses: <?php echo $debugInfo['expenseCount']; ?> | 
      Categories: <?php echo $debugInfo['categoryCount']; ?> | 
      Kitchens: <?php echo $debugInfo['kitchenDataCount']; ?>
      <?php if (!empty($expensesByCategory)): ?>
        | Expense Categories: <?php echo implode(', ', array_keys($expensesByCategory)); ?>
      <?php endif; ?>
      <br>
      <small>
        User ID: <?php echo $debugInfo['userId'] ?? 'N/A'; ?> | 
        Has Checkuser: <?php echo $debugInfo['checkuserExists'] ? 'Yes' : 'No'; ?> | 
        Open Date: <?php echo $debugInfo['openDate'] ?? 'N/A'; ?> |
        Date Range: <?php echo ($debugInfo['reportStartDate'] ?? 'N/A') . ' to ' . ($debugInfo['reportEndDate'] ?? 'N/A'); ?>
      </small>
    </div>
  <?php endif; ?>

  <!-- SQL Queries Debug (Remove in production) -->
  <?php if (isset($queryLog) && !empty($queryLog)): ?>
    <div class="alert alert-warning no-print" style="margin-bottom: 10px;">
      <strong>SQL Queries Executed:</strong>
      <div style="max-height: 300px; overflow-y: auto; font-family: monospace; font-size: 11px; background: #f8f8f8; padding: 10px; margin-top: 10px; border: 1px solid #ddd;">
        <?php foreach ($queryLog as $query): ?>
          <div style="margin-bottom: 5px; padding-bottom: 5px; border-bottom: 1px solid #eee;">
            <?php echo htmlspecialchars($query); ?>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endif; ?>

  <!-- Error Check -->
  <?php if (isset($error)): ?>
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="alert alert-danger">
          <h4>Error</h4>
          <p><?php echo htmlspecialchars($error); ?></p>
        </div>
      </div>
    </div>
    </div></body></html>
    <?php return; ?>
  <?php endif; ?>

  <div id="reportBody">


  <!-- Printable Area -->
  <div id="printArea" class="printable-section" style="background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:15px; margin:10px 0; max-width:100%; overflow:hidden; min-height:auto;">
    <div class="report-header">
      <h3 class="m-b-0 no-print">Daily Comprehensive Report</h3>
      <p class="report-meta m-b-0">
        Generated: <?php echo date('Y-m-d H:i:s'); ?>
        | User: <?php 
        if (isset($userinfo) && $userinfo) {
          echo htmlspecialchars($userinfo->firstname . ' ' . $userinfo->lastname); 
        } else {
          echo 'N/A';
        }
        ?>
      </p>
      <p class="report-meta m-b-0" style="color: #337ab7; font-weight: bold;">
        <strong>Report Period:</strong> 
        <?php 
        if (!isset($filterType) || $filterType == 'current') {
          echo 'Current Open Register';
          if (isset($registerinfo) && $registerinfo) {
            echo ' (Started: ' . date('M d, Y H:i', strtotime($registerinfo->opendate)) . ')';
          }
        } elseif ($filterType == 'date_range') {
          echo 'Date Range: ' . date('M d, Y', strtotime($reportStartDate)) . ' to ' . date('M d, Y', strtotime($reportEndDate));
        } elseif ($filterType == 'cash_register') {
          echo 'Cash Register';
          if (isset($selectedRegister) && $selectedRegister) {
            echo ' #' . $selectedRegister->counter_no . ' (' . date('M d, Y H:i', strtotime($selectedRegister->opendate)) . ' - ' . date('M d, Y H:i', strtotime($selectedRegister->closedate)) . ')';
          }
        } elseif ($filterType == 'all_dates') {
          echo 'All Time Data';
          if (isset($reportStartDate) && isset($reportEndDate)) {
            echo ' (' . date('M d, Y', strtotime($reportStartDate)) . ' to ' . date('M d, Y', strtotime($reportEndDate)) . ')';
          }
        }
        ?>
      </p>
    </div>

    <!-- Sales & Financial Overview (Cash Register Style) -->
    <div class="row">
      <div class="col-md-6" style="float: left; width: 50%;">
        <div class="panel panel-default">
          <div class="panel-heading">💰 Financial Overview</div>
          <div class="panel-body">
            <div class="table-responsive">
          <table class="table table-bordered table-striped table-hover">
            <thead>
              <tr>
                <th align="left">Description</th>
                <th align="left">Details</th>
                <th align="right">Amount</th>
              </tr>
            </thead>
            <tbody>
              <?php  
              $total = 0; 
              if (!empty($totalamount)) { 
                foreach ($totalamount as $amount) { 
                  $total = $total + $amount->totalamount;
              ?>
              <tr>
                <td>Payment Method</td>
                <td><?php echo $amount->payment_method; ?></td>
                <td align="right"><?php echo number_format($amount->totalamount, 2); ?></td>
              </tr>
              <?php } } ?>
            </tbody>
            <tfoot>
              <?php 
              // Payment Methods Total
              $totalSales = 0;
              if (!empty($totalamount)) {
                foreach ($totalamount as $amount) {
                  $totalSales += $amount->totalamount;
                }
              }
              
              // Customer Type Sales
              $employeeSales = $customertypewise->employee_sales ?? 0;
              $guestSales = $customertypewise->guest_sales ?? 0;
              $charitySales = $customertypewise->charity_sales ?? 0;
              
              // Calculate Hotel Cash Sale
              $totalHotelCashSale = $totalSales - $employeeSales - $guestSales - $charitySales;
              
              // Shop Sales (from controller)
              $shopRegularSales = isset($shopSalesData) ? $shopSalesData->shop_regular_sales : 0;
              $shopEmployeeSales = isset($shopSalesData) ? $shopSalesData->shop_employee_sales : 0;
              $shopGuestSales = isset($shopSalesData) ? $shopSalesData->shop_guest_sales : 0;
              $totalShopSales = $shopRegularSales + $shopEmployeeSales + $shopGuestSales;
              
              // Shop Sales to be given to shop
              $totalSalesToShop = $totalShopSales;
              
              // Shop expenses from controller (dynamic)
              $shopExpensesAmount = $shopExpenses ?? 0;
              
              $totalExpenseAmount = $totalexpenses ?? 0;
              

              
              $totalAmountToShop = $totalSalesToShop + $shopExpensesAmount;
              $openingBalance = $registerinfo->opening_balance ?? 0;
              
              // Final calculations - matching cash register close logic exactly
              // Cash register formula: total_sales + opening_balance - otherExpenses - totalShopAmountToGive
              // where total_sales = totalHotelCashSale (the green highlighted total)
              $remainingBalance = $totalHotelCashSale + $openingBalance - $otherExpensesTotal - $totalShopAmountToGive;
              $totalProfit = $remainingBalance - $openingBalance;
              ?>
              
              <!-- Payment Method Total -->
              <tr style="background-color: #f0f0f0; font-weight: bold;">
                <td align="right" colspan="2">Total</td>
                <td align="right"><?php echo number_format($totalSales, 2); ?></td>
              </tr>
              
              <!-- Customer Type Deductions -->
              <tr>
                <td align="right" colspan="2">
                  Employee Sales<br>
                  Guest Sales<br>
                  Charity Sales
                </td>
                <td align="right">
                  - <?php echo number_format($employeeSales, 2); ?><br>
                  - <?php echo number_format($guestSales, 2); ?><br>
                  - <?php echo number_format($charitySales, 2); ?>
                </td>
              </tr>
              
              <!-- Hotel Cash Sale -->
              <tr style="background-color: #d4edda; font-weight: bold;">
                <td align="right" colspan="2">Total Hotel Cash Sale With Shop</td>
                <td align="right"><?php echo number_format($totalHotelCashSale, 2); ?></td>
              </tr>
              
              <!-- Shop Sales -->
              <tr>
                <td align="right" colspan="2">
                  Shop - Regular Sales<br>
                  Shop - Employee Sales<br>
                  Shop - Guest Sales
                </td>
                <td align="right">
                  - <?php echo number_format($shopRegularSales, 2); ?><br>
                  - <?php echo number_format($shopEmployeeSales, 2); ?><br>
                  - <?php echo number_format($shopGuestSales, 2); ?>
                </td>
              </tr>
              <tr style="background-color: #e8f4fd; font-weight: bold;">
                <td align="right" colspan="2">Total Sales to be Given to Shop</td>
                <td align="right">- <?php echo number_format($totalSalesToShop, 2); ?></td>
              </tr>
              
              <!-- Shop Expenses -->
              <?php if ($shopExpensesAmount > 0): ?>
              <tr>
                <td align="right" colspan="2">Expenses - Shop</td>
                <td align="right">- <?php echo number_format($shopExpensesAmount, 2); ?></td>
              </tr>
              <?php endif; ?>
              <?php 
              // Calculate total amount to be given to shop (Total Shop Sales + Shop Expenses)
              $totalShopAmountToGive = $totalSalesToShop + $shopExpensesAmount;
              ?>
              <?php if ($totalShopAmountToGive > 0): ?>
              <tr style="background-color: #fff3cd; font-weight: bold;">
                <td align="right" colspan="2">Total Amount to be Given to Shop</td>
                <td align="right">- <?php echo number_format($totalShopAmountToGive, 2); ?></td>
              </tr>
              <?php endif; ?>
              
              <?php 
              // Separate other expenses (non-shop expenses)
              $otherExpensesTotal = 0;
              if (!empty($expensesByCategory)) {
                foreach ($expensesByCategory as $categoryName => $amount) {
                  if (!(strtolower($categoryName) == 'shop' || strpos(strtolower($categoryName), 'shop') !== false)) {
                    $otherExpensesTotal += $amount;
                  }
                }
              }
              ?>
              
              <?php if ($otherExpensesTotal > 0): ?>
              <tr>
                <td align="right" colspan="2">
                  <?php 
                  $expenseLines = array();
                  if (!empty($expensesByCategory)) {
                    foreach ($expensesByCategory as $categoryName => $amount) {
                      if (!(strtolower($categoryName) == 'shop' || strpos(strtolower($categoryName), 'shop') !== false)) {
                        $expenseLines[] = 'Expenses - ' . htmlspecialchars($categoryName);
                      }
                    }
                  }
                  echo implode('<br>', $expenseLines);
                  ?>
                </td>
                <td align="right">
                  <?php 
                  $amountLines = array();
                  if (!empty($expensesByCategory)) {
                    foreach ($expensesByCategory as $categoryName => $amount) {
                      if (!(strtolower($categoryName) == 'shop' || strpos(strtolower($categoryName), 'shop') !== false)) {
                        $amountLines[] = '- ' . number_format($amount, 2);
                      }
                    }
                  }
                  echo implode('<br>', $amountLines);
                  ?>
                </td>
              </tr>
              <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td align="right" colspan="2">Total Other Expenses</td>
                <td align="right">- <?php echo number_format($otherExpensesTotal, 2); ?></td>
              </tr>
              <?php endif; ?>
              
              <!-- Opening Balance -->
              <tr>
                <td align="right" colspan="2">Opening Balance</td>
                <td align="right">+ <?php echo number_format($openingBalance, 2); ?></td>
              </tr>
              
              <!-- Remaining Balance -->
              <tr>
                <td align="right" colspan="2">Remaining Balance</td>
                <td align="right"><?php echo number_format($totalHotelCashSale + $openingBalance - $otherExpensesTotal - $totalShopAmountToGive, 2); ?></td>
              </tr>
              
              <!-- Total Profit -->
              <?php 
              // Calculate Total Profit or Loss (Remaining Balance - Opening Balance) - same as cash register
              $finalRemainingBalance = $totalHotelCashSale + $openingBalance - $otherExpensesTotal - $totalShopAmountToGive;
              $finalTotalProfitLoss = $finalRemainingBalance - $openingBalance;
              ?>
              <tr style="background-color: <?php echo ($finalTotalProfitLoss >= 0) ? '#d4edda' : '#f8d7da'; ?>; font-weight: bold;">
                <td align="right" colspan="2">Total <?php echo ($finalTotalProfitLoss >= 0) ? 'Profit' : 'Loss'; ?></td>
                <td align="right"><?php echo ($finalTotalProfitLoss >= 0 ? '+' : '') . ' ' . number_format($finalTotalProfitLoss, 2); ?></td>
              </tr>
              
              <!-- Cancelled Orders Impact -->
              <?php if (isset($totalCancelledValue) && $totalCancelledValue > 0): ?>
              <tr style="background-color: #fff3cd; color: #856404;">
                <td align="right" colspan="2">Cancelled Orders Impact (<?php echo $totalCancelledOrders ?? 0; ?> orders)</td>
                <td align="right">- <?php echo number_format($totalCancelledValue, 2); ?></td>
              </tr>
              <?php endif; ?>
            </tfoot>
          </table>
        </div>
        
          </div>
        </div>
      </div>
          
      <!-- Payment Methods Column -->
      <div class="col-md-6 last" style="float: left; width: 50%;">
        <div id="expensesPrintArea" style="background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:0px; max-width:100%; overflow:hidden; min-height:auto;">
          <div class="report-header">
            <h3 class="m-b-0" style="margin-top:0px;">Expenses Report</h3>
            <p class="report-meta m-b-0">
              Date: <span id="reportDate"><?php echo date('Y-m-d'); ?></span>
              <span id="reportFilters" style="margin-left:10px;">Grouped by Category</span>
            </p>
          </div>
          <div id="reportBody">
            <?php 
            // Debug information (can be removed in production)
            if (empty($expensesByCategory)) {
              echo "<!-- DEBUG: expensesByCategory is empty -->";
              if (isset($expenses)) {
                echo "<!-- DEBUG: Total raw expenses count: " . count($expenses) . " -->";
              }
            } else {
              echo "<!-- DEBUG: Found " . count($expensesByCategory) . " expense categories -->";
              foreach ($expensesByCategory as $cat => $total) {
                echo "<!-- DEBUG: Category '$cat' = $total -->";
              }
            }
            ?>
            <?php if (!empty($groupedExpenses)): ?>
              <?php foreach ($groupedExpenses as $categoryName => $categoryData): ?>
                <div class="category-block">
                  <h4 class="category-title" style="font-size: 10px; margin-bottom: 1px; margin-top: 3px; padding: 4px 6px; background-color: #f8f9fa; border-left: 3px solid #007bff; color: #495057; line-height: 1.2;"><?php echo htmlspecialchars($categoryName); ?></h4>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped expenses-table" style="margin-bottom: 6px; font-size: 11px;">
                      <thead>
                        <tr>
                          <th class="text-right" style="padding: 3px 4px; line-height: 1.2;">Item Name</th>
                          <th class="text-right" style="width: 70px; padding: 3px 2px; line-height: 1.2;">Rate</th>
                          <th class="text-right" style="width: 50px; padding: 3px 2px; line-height: 1.2;">Qty</th>
                          <th class="text-right" style="width: 80px; padding: 3px 2px; line-height: 1.2;">Amount</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php foreach ($categoryData['items'] as $itemName => $itemData): ?>
                        <tr style="font-size: 10px;">
                          <td style="padding: 2px 4px; line-height: 1.1;"><?php echo htmlspecialchars($itemName); ?></td>
                          <td class="text-right" style="padding: 2px; line-height: 1.1;"><?php echo number_format($itemData['rate'], 2); ?></td>
                          <td class="text-right" style="padding: 2px; line-height: 1.1;"><?php echo number_format($itemData['quantity'], 2); ?></td>
                          <td class="text-right" style="padding: 2px; line-height: 1.1;"><strong><?php echo number_format($itemData['total'], 2); ?></strong></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                      <tfoot>
                        <tr style="background-color: #f8f9fa; font-weight: bold; font-size: 10px;">
                          <th colspan="3" class="text-right" style="padding: 3px 4px; border: 1px solid #ddd; line-height: 1.1;">Category Total:</th>
                          <th class="text-right" style="padding: 3px 2px; border: 1px solid #ddd; line-height: 1.1;"><?php echo number_format($categoryData['total'], 2); ?></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php elseif (!empty($expensesByCategory)): ?>
              <!-- Fallback to old display if groupedExpenses is not available -->
              <?php foreach ($expensesByCategory as $categoryName => $categoryTotal): ?>
                <?php 
                // Get expenses for this category
                $categoryExpenses = array();
                if (!empty($expenses)) {
                  foreach ($expenses as $expense) {
                    if (($expense->category_name ?? 'Uncategorized') == $categoryName) {
                      $categoryExpenses[] = $expense;
                    }
                  }
                }
                ?>
                
                <?php if (!empty($categoryExpenses)): ?>
                <div class="category-block">
                  <h4 class="category-title" style="font-size: 10px; margin-bottom: 1px; margin-top: 3px; padding: 4px 6px; background-color: #f8f9fa; border-left: 3px solid #007bff; color: #495057; line-height: 1.2;"><?php echo htmlspecialchars($categoryName); ?></h4>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped expenses-table" style="margin-bottom: 6px; font-size: 11px;">
                      <thead>
                        <tr>
                          <th class="text-right" style="padding: 3px 4px; line-height: 1.2;">Item Name</th>
                          <th class="text-right" style="width: 70px; padding: 3px 2px; line-height: 1.2;">Rate</th>
                          <th class="text-right" style="width: 50px; padding: 3px 2px; line-height: 1.2;">Qty</th>
                          <th class="text-right" style="width: 80px; padding: 3px 2px; line-height: 1.2;">Amount</th>
                        </tr>
                      </thead>
                      <tbody>
                      <?php foreach ($categoryExpenses as $expense): ?>
                        <tr style="font-size: 10px;">
                          <td style="padding: 2px 4px; line-height: 1.1;"><?php 
                            // Use the display_name field that shows proper names based on category
                            $itemName = '';
                            if (isset($expense->display_name) && !empty($expense->display_name)) {
                              $itemName = $expense->display_name;
                            } elseif (isset($expense->product_name) && !empty($expense->product_name)) {
                              $itemName = $expense->product_name;
                            } elseif (isset($expense->entity_name) && !empty($expense->entity_name)) {
                              $itemName = $expense->entity_name;
                            } elseif (isset($expense->item_name) && !empty($expense->item_name)) {
                              $itemName = $expense->item_name;
                            } elseif (isset($expense->expense_item) && !empty($expense->expense_item)) {
                              $itemName = $expense->expense_item;
                            } elseif (isset($expense->description) && !empty($expense->description)) {
                              $itemName = $expense->description;
                            } elseif (isset($expense->expense_name) && !empty($expense->expense_name)) {
                              $itemName = $expense->expense_name;
                            } else {
                              $itemName = 'N/A';
                            }
                            echo htmlspecialchars($itemName);
                          ?></td>
                          <td class="text-right" style="padding: 2px; line-height: 1.1;"><?php 
                            // Try different possible fields for rate/price
                            $itemRate = 0;
                            if (isset($expense->rate) && !empty($expense->rate)) {
                              $itemRate = $expense->rate;
                            } elseif (isset($expense->price) && !empty($expense->price)) {
                              $itemRate = $expense->price;
                            } elseif (isset($expense->unit_price) && !empty($expense->unit_price)) {
                              $itemRate = $expense->unit_price;
                            } elseif (isset($expense->item_rate) && !empty($expense->item_rate)) {
                              $itemRate = $expense->item_rate;
                            } elseif (isset($expense->product_price) && !empty($expense->product_price)) {
                              $itemRate = $expense->product_price;
                            } elseif (isset($expense->amount) && isset($expense->quantity) && $expense->quantity > 0) {
                              $itemRate = $expense->amount / $expense->quantity;
                            }
                            echo number_format($itemRate, 2);
                          ?></td>
                          <td class="text-right" style="padding: 2px; line-height: 1.1;"><?php echo number_format($expense->quantity ?? 1, 2); ?></td>
                          <td class="text-right" style="padding: 2px; line-height: 1.1;"><strong><?php echo number_format($expense->total_amount ?? 0, 2); ?></strong></td>
                        </tr>
                      <?php endforeach; ?>
                    </tbody>
                      <tfoot>
                        <tr style="background-color: #f8f9fa; font-weight: bold; font-size: 10px;">
                          <th colspan="3" class="text-right" style="padding: 3px 4px; border: 1px solid #ddd; line-height: 1.1;">Category Total:</th>
                          <th class="text-right" style="padding: 3px 2px; border: 1px solid #ddd; line-height: 1.1;"><?php echo number_format($categoryTotal, 2); ?></th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                </div>
                <?php endif; ?>
              <?php endforeach; ?>
            <?php else: ?>
              <div class="no-data">
                No categorized expenses to display.
                <?php if (!empty($expenses)): ?>
                  <br><small>DEBUG: Found <?php echo count($expenses); ?> raw expense records</small>
                  
                  <!-- Show raw expenses for debugging -->
                  <div style="margin-top: 15px; background: #f8f9fa; padding: 10px; border-radius: 4px;">
                    <strong>Raw Expense Data (Debug):</strong>
                    <table class="table table-bordered" style="font-size: 10px; margin-top: 10px;">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>Category</th>
                          <th>Entity</th>
                          <th>Amount</th>
                          <th>Created</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach (array_slice($expenses, 0, 10) as $exp): ?>
                          <tr>
                            <td><?php echo $exp->expense_id ?? 'N/A'; ?></td>
                            <td><?php echo $exp->category_name ?? ($exp->category ?? 'N/A'); ?></td>
                            <td><?php echo $exp->entity_name ?? 'N/A'; ?></td>
                            <td><?php echo $exp->total_amount ?? ($exp->amount ?? 'N/A'); ?></td>
                            <td><?php echo isset($exp->created_at) ? date('Y-m-d H:i', strtotime($exp->created_at)) : 'N/A'; ?></td>
                          </tr>
                        <?php endforeach; ?>
                        <?php if (count($expenses) > 10): ?>
                          <tr><td colspan="5">... and <?php echo count($expenses) - 10; ?> more records</td></tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered" style="margin-top: 10px; font-size: 11px;">
              <tbody>
                <tr style="background-color: #d1ecf1; font-weight: bold; font-size: 11px;">
                  <td class="text-right" style="padding: 4px 6px; line-height: 1.1;">Grand Total Expenses:</td>
                  <td class="text-right" style="width: 100px; padding: 4px 6px; line-height: 1.1;"><span id="reportGrandTotal"><?php 
                    // Calculate grand total by summing all category totals
                    $calculatedGrandTotal = 0;
                    if (!empty($expensesByCategory)) {
                      foreach ($expensesByCategory as $categoryName => $categoryTotal) {
                        $calculatedGrandTotal += $categoryTotal;
                      }
                    }
                    echo number_format($calculatedGrandTotal, 2);
                  ?></span></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
  <!-- /printArea -->
  <!-- ========================= Cancelled Orders Details ========================= -->
  <div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
      <div class="printable-section" style="background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:15px; margin:10px 0;">
        <div class="report-header">
          <h4 class="m-b-0" style="color: #d9534f;">❌ Cancelled Orders Details</h4>
          <p class="report-meta m-b-0">
            <small>Detailed List of Cancelled Orders</small>
          </p>
        </div>
        <div class="panel panel-danger">
          <div class="panel-body">
            <?php if (!empty($cancelledOrders) && $totalCancelledOrders > 0): ?>
              <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                  <thead>
                    <tr>
                      <th style="width: 12%;">Order ID</th>
                      <th style="width: 20%;">Customer</th>
                      <th style="width: 15%;">Customer Type</th>
                      <th class="text-right" style="width: 12%;">Value</th>
                      <th style="width: 31%;">Reason</th>
                      <th class="text-center" style="width: 10%;">Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($cancelledOrders as $order): ?>
                    <tr>
                      <td>
                        <a href="<?php echo base_url("ordermanage/order/orderdetails/$order->order_id") ?>" class="btn btn-link btn-sm" style="padding: 0; color: #337ab7;">
                          <strong><?php echo htmlspecialchars($order->order_id); ?></strong>
                        </a>
                      </td>
                      <td>
                        <?php echo htmlspecialchars($order->customer_name ?: 'Walk-in Customer'); ?>
                      </td>
                      <td>
                        <span class="label label-default"><?php echo htmlspecialchars($order->customer_type ?: 'Regular'); ?></span>
                      </td>
                      <td class="text-right">
                        <span class="text-danger"><strong><?php echo number_format($order->totalamount, 2); ?></strong></span>
                      </td>
                      <td>
                        <span class="text-muted" style="font-style: italic;">
                          <?php echo htmlspecialchars($order->cancel_reason ?: 'No reason provided'); ?>
                        </span>
                      </td>
                      <td class="text-center">
                        <small><?php echo !empty($order->order_time) ? date('H:i', strtotime($order->order_time)) : 'N/A'; ?></small>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                      <td colspan="3"><strong>Total Cancelled Orders</strong></td>
                      <td class="text-right text-danger"><strong><?php echo number_format($totalCancelledValue, 2); ?></strong></td>
                      <td colspan="2" class="text-center"><strong><?php echo $totalCancelledOrders; ?> orders</strong></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            <?php else: ?>
              <div class="text-center text-success">
                <h4>✅ No Cancelled Orders Today!</h4>
                <p>Excellent customer satisfaction performance.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================= Employee Orders Details ========================= -->
  <div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
      <div class="printable-section" style="background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:15px; margin:10px 0;">
        <div class="report-header">
          <h4 class="m-b-0" style="color: #f0ad4e;">👥 Employee Orders Details</h4>
          <p class="report-meta m-b-0">
            <small>Detailed List of Employee Orders</small>
          </p>
        </div>
        <div class="panel panel-warning">
          <div class="panel-body">
            <?php if (!empty($employeeOrders) && $totalEmployeeOrders > 0): ?>
              <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                  <thead>
                    <tr>
                      <th style="width: 10%;">Order ID</th>
                      <th style="width: 20%;">Employee Name</th>
                      <th style="width: 35%;">Items Ordered</th>
                      <th style="width: 20%;">Notes</th>
                      <th class="text-right" style="width: 10%;">Value</th>
                      <th class="text-center" style="width: 5%;">Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($employeeOrders as $order): ?>
                    <tr>
                      <td>
                        <a href="<?php echo base_url("ordermanage/order/orderdetails/$order->order_id") ?>" class="btn btn-link btn-sm" style="padding: 0; color: #337ab7;">
                          <strong><?php echo htmlspecialchars($order->order_id); ?></strong>
                        </a>
                      </td>
                      <td>
                        <?php echo htmlspecialchars($order->customer_name ?: 'Employee'); ?>
                      </td>
                      <td>
                        <small>
                        <?php 
                        if (!empty($order->order_items)) {
                          echo '<ul style="margin: 0; padding-left: 15px; list-style: disc;">';
                          foreach ($order->order_items as $item) {
                            $itemName = $item->ProductName ?? 'Unknown Item';
                            if (!empty($item->variantName)) {
                              $itemName .= ' (' . $item->variantName . ')';
                            }
                            echo '<li>' . htmlspecialchars($item->menuqty . 'x ' . $itemName) . '</li>';
                          }
                          echo '</ul>';
                        } else {
                          echo 'No items found';
                        }
                        ?>
                        </small>
                      </td>
                      <td>
                        <small class="text-muted" style="font-style: italic;">
                          <?php echo htmlspecialchars($order->customer_note ?: 'No notes'); ?>
                        </small>
                      </td>
                      <td class="text-right">
                        <span class="text-warning"><strong><?php echo number_format($order->totalamount, 2); ?></strong></span>
                      </td>
                      <td class="text-center">
                        <small><?php echo !empty($order->order_time) ? date('H:i', strtotime($order->order_time)) : 'N/A'; ?></small>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                      <td colspan="4"><strong>Total Employee Orders</strong></td>
                      <td class="text-right text-warning"><strong><?php echo number_format($totalEmployeeOrdersValue, 2); ?></strong></td>
                      <td class="text-center"><strong><?php echo $totalEmployeeOrders; ?> orders</strong></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            <?php else: ?>
              <div class="text-center text-muted">
                <h4>📋 No Employee Orders Today</h4>
                <p>No employee orders were placed during this period.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================= Guest Orders Details ========================= -->
  <div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
      <div class="printable-section" style="background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:15px; margin:10px 0;">
        <div class="report-header">
          <h4 class="m-b-0" style="color: #5bc0de;">🏨 Guest Orders Details</h4>
          <p class="report-meta m-b-0">
            <small>Detailed List of Guest Orders</small>
          </p>
        </div>
        <div class="panel panel-info">
          <div class="panel-body">
            <?php if (!empty($guestOrders) && $totalGuestOrders > 0): ?>
              <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                  <thead>
                    <tr>
                      <th style="width: 10%;">Order ID</th>
                      <th style="width: 20%;">Guest Name</th>
                      <th style="width: 35%;">Items Ordered</th>
                      <th style="width: 20%;">Notes</th>
                      <th class="text-right" style="width: 10%;">Value</th>
                      <th class="text-center" style="width: 5%;">Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($guestOrders as $order): ?>
                    <tr>
                      <td>
                        <a href="<?php echo base_url("ordermanage/order/orderdetails/$order->order_id") ?>" class="btn btn-link btn-sm" style="padding: 0; color: #337ab7;">
                          <strong><?php echo htmlspecialchars($order->order_id); ?></strong>
                        </a>
                      </td>
                      <td>
                        <?php echo htmlspecialchars($order->customer_name ?: 'Guest'); ?>
                      </td>
                      <td>
                        <small>
                        <?php 
                        if (!empty($order->order_items)) {
                          echo '<ul style="margin: 0; padding-left: 15px; list-style: disc;">';
                          foreach ($order->order_items as $item) {
                            $itemName = $item->ProductName ?? 'Unknown Item';
                            if (!empty($item->variantName)) {
                              $itemName .= ' (' . $item->variantName . ')';
                            }
                            echo '<li>' . htmlspecialchars($item->menuqty . 'x ' . $itemName) . '</li>';
                          }
                          echo '</ul>';
                        } else {
                          echo 'No items found';
                        }
                        ?>
                        </small>
                      </td>
                      <td>
                        <small class="text-muted" style="font-style: italic;">
                          <?php echo htmlspecialchars($order->customer_note ?: 'No notes'); ?>
                        </small>
                      </td>
                      <td class="text-right">
                        <span class="text-info"><strong><?php echo number_format($order->totalamount, 2); ?></strong></span>
                      </td>
                      <td class="text-center">
                        <small><?php echo !empty($order->order_time) ? date('H:i', strtotime($order->order_time)) : 'N/A'; ?></small>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                      <td colspan="4"><strong>Total Guest Orders</strong></td>
                      <td class="text-right text-info"><strong><?php echo number_format($totalGuestOrdersValue, 2); ?></strong></td>
                      <td class="text-center"><strong><?php echo $totalGuestOrders; ?> orders</strong></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            <?php else: ?>
              <div class="text-center text-muted">
                <h4>🏨 No Guest Orders Today</h4>
                <p>No guest orders were placed during this period.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================= Charity Orders Details ========================= -->
  <div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
      <div class="printable-section" style="background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:15px; margin:10px 0;">
        <div class="report-header">
          <h4 class="m-b-0" style="color: #5cb85c;">❤️ Charity Orders Details</h4>
          <p class="report-meta m-b-0">
            <small>Detailed List of Charity Orders</small>
          </p>
        </div>
        <div class="panel panel-success">
          <div class="panel-body">
            <?php if (!empty($charityOrders) && $totalCharityOrders > 0): ?>
              <div class="table-responsive">
                <table class="table table-bordered table-condensed">
                  <thead>
                    <tr>
                      <th style="width: 10%;">Order ID</th>
                      <th style="width: 20%;">Recipient Name</th>
                      <th style="width: 35%;">Items Ordered</th>
                      <th style="width: 20%;">Notes</th>
                      <th class="text-right" style="width: 10%;">Value</th>
                      <th class="text-center" style="width: 5%;">Time</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($charityOrders as $order): ?>
                    <tr>
                      <td>
                        <a href="<?php echo base_url("ordermanage/order/orderdetails/$order->order_id") ?>" class="btn btn-link btn-sm" style="padding: 0; color: #337ab7;">
                          <strong><?php echo htmlspecialchars($order->order_id); ?></strong>
                        </a>
                      </td>
                      <td>
                        <?php echo htmlspecialchars($order->customer_name ?: 'Charity Recipient'); ?>
                      </td>
                      <td>
                        <small>
                        <?php 
                        if (!empty($order->order_items)) {
                          echo '<ul style="margin: 0; padding-left: 15px; list-style: disc;">';
                          foreach ($order->order_items as $item) {
                            $itemName = $item->ProductName ?? 'Unknown Item';
                            if (!empty($item->variantName)) {
                              $itemName .= ' (' . $item->variantName . ')';
                            }
                            echo '<li>' . htmlspecialchars($item->menuqty . 'x ' . $itemName) . '</li>';
                          }
                          echo '</ul>';
                        } else {
                          echo 'No items found';
                        }
                        ?>
                        </small>
                      </td>
                      <td>
                        <small class="text-muted" style="font-style: italic;">
                          <?php echo htmlspecialchars($order->customer_note ?: 'No notes'); ?>
                        </small>
                      </td>
                      <td class="text-right">
                        <span class="text-success"><strong><?php echo number_format($order->totalamount, 2); ?></strong></span>
                      </td>
                      <td class="text-center">
                        <small><?php echo !empty($order->order_time) ? date('H:i', strtotime($order->order_time)) : 'N/A'; ?></small>
                      </td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                  <tfoot>
                    <tr style="background-color: #f9f9f9; font-weight: bold;">
                      <td colspan="4"><strong>Total Charity Orders</strong></td>
                      <td class="text-right text-success"><strong><?php echo number_format($totalCharityOrdersValue, 2); ?></strong></td>
                      <td class="text-center"><strong><?php echo $totalCharityOrders; ?> orders</strong></td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            <?php else: ?>
              <div class="text-center text-muted">
                <h4>❤️ No Charity Orders Today</h4>
                <p>No charity orders were placed during this period.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  

  <div class="row" class="no-print" style="margin-top: 20px;">
    <div class="col-md-12">
      <div class="" style="background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:15px; margin:10px 0;">
        <div class="report-header">
          <h4 class="m-b-0" style="color: #337ab7;">🍽️ Sales by Items - All Customer Types</h4>
          <p class="report-meta m-b-0">
            <small>Detailed breakdown of items sold across all customer types</small>
          </p>
        </div>
        
        <div class="row">
          <!-- Left Column: Item Sales Table -->
          <div class="col-md-12" style="float: left; width: 100%;">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h5 style="margin: 0;">Item Sales Analysis</h5>
                <small>Shows quantity sold and revenue for each item by customer type</small>
              </div>
              <div class="panel-body" style="padding: 10px;">
                <?php if (!empty($itemSalesData)): ?>
                  <div class="table-responsive">
                    <table class="table table-bordered table-striped table-condensed" style="font-size: 14px;">
                      <thead style="background-color: #f5f5f5;">
                        <tr>
                          <th rowspan="2" style="vertical-align: middle; width: 30%; font-size: 14px; padding: 8px; color: #000;">Item Name</th>
                          <th rowspan="2" class="text-center" style="vertical-align: middle; width: 10%; font-size: 14px; padding: 8px; color: #000;">Total Qty</th>
                          <th rowspan="2" class="text-right" style="vertical-align: middle; width: 12%; font-size: 14px; padding: 8px; color: #000;">Total Revenue</th>
                          <th colspan="4" class="text-center" style="border-bottom: 2px solid #000; font-size: 14px; padding: 8px; color: #000;">Customer Types</th>
                        </tr>
                        <tr style="background-color: #e8f4fd;">
                          <th class="text-center" style="width: 12%; color: #000; font-size: 12px; padding: 6px;">Regular<br><small style="color: #000;">(Qty/Rev)</small></th>
                          <th class="text-center" style="width: 12%; color: #b8860b; font-size: 12px; padding: 6px;">Employee<br><small style="color: #b8860b;">(Qty/Rev)</small></th>
                          <th class="text-center" style="width: 12%; color: #2e5d94; font-size: 12px; padding: 6px;">Guest<br><small style="color: #2e5d94;">(Qty/Rev)</small></th>
                          <th class="text-center" style="width: 12%; color: #2d5a2d; font-size: 12px; padding: 6px;">Charity<br><small style="color: #2d5a2d;">(Qty/Rev)</small></th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php 
                        $grandTotalQty = 0;
                        $grandTotalRevenue = 0;
                        $grandRegularQty = 0; $grandRegularRevenue = 0;
                        $grandEmployeeQty = 0; $grandEmployeeRevenue = 0;
                        $grandGuestQty = 0; $grandGuestRevenue = 0;
                        $grandCharityQty = 0; $grandCharityRevenue = 0;
                        
                        foreach ($itemSalesData as $item): 
                          $totalItemQty = ($item->regular_qty ?? 0) + ($item->employee_qty ?? 0) + ($item->guest_qty ?? 0) + ($item->charity_qty ?? 0);
                          $totalItemRevenue = ($item->regular_revenue ?? 0) + ($item->employee_revenue ?? 0) + ($item->guest_revenue ?? 0) + ($item->charity_revenue ?? 0);
                          
                          $grandTotalQty += $totalItemQty;
                          $grandTotalRevenue += $totalItemRevenue;
                          $grandRegularQty += ($item->regular_qty ?? 0);
                          $grandRegularRevenue += ($item->regular_revenue ?? 0);
                          $grandEmployeeQty += ($item->employee_qty ?? 0);
                          $grandEmployeeRevenue += ($item->employee_revenue ?? 0);
                          $grandGuestQty += ($item->guest_qty ?? 0);
                          $grandGuestRevenue += ($item->guest_revenue ?? 0);
                          $grandCharityQty += ($item->charity_qty ?? 0);
                          $grandCharityRevenue += ($item->charity_revenue ?? 0);
                        ?>
                        <tr style="font-size: 13px;">
                          <td style="padding: 7px;">
                            <strong style="color: #000; font-size: 13px;"><?php echo htmlspecialchars($item->item_name ?? 'Unknown Item'); ?></strong>
                            <?php if (!empty($item->variant_name)): ?>
                              - <small style="color: #333; font-size: 11px;"><?php echo htmlspecialchars($item->variant_name); ?></small>
                            <?php endif; ?>
                          </td>
                          <td class="text-center" style="padding: 7px;">
                            <span class="badge badge-primary" style="font-size: 12px; background-color: #2c3e50; color: #fff; border: 1px solid #000;"><?php echo number_format($totalItemQty); ?></span>
                          </td>
                          <td class="text-right" style="padding: 7px;">
                            <strong style="color: #000; font-size: 13px;"><?php echo number_format($totalItemRevenue, 2); ?></strong>
                          </td>
                          <td class="text-center" style="padding: 6px;">
                            <div style="color: #000; font-size: 12px;">
                              <strong><?php echo number_format($item->regular_qty ?? 0); ?></strong><br>
                              <small style="font-size: 10px; color: #333;"><?php echo number_format($item->regular_revenue ?? 0, 2); ?></small>
                            </div>
                          </td>
                          <td class="text-center" style="padding: 6px;">
                            <div style="color: #b8860b; font-size: 12px;">
                              <strong><?php echo number_format($item->employee_qty ?? 0); ?></strong><br>
                              <small style="font-size: 10px; color: #b8860b;"><?php echo number_format($item->employee_revenue ?? 0, 2); ?></small>
                            </div>
                          </td>
                          <td class="text-center" style="padding: 6px;">
                            <div style="color: #2e5d94; font-size: 12px;">
                              <strong><?php echo number_format($item->guest_qty ?? 0); ?></strong><br>
                              <small style="font-size: 10px; color: #2e5d94;"><?php echo number_format($item->guest_revenue ?? 0, 2); ?></small>
                            </div>
                          </td>
                          <td class="text-center" style="padding: 6px;">
                            <div style="color: #2d5a2d; font-size: 12px;">
                              <strong><?php echo number_format($item->charity_qty ?? 0); ?></strong><br>
                              <small style="font-size: 10px; color: #2d5a2d;"><?php echo number_format($item->charity_revenue ?? 0, 2); ?></small>
                            </div>
                          </td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                      <tfoot style="background-color: #f0f0f0; font-weight: bold; border-top: 2px solid #000;">
                        <tr style="font-size: 13px;">
                          <th style="padding: 8px; font-size: 14px; color: #000;">Grand Totals</th>
                          <th class="text-center" style="padding: 8px;">
                            <span class="badge badge-primary" style="font-size: 13px; background-color: #000; color: #fff; border: 1px solid #000;"><?php echo number_format($grandTotalQty); ?></span>
                          </th>
                          <th class="text-right" style="font-size: 14px; color: #000; padding: 8px;">
                            <?php echo number_format($grandTotalRevenue, 2); ?>
                          </th>
                          <th class="text-center" style="padding: 7px;">
                            <div style="color: #000; font-size: 12px;">
                              <strong><?php echo number_format($grandRegularQty); ?></strong><br>
                              <small style="font-size: 10px; color: #333;"><?php echo number_format($grandRegularRevenue, 2); ?></small>
                            </div>
                          </th>
                          <th class="text-center" style="padding: 7px;">
                            <div style="color: #b8860b; font-size: 12px;">
                              <strong><?php echo number_format($grandEmployeeQty); ?></strong><br>
                              <small style="font-size: 10px; color: #b8860b;"><?php echo number_format($grandEmployeeRevenue, 2); ?></small>
                            </div>
                          </th>
                          <th class="text-center" style="padding: 7px;">
                            <div style="color: #2e5d94; font-size: 12px;">
                              <strong><?php echo number_format($grandGuestQty); ?></strong><br>
                              <small style="font-size: 10px; color: #2e5d94;"><?php echo number_format($grandGuestRevenue, 2); ?></small>
                            </div>
                          </th>
                          <th class="text-center" style="padding: 7px;">
                            <div style="color: #2d5a2d; font-size: 12px;">
                              <strong><?php echo number_format($grandCharityQty); ?></strong><br>
                              <small style="font-size: 10px; color: #2d5a2d;"><?php echo number_format($grandCharityRevenue, 2); ?></small>
                            </div>
                          </th>
                        </tr>
                      </tfoot>
                    </table>
                  </div>
                <?php else: ?>
                  <div class="text-center text-muted" style="padding: 30px;">
                    <h5>📊 No Item Sales Data</h5>
                    <p>No items were sold during this period.</p>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
          <div class="clearfix"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- ========================= Kitchen Sales Report ========================= -->
  <div class="row" style="margin-top: 20px;">
    <div class="col-md-12">
      <div id="kitchenSalesArea" class="printable-section" style="background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:15px; margin:10px 0; max-width:100%; overflow:hidden; min-height:auto;">
        <div class="report-header">
          <h3 class="m-b-0">🍴 Kitchen Sales Report</h3>
          <p class="report-meta m-b-0">
            Date: <span><?php echo date('Y-m-d'); ?></span>
            <span style="margin-left:10px;">Sales by Kitchen (Employee • Guest • Charity • Regular)</span>
          </p>
        </div>
        <div class="panel panel-success">
          <div class="panel-heading">
            Kitchen Performance Analysis
            <small style="margin-left: 15px; font-weight: normal; opacity: 0.8;">
              (Regular Customers Only - Excludes Employee, Guest & Charity)
            </small>
          </div>
          <div class="panel-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Kitchen</th>
                    <th class="text-center">Top Items Sold</th>
                    <th class="text-center">Regular<br><small>(Items)</small></th>
                    <th class="text-center">Employee<br><small>(Qty)</small></th>
                    <th class="text-center">Guest<br><small>(Qty)</small></th>
                    <th class="text-center">Charity<br><small>(Qty)</small></th>
                    <th class="text-right">Sales*<br><small>(Regular Only)</small></th>
                    <th class="text-center">Labor Cost</th>
                    <th class="text-center">Ingredients Cost</th>
                    <th class="text-right">Total Costs</th>
                    <th class="text-right">Net Profit*</th>
                    <th class="text-center">Details</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $totalKitchenSalesAmount = $totalKitchenSales ?? 0;
                  $grandTotalCosts = 0;
                  $grandNetProfit = 0;
                  
                  if (!empty($kitchenSalesData)):
                    foreach ($kitchenSalesData as $kitchen):
                      
                      // Get top 3 items sold
                      $topItems = array();
                      if (!empty($kitchen->items_sold)) {
                        $topItems = array_slice($kitchen->items_sold, 0, 3);
                      }
                  ?>
                    <tr>
                      <td><strong><?php echo htmlspecialchars($kitchen->kitchen_name); ?></strong></td>
                      <td style="font-size: 11px;">
                        <?php if (!empty($topItems)): ?>
                          <?php foreach ($topItems as $item): ?>
                            <div><?php echo htmlspecialchars($item->product_name); ?> (<?php echo $item->quantity_sold; ?>)</div>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <span class="text-muted">No items sold</span>
                        <?php endif; ?>
                      </td>
                      <td class="text-center">
                        <strong><?php echo number_format($kitchen->total_items_count ?? 0); ?></strong>
                        <br><small class="text-muted">Regular</small>
                      </td>
                      <td class="text-center">
                        <strong style="color: #f0ad4e;"><?php echo number_format($kitchen->customer_types['employee']['qty'] ?? 0); ?></strong>
                        <br><small class="text-muted">Employee</small>
                      </td>
                      <td class="text-center">
                        <strong style="color: #5bc0de;"><?php echo number_format($kitchen->customer_types['guest']['qty'] ?? 0); ?></strong>
                        <br><small class="text-muted">Guest</small>
                      </td>
                      <td class="text-center">
                        <strong style="color: #d9534f;"><?php echo number_format($kitchen->customer_types['charity']['qty'] ?? 0); ?></strong>
                        <br><small class="text-muted">Charity</small>
                      </td>
                      <td class="text-right">
                        <strong style="color: #5cb85c;"><?php echo number_format($kitchen->total_sales, 2); ?></strong>
                        <br><small class="text-muted">Regular Only</small>
                      </td>
                      <td class="text-center">
                        <?php 
                        // Calculate Labor Costs (Employee expenses)
                        $laborTotal = 0;
                        $employeeCount = 0;
                        if (!empty($kitchen->kitchen_expenses['employee_expenses'])) {
                          foreach ($kitchen->kitchen_expenses['employee_expenses'] as $expense) {
                            $laborTotal += $expense->total_amount;
                          }
                          $employeeCount = count($kitchen->kitchen_expenses['employee_expenses']);
                        }
                        ?>
                        <span style="color: #d9534f;"><?php echo number_format($laborTotal, 2); ?></span>
                        <br><small>(<?php echo $employeeCount; ?> staff)</small>
                      </td>
                      <td class="text-center">
                        <?php 
                        // Calculate Ingredients Costs (Product + Entity expenses)
                        $ingredientsTotal = 0;
                        $ingredientCount = 0;
                        if (!empty($kitchen->kitchen_expenses['product_expenses'])) {
                          foreach ($kitchen->kitchen_expenses['product_expenses'] as $expense) {
                            $ingredientsTotal += $expense->total_amount;
                          }
                          $ingredientCount += count($kitchen->kitchen_expenses['product_expenses']);
                        }
                        if (!empty($kitchen->kitchen_expenses['entity_expenses'])) {
                          foreach ($kitchen->kitchen_expenses['entity_expenses'] as $expense) {
                            $ingredientsTotal += $expense->total_amount;
                          }
                          $ingredientCount += count($kitchen->kitchen_expenses['entity_expenses']);
                        }
                        ?>
                        <span style="color: #5cb85c;"><?php echo number_format($ingredientsTotal, 2); ?></span>
                        <br><small>(<?php echo $ingredientCount; ?> items)</small>
                      </td>
                      <td class="text-right" style="color: #d9534f;">
                        <?php $totalCosts = $laborTotal + $ingredientsTotal; ?>
                        <strong><?php echo number_format($totalCosts, 2); ?></strong>
                      </td>
                      <td class="text-right <?php $netProfit = ($kitchen->total_sales ?? 0) - $totalCosts; echo ($netProfit >= 0) ? 'text-success' : 'text-danger'; ?>">
                        <strong><?php echo number_format($netProfit, 2); ?></strong>
                      </td>
                      <td class="text-center">
                        <button type="button" class="btn btn-xs btn-info" data-toggle="modal" data-target="#kitchenDetail<?php echo $kitchen->kitchen_id; ?>">
                          <i class="fa fa-eye"></i> View
                        </button>
                      </td>
                    </tr>
                  <?php 
                    // Add to grand totals
                    $grandTotalCosts += $totalCosts;
                    $grandNetProfit += $netProfit;
                    
                    endforeach;
                  else:
                  ?>
                    <tr>
                      <td colspan="12" class="text-center text-muted">No kitchen data available</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
                <tfoot>
                  <tr style="background-color: #dff0d8; font-weight: bold;">
                    <th>Totals</th>
                    <th class="text-center">All Items</th>
                    <th class="text-center">
                      <?php 
                      $totalItemsCount = 0;
                      if (!empty($kitchenSalesData)) {
                        foreach ($kitchenSalesData as $kitchen) {
                          $totalItemsCount += $kitchen->total_items_count ?? 0;
                        }
                      }
                      echo number_format($totalItemsCount);
                      ?>
                    </th>
                    <th class="text-center" style="color: #f0ad4e;">
                      <?php 
                      $totalEmployeeQty = 0;
                      if (!empty($kitchenSalesData)) {
                        foreach ($kitchenSalesData as $kitchen) {
                          $totalEmployeeQty += $kitchen->customer_types['employee']['qty'] ?? 0;
                        }
                      }
                      echo number_format($totalEmployeeQty);
                      ?>
                    </th>
                    <th class="text-center" style="color: #5bc0de;">
                      <?php 
                      $totalGuestQty = 0;
                      if (!empty($kitchenSalesData)) {
                        foreach ($kitchenSalesData as $kitchen) {
                          $totalGuestQty += $kitchen->customer_types['guest']['qty'] ?? 0;
                        }
                      }
                      echo number_format($totalGuestQty);
                      ?>
                    </th>
                    <th class="text-center" style="color: #d9534f;">
                      <?php 
                      $totalCharityQty = 0;
                      if (!empty($kitchenSalesData)) {
                        foreach ($kitchenSalesData as $kitchen) {
                          $totalCharityQty += $kitchen->customer_types['charity']['qty'] ?? 0;
                        }
                      }
                      echo number_format($totalCharityQty);
                      ?>
                    </th>
                    <th class="text-right" style="color: #5cb85c;"><?php echo number_format($totalKitchenSalesAmount, 2); ?></th>
                    <th class="text-center" style="color: #d9534f;">
                      <?php 
                      $totalLaborCosts = 0;
                      if (!empty($kitchenSalesData)) {
                        foreach ($kitchenSalesData as $kitchen) {
                          // Calculate Labor Costs (Employee expenses)
                          if (!empty($kitchen->kitchen_expenses['employee_expenses'])) {
                            foreach ($kitchen->kitchen_expenses['employee_expenses'] as $expense) {
                              $totalLaborCosts += $expense->total_amount;
                            }
                          }
                        }
                      }
                      echo number_format($totalLaborCosts, 2);
                      ?>
                    </th>
                    <th class="text-center" style="color: #5cb85c;">
                      <?php 
                      $totalIngredientCosts = 0;
                      if (!empty($kitchenSalesData)) {
                        foreach ($kitchenSalesData as $kitchen) {
                          // Calculate Ingredients Costs (Product + Entity expenses)
                          if (!empty($kitchen->kitchen_expenses['product_expenses'])) {
                            foreach ($kitchen->kitchen_expenses['product_expenses'] as $expense) {
                              $totalIngredientCosts += $expense->total_amount;
                            }
                          }
                          if (!empty($kitchen->kitchen_expenses['entity_expenses'])) {
                            foreach ($kitchen->kitchen_expenses['entity_expenses'] as $expense) {
                              $totalIngredientCosts += $expense->total_amount;
                            }
                          }
                        }
                      }
                      echo number_format($totalIngredientCosts, 2);
                      ?>
                    </th>
                    <th class="text-right" style="color: #d9534f;"><?php echo number_format($grandTotalCosts, 2); ?></th>
                    <th class="text-right <?php echo ($grandNetProfit >= 0) ? 'text-success' : 'text-danger'; ?>"><?php echo number_format($grandNetProfit, 2); ?></th>
                    <th class="text-center">📊</th>
                  </tr>
                </tfoot>
              </table>
            </div>
            
            <!-- Kitchen Performance Summary -->
            <div class="row" style="margin-top: 15px;">
              <div class="col-md-2">
                <div class="text-center">
                  <strong>Total Sales</strong><br>
                  <small style="color: #666;">(Regular Only)</small><br>
                  <span class="text-success" style="font-size: 14px;"><?php echo number_format($totalKitchenSalesAmount, 2); ?></span>
                </div>
              </div>
              <div class="col-md-2">
                <div class="text-center">
                  <strong>Total Costs</strong><br>
                  <span class="text-danger" style="font-size: 14px;"><?php echo number_format($grandTotalCosts, 2); ?></span>
                </div>
              </div>
              <div class="col-md-2">
                <div class="text-center">
                  <strong>Net Profit</strong><br>
                  <span class="<?php echo ($grandNetProfit >= 0) ? 'text-success' : 'text-danger'; ?>" style="font-size: 14px;">
                    <?php echo number_format($grandNetProfit, 2); ?>
                  </span>
                </div>
              </div>
              <div class="col-md-2">
                <div class="text-center">
                  <strong>Employee Sales</strong><br>
                  <span class="text-info" style="font-size: 14px;">
                    <?php 
                    $totalEmployeeSales = 0;
                    if (!empty($kitchenSalesData)) {
                      foreach ($kitchenSalesData as $kitchen) {
                        $totalEmployeeSales += $kitchen->customer_types['employee']['amount'] ?? 0;
                      }
                    }
                    echo number_format($totalEmployeeSales, 2); 
                    ?>
                  </span>
                </div>
              </div>
              <div class="col-md-2">
                <div class="text-center">
                  <strong>Guest Sales</strong><br>
                  <span class="text-warning" style="font-size: 14px;">
                    <?php 
                    $totalGuestSales = 0;
                    if (!empty($kitchenSalesData)) {
                      foreach ($kitchenSalesData as $kitchen) {
                        $totalGuestSales += $kitchen->customer_types['guest']['amount'] ?? 0;
                      }
                    }
                    echo number_format($totalGuestSales, 2); 
                    ?>
                  </span>
                </div>
              </div>
              <div class="col-md-2">
                <div class="text-center">
                  <strong>Charity Sales</strong><br>
                  <span class="text-primary" style="font-size: 14px;">
                    <?php 
                    $totalCharitySales = 0;
                    if (!empty($kitchenSalesData)) {
                      foreach ($kitchenSalesData as $kitchen) {
                        $totalCharitySales += $kitchen->customer_types['charity']['amount'] ?? 0;
                      }
                    }
                    echo number_format($totalCharitySales, 2); 
                    ?>
                  </span>
                </div>
              </div>
            </div>
            
            <!-- Explanation Note -->
            <div class="alert alert-info" style="margin-top: 15px; margin-bottom: 0;">
              <small>
                <strong>Note:</strong> 
                * Sales and Net Profit calculations include <strong>Regular customers only</strong>. 
                Employee, Guest, and Charity quantities are shown for reference but <strong>excluded from profit calculations</strong> 
                as they typically represent complimentary, discounted, or donated meals.
              </small>
            </div>
            
            <!-- Kitchen Detail Modals -->
            <?php if (!empty($kitchenSalesData)): ?>
              <?php foreach ($kitchenSalesData as $kitchen): ?>
                <!-- Modal for Kitchen <?php echo $kitchen->kitchen_id; ?> -->
                <div class="modal fade" id="kitchenDetail<?php echo $kitchen->kitchen_id; ?>" tabindex="-1" role="dialog">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">🍴 <?php echo htmlspecialchars($kitchen->kitchen_name); ?> - Detailed Analysis</h4>
                      </div>
                      <div class="modal-body">
                        <div class="row">
                          <!-- Items Sold Details -->
                          <div class="col-md-12">
                            <h5>🍽️ Items Sold Today</h5>
                            <?php if (!empty($kitchen->items_sold)): ?>
                            <table class="table table-condensed table-striped">
                              <thead>
                                <tr>
                                  <th>Item Name</th>
                                  <th class="text-center">Quantity Sold</th>
                                  <th class="text-right">Unit Price</th>
                                  <th class="text-center">Customer Type</th>
                                  <th class="text-right">Total Amount</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php foreach ($kitchen->items_sold as $item): ?>
                                <tr>
                                  <td><strong><?php echo htmlspecialchars($item->product_name); ?></strong></td>
                                  <td class="text-center"><span class="badge"><?php echo $item->quantity_sold; ?></span></td>
                                  <td class="text-right"><?php echo number_format($item->unit_price, 2); ?></td>
                                  <td class="text-center"><small><?php echo htmlspecialchars($item->customer_type); ?></small></td>
                                  <td class="text-right"><strong><?php echo number_format($item->total_amount, 2); ?></strong></td>
                                </tr>
                                <?php endforeach; ?>
                              </tbody>
                              <tfoot>
                                <tr style="background-color: #dff0d8; font-weight: bold;">
                                  <td>Total</td>
                                  <td class="text-center"><?php echo $kitchen->total_items_count; ?></td>
                                  <td colspan="2"></td>
                                  <td class="text-right"><?php echo number_format($kitchen->total_sales, 2); ?></td>
                                </tr>
                              </tfoot>
                            </table>
                            <?php else: ?>
                            <div class="alert alert-info">No items sold from this kitchen today.</div>
                            <?php endif; ?>
                          </div>
                        </div>
                        
                        <hr>
                      
                        <?php if (!empty($kitchen->direct_expenses['expenses'])): ?>
                        <hr>
                        <div class="row">
                          <div class="col-md-12">
                            <h5>� Direct Expenses</h5>
                            <table class="table table-condensed table-striped">
                              <thead><tr><th>Expense Category</th><th class="text-center">Transactions</th><th class="text-right">Amount</th></tr></thead>
                              <tbody>
                                <?php foreach ($kitchen->direct_expenses['expenses'] as $expense): ?>
                                <tr>
                                  <td><?php echo htmlspecialchars($expense->expense_category); ?></td>
                                  <td class="text-center"><?php echo $expense->transaction_count; ?></td>
                                  <td class="text-right"><?php echo number_format($expense->amount, 2); ?></td>
                                </tr>
                                <?php endforeach; ?>
                              </tbody>
                            </table>
                          </div>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Kitchen Expenses During Cash Register Period -->
                        <?php if (!empty($kitchen->kitchen_expenses) && $kitchen->total_kitchen_expenses > 0): ?>
                        <div class="row">
                          <div class="col-md-12">
                            <h5>💸 Expenses During Cash Register Period</h5>
                            <p class="text-muted">
                              <small>Expenses added between <strong><?php echo date('M d, Y H:i', strtotime($registerinfo->opendate)); ?></strong> and <strong><?php echo date('M d, Y H:i'); ?></strong></small>
                            </p>
                          </div>
                        </div>
                        
                        <div class="row">
                          <!-- Labor Costs -->
                          <div class="col-md-6">
                            <h6>� Labor Costs</h6>
                            <?php if (!empty($kitchen->kitchen_expenses['employee_expenses'])): ?>
                            <table class="table table-condensed table-striped table-sm">
                              <thead>
                                <tr>
                                  <th>Employee</th>
                                  <th class="text-right">Amount</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php 
                                $employeeTotal = 0;
                                foreach ($kitchen->kitchen_expenses['employee_expenses'] as $expense): 
                                  $employeeTotal += $expense->total_amount;
                                ?>
                                <tr>
                                  <td>
                                    <strong><?php echo htmlspecialchars($expense->entity_name ?: 'Unknown'); ?></strong>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($expense->category_name ?: ''); ?></small>
                                  </td>
                                  <td class="text-right">
                                    <strong><?php echo number_format($expense->total_amount, 2); ?></strong>
                                  </td>
                                </tr>
                                <?php endforeach; ?>
                              </tbody>
                              <tfoot>
                                <tr style="background-color: #d9534f; color: white;">
                                  <td><strong>Total</strong></td>
                                  <td class="text-right"><strong><?php echo number_format($employeeTotal, 2); ?></strong></td>
                                </tr>
                              </tfoot>
                            </table>
                            <?php else: ?>
                            <div class="alert alert-info alert-sm">
                              <small>No employee expenses recorded.</small>
                            </div>
                            <?php endif; ?>
                          </div>
                          
                          <!-- Ingredients Cost -->
                          <div class="col-md-6">
                            <h6>🥘 Ingredients Cost</h6>
                            <?php if (!empty($kitchen->kitchen_expenses['product_expenses']) || !empty($kitchen->kitchen_expenses['entity_expenses'])): ?>
                            <table class="table table-condensed table-striped table-sm">
                              <thead>
                                <tr>
                                  <th>Item</th>
                                  <th class="text-center">Type</th>
                                  <th class="text-center">Qty</th>
                                  <th class="text-right">Amount</th>
                                </tr>
                              </thead>
                              <tbody>
                                <?php 
                                $ingredientsTotal = 0;
                                
                                // Display Product Expenses
                                if (!empty($kitchen->kitchen_expenses['product_expenses'])):
                                  foreach ($kitchen->kitchen_expenses['product_expenses'] as $expense): 
                                    $ingredientsTotal += $expense->total_amount;
                                ?>
                                <tr>
                                  <td>
                                    <strong><?php echo htmlspecialchars($expense->product_name ?: 'Unknown Product'); ?></strong>
                                  </td>
                                  <td class="text-center">
                                    <span class="label label-info">Product</span>
                                  </td>
                                  <td class="text-center">
                                    <strong><?php echo number_format($expense->quantity ?: 0, 2); ?></strong>
                                    <br><small><?php echo htmlspecialchars($expense->unit ?: ''); ?></small>
                                  </td>
                                  <td class="text-right">
                                    <strong><?php echo number_format($expense->total_amount, 2); ?></strong>
                                  </td>
                                </tr>
                                <?php endforeach; endif;
                                
                                // Display Entity Expenses
                                if (!empty($kitchen->kitchen_expenses['entity_expenses'])):
                                  foreach ($kitchen->kitchen_expenses['entity_expenses'] as $expense): 
                                    $ingredientsTotal += $expense->total_amount;
                                ?>
                                <tr>
                                  <td>
                                    <strong><?php echo htmlspecialchars($expense->entity_name ?: 'Unknown Entity'); ?></strong>
                                    <br><small class="text-muted"><?php echo htmlspecialchars($expense->category_name ?: ''); ?></small>
                                  </td>
                                  <td class="text-center">
                                    <span class="label label-warning">Entity</span>
                                  </td>
                                  <td class="text-center">
                                    <strong><?php echo number_format($expense->quantity ?: 0, 2); ?></strong>
                                    <br><small><?php echo htmlspecialchars($expense->unit ?: 'items'); ?></small>
                                  </td>
                                  <td class="text-right">
                                    <strong><?php echo number_format($expense->total_amount, 2); ?></strong>
                                  </td>
                                </tr>
                                <?php endforeach; endif; ?>
                              </tbody>
                              <tfoot>
                                <tr style="background-color: #5cb85c; color: white;">
                                  <td><strong>Total Ingredients</strong></td>
                                  <td class="text-center"><strong>-</strong></td>
                                  <td class="text-center"><strong>-</strong></td>
                                  <td class="text-right"><strong><?php echo number_format($ingredientsTotal, 2); ?></strong></td>
                                </tr>
                              </tfoot>
                            </table>
                            <?php else: ?>
                            <div class="alert alert-info alert-sm">
                              <small>No ingredients expenses recorded.</small>
                            </div>
                            <?php endif; ?>
                          </div>
                        </div>
                          
                        <!-- Total Kitchen Expenses Summary -->
                        <div class="row">
                          <div class="col-md-12">
                            <div class="alert alert-warning">
                              <div class="row">
                                <div class="col-md-6">
                                  <strong>Total Kitchen Expenses During Cash Register Period:</strong>
                                </div>
                                <div class="col-md-6 text-right">
                                  <h4 class="margin-0"><strong><?php echo number_format($kitchen->total_kitchen_expenses, 2); ?></strong></h4>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <?php endif; ?>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</div><!-- /container -->

<!-- jQuery + Bootstrap JS (matching todaymanageexpenses) -->
<script src="https://code.jquery.com/jquery-1.12.4.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    $('#btnPrintReport').click(function() {
        printComprehensiveReport();
    });
});

/**
 * Print the comprehensive daily report with all sections
 * This function includes all report sections except Kitchen Sales Report
 * and prevents duplicate content by using proper section identification
 */
function printComprehensiveReport() {
    // Create a new window for printing
    var printWindow = window.open('', '_blank', 'width=1200,height=900,scrollbars=yes');
    
    // Build the complete print HTML with all sections
    var printHTML = `
    <!DOCTYPE html>
    <html>
    <head>
      <title>Daily Comprehensive Report - <?php echo date('Y-m-d'); ?></title>
      <meta charset="utf-8">
      <style>
        body { 
          font-family: Arial, sans-serif; 
          margin: 15px; 
          font-size: 12px;
          line-height: 1.4;
          color: #000;
          background: #fff;
        }
        .print-section { 
          margin-bottom: 20px; 
          page-break-inside: avoid;
          border-bottom: 1px solid #ddd;
          padding-bottom: 10px;
        }
        .section-title {
          font-size: 16px;
          font-weight: bold;
          margin: 15px 0 10px 0;
          color: #2c3e50;
          border-bottom: 2px solid #3498db;
          padding-bottom: 5px;
          text-align: center;
        }
        .report-header h3, .report-header h4 {
          color: #000 !important;
          font-weight: bold;
          margin: 8px 0;
        }
        .report-meta {
          color: #666 !important;
          font-size: 11px;
          margin: 5px 0;
        }
        table { 
          width: 100%; 
          border-collapse: collapse; 
          margin-bottom: 15px;
          font-size: 11px;
        }
        th, td { 
          border: 1px solid #ddd; 
          padding: 6px 8px; 
          text-align: left;
          vertical-align: top;
          color: #000;
        }
        th { 
          background-color: #f8f9fa; 
          font-weight: bold;
          color: #495057;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-success { color: #28a745 !important; }
        .text-danger { color: #dc3545 !important; }
        .text-warning { color: #ffc107 !important; }
        .text-info { color: #17a2b8 !important; }
        .bg-info { background-color: #e7f3ff !important; }
        .bg-success { background-color: #e8f5e8 !important; }
        .bg-warning { background-color: #fff3cd !important; }
        .bg-danger { background-color: #f8d7da !important; }
        .panel {
          border: 1px solid #ddd;
          margin-bottom: 15px;
          border-radius: 4px;
        }
        .panel-heading {
          background-color: #f5f5f5;
          padding: 10px 15px;
          border-bottom: 1px solid #ddd;
          font-weight: bold;
        }
        .panel-body {
          padding: 15px;
        }
        .row { 
          display: flex; 
          width: 100%; 
          margin-bottom: 15px;
        }
        .col-md-6 { 
          width: 48%; 
          margin-right: 2%;
          padding: 0;
        }
        .col-md-6:last-child, .col-md-6.last {
          margin-right: 0;
        }
        .col-md-12 {
          width: 100%;
        }
        .clearfix::after {
          content: "";
          display: table;
          clear: both;
        }
        @media print {
          body { 
            margin: 8mm; 
            font-size: 10px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
          }
          .print-section { 
            page-break-inside: avoid;
            margin-bottom: 15px;
          }
          .section-title {
            font-size: 14px !important;
            margin: 10px 0 8px 0 !important;
          }
          .report-header h3, .report-header h4 {
            font-size: 12px !important;
            margin: 5px 0 !important;
          }
          .report-meta {
            font-size: 9px !important;
          }
          table {
            font-size: 9px !important;
            margin-bottom: 10px !important;
          }
          th, td {
            padding: 3px 5px !important;
            line-height: 1.1 !important;
          }
          .panel-heading {
            padding: 5px 10px !important;
            font-size: 10px !important;
          }
          .panel-body {
            padding: 8px !important;
          }
          .no-print { display: none !important; }
        }
        </style>
    </head>
    <body>`;
    
    // Function to check if a section has actual data (not just "no data" messages)
    function hasActualData(section) {
        var content = section.innerHTML;
        
        // Check for "no data" indicators - if found, section is empty
        var noDataIndicators = [
            'No Employee Orders Today',
            'No employee orders were placed',
            'No Guest Orders Today', 
            'No guest orders were placed',
            'No Charity Orders Today',
            'No charity orders were placed',
            'No Cancelled Orders Today',
            'No cancelled orders',
            'Excellent customer satisfaction performance',
            'No categorized expenses to display',
            'No data available',
            'No items sold'
        ];
        
        // If any "no data" indicator is found, section is considered empty
        for (var j = 0; j < noDataIndicators.length; j++) {
            if (content.includes(noDataIndicators[j])) {
                return false;
            }
        }
        
        // Check if section has actual table data (not just headers)
        var tables = section.querySelectorAll('table');
        var hasTableData = false;
        
        for (var k = 0; k < tables.length; k++) {
            var table = tables[k];
            var rows = table.querySelectorAll('tbody tr');
            
            // Check if table has data rows (not just "no data" messages)
            if (rows.length > 0) {
                for (var l = 0; l < rows.length; l++) {
                    var rowText = rows[l].textContent || rows[l].innerText;
                    // If row doesn't contain "no data" type messages, it has data
                    if (!rowText.includes('No ') && !rowText.includes('no ') && rowText.trim().length > 0) {
                        hasTableData = true;
                        break;
                    }
                }
            }
            
            if (hasTableData) break;
        }
        
        // For main sections (Financial Overview, Expenses), always include
        if (section.id === 'printArea' || section.id === 'expensesPrintArea') {
            return true;
        }
        
        // For Sales by Items section, check if it has actual item data
        if (content.includes('🍽️ Sales by Items')) {
            // If it contains "No data to display" or similar, skip it
            if (content.includes('No data to display') || content.includes('No items sold')) {
                return false;
            }
            return true; // Otherwise include it
        }
        
        // For other sections, include if they have table data OR if they don't contain specific "no data" messages
        // Don't use generic "No " check as it's too broad and can exclude valid sections
        return hasTableData || content.trim().length > 200;
    }
    
    // Get all printable sections and add them in order, excluding Kitchen Sales Report and empty sections
    var allSections = document.querySelectorAll('.printable-section');
    var processedSections = new Set(); // Track processed sections to avoid duplicates
    
    for (var i = 0; i < allSections.length; i++) {
        var section = allSections[i];
        var sectionId = section.id || 'section-' + i;

        // Skip Kitchen Sales Report (has ID 'kitchenSalesArea' or contains kitchen sales text)
        if (section.id === 'kitchenSalesArea' || 
            section.innerHTML.includes('🍴 Kitchen Sales Report') ||
            section.innerHTML.includes('Kitchen Sales Report')) {
            continue;
        }
        
        // Skip if already processed (avoid duplicates)
        if (processedSections.has(sectionId)) {
            continue;
        }
        
        // Skip sections with no actual data
        if (!hasActualData(section)) {
            continue;
        }
        
        // Mark as processed
        processedSections.add(sectionId);
        
        // Add section content
        printHTML += '<div class="print-section">';
        printHTML += section.innerHTML;
        printHTML += '</div>';
    }
    
    // Close HTML
    printHTML += `
    </body>
    </html>`;
    
    // Write content to print window and handle printing
    printWindow.document.write(printHTML);
    printWindow.document.close();
    
    // Wait for content to load then print and close
    setTimeout(function() {
        printWindow.focus();
        printWindow.print();
        setTimeout(function() {
            printWindow.close();
        }, 1000);
    }, 800);
}
</script>

</body>
</html>