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
        <!-- <h3 class="m-b-0">Daily Comprehensive Report <small class="text-muted">Complete business overview</small></h3> -->
      </div>
      <div class="col-md-4 text-right">
        <button type="button" id="btnPrintReport" class="btn btn-primary">
          <span class="glyphicon glyphicon-print"></span> Print Report
        </button>
      </div>
    </div>
  </div>

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

  <!-- Printable Area -->
  <div id="printArea" class="printable-section" style="background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:15px; margin:10px 0; max-width:100%; overflow:hidden; min-height:auto;">
    <div class="report-header">
      <h3 class="m-b-0">Daily Comprehensive Report</h3>
      <p class="report-meta m-b-0">
        Date: <?php echo isset($reportDate) ? $reportDate : date('Y-m-d'); ?>
        | Time: <?php echo isset($reportTime) ? $reportTime : date('H:i:s'); ?>
        | User: <?php 
        if (isset($userinfo) && $userinfo) {
          echo htmlspecialchars($userinfo->firstname . ' ' . $userinfo->lastname); 
        } else {
          echo 'N/A';
        }
        ?>
        | Register: <?php 
        if (isset($registerinfo) && $registerinfo) {
          echo date('d M, Y H:i', strtotime($registerinfo->opendate)); 
        } else {
          echo 'N/A';
        }
        ?>
      </p>
    </div>

    <!-- Sales & Financial Overview (Cash Register Style) -->
    <div class="row">
      <div class="col-md-7" style="float: left; width: 58%; margin-right: 2%;">
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
            </tfoot>
          </table>
        </div>
          </div>
        </div>
      </div>
          
      <!-- Category Sales Column -->
      <div class="col-md-5" style="float: left; width: 38%;">
        <div class="panel panel-default">
          <div class="panel-heading">📊 Sales by Category</div>
          <div class="panel-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Category</th>
                    <th class="text-right">Sales Amount</th>
                    <th class="text-right">Percentage</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  // Calculate category sales (this would need to be added to controller)
                  $categorySales = array();
                  $totalCategorySales = 0;
                  
                  // For now, let's use example data - you can later connect to real category data
                  $categorySales = array(
                    'Main Dishes' => 1200.00,
                    'Beverages' => 450.00,
                    'Appetizers' => 320.00,
                    'Desserts' => 180.00,
                    'Shop Items' => ($shopSalesData->total_shop_sales ?? 0)
                  );
                  
                  $totalCategorySales = array_sum($categorySales);
                  
                  foreach ($categorySales as $categoryName => $amount):
                    $percentage = $totalCategorySales > 0 ? ($amount / $totalCategorySales * 100) : 0;
                  ?>
                    <tr>
                      <td><?php echo htmlspecialchars($categoryName); ?></td>
                      <td class="text-right"><?php echo number_format($amount, 2); ?></td>
                      <td class="text-right"><?php echo number_format($percentage, 1); ?>%</td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
                <tfoot>
                  <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <th>Total Category Sales</th>
                    <th class="text-right"><?php echo number_format($totalCategorySales, 2); ?></th>
                    <th class="text-right">100.0%</th>
                  </tr>
                </tfoot>
              </table>
            </div>
            
            <!-- Payment Methods Breakdown -->
            <h5 style="margin-top: 20px; color: #337ab7;">💳 Payment Methods</h5>
            <div class="table-responsive">
              <table class="table table-bordered table-condensed">
                <thead>
                  <tr>
                    <th>Payment Method</th>
                    <th class="text-right">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (!empty($totalamount)): ?>
                    <?php foreach ($totalamount as $payment): ?>
                      <tr>
                        <td><?php echo htmlspecialchars($payment->payment_method ?? 'Unknown'); ?></td>
                        <td class="text-right"><?php echo number_format($payment->totalamount ?? 0, 2); ?></td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="2" class="text-center text-muted">No payment data available</td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            
            <!-- Today's Performance Summary -->
            <h5 style="margin-top: 20px; color: #337ab7;">📈 Performance Summary</h5>
            <div class="panel panel-info">
              <div class="panel-body" style="padding: 10px;">
                <?php 
                // Calculate totals for performance
                $totalSalesAmount = 0;
                if (!empty($totalamount)) {
                  foreach ($totalamount as $amount) {
                    $totalSalesAmount += $amount->totalamount;
                  }
                }
                $totalExpenseAmount = $totalexpenses ?? 0;
                $netProfitAmount = $totalSalesAmount - $totalExpenseAmount;
                $profitMargin = $totalSalesAmount > 0 ? ($netProfitAmount / $totalSalesAmount * 100) : 0;
                ?>
                <div class="row">
                  <div class="col-sm-6">
                    <strong>Total Sales:</strong><br>
                    <span class="text-success" style="font-size: 18px;"><?php echo number_format($totalSalesAmount, 2); ?></span>
                  </div>
                  <div class="col-sm-6">
                    <strong>Total Expenses:</strong><br>
                    <span class="text-danger" style="font-size: 18px;"><?php echo number_format($totalExpenseAmount, 2); ?></span>
                  </div>
                </div>
                <hr style="margin: 10px 0;">
                <div class="row">
                  <div class="col-sm-6">
                    <strong>Net Profit:</strong><br>
                    <span class="<?php echo ($netProfitAmount >= 0) ? 'text-success' : 'text-danger'; ?>" style="font-size: 18px;">
                      <?php echo number_format($netProfitAmount, 2); ?>
                    </span>
                  </div>
                  <div class="col-sm-6">
                    <strong>Profit Margin:</strong><br>
                    <span class="<?php echo ($profitMargin >= 0) ? 'text-success' : 'text-danger'; ?>" style="font-size: 18px;">
                      <?php echo number_format($profitMargin, 1); ?>%
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>
    </div>
  </div>
  <!-- /printArea -->
  
  <!-- ========================= Two Column Layout for Reports ========================= -->
  <div class="row">
    <!-- Left Column: Expenses Report -->
    <div class="col-md-6" style="float: left; width: 48%; margin-right: 2%;">
      <div id="expensesPrintArea" class="printable-section" style="background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:15px; margin:10px 0; max-width:100%; overflow:hidden; min-height:auto;" class="dense">
    <div class="report-header">
      <h3 class="m-b-0">Expenses Report</h3>
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
      <?php if (!empty($expensesByCategory)): ?>
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
                      // Try different possible fields for item/product name
                      $itemName = '';
                      if (isset($expense->item_name) && !empty($expense->item_name)) {
                        $itemName = $expense->item_name;
                      } elseif (isset($expense->product_name) && !empty($expense->product_name)) {
                        $itemName = $expense->product_name;
                      } elseif (isset($expense->expense_item) && !empty($expense->expense_item)) {
                        $itemName = $expense->expense_item;
                      } elseif (isset($expense->description) && !empty($expense->description)) {
                        $itemName = $expense->description;
                      } elseif (isset($expense->expense_name) && !empty($expense->expense_name)) {
                        $itemName = $expense->expense_name;
                      } else {
                        $itemName = $expense->entity_name ?? 'N/A';
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
    
    <!-- Right Column: Additional Report Section -->
    <div class="col-md-6" style="float: left; width: 48%;">
      <div id="additionalReportArea" class="printable-section" style="background:#fff; border:1px solid #e6e9ee; border-radius:6px; padding:15px; margin:10px 0; max-width:100%; overflow:hidden; min-height:auto;">
        <div class="report-header">
          <h3 class="m-b-0">Additional Report</h3>
          <p class="report-meta m-b-0">
            Date: <span><?php echo date('Y-m-d'); ?></span>
            <span style="margin-left:10px;">Summary Information</span>
          </p>
        </div>
        <div class="panel panel-default">
          <div class="panel-heading">Report Summary</div>
          <div class="panel-body">
            <p>This section can be used for additional reporting content such as:</p>
            <ul>
              <li>Top selling items</li>
              <li>Performance metrics</li>
              <li>Daily targets vs achievements</li>
              <li>Staff performance</li>
              <li>Customer feedback summary</li>
            </ul>
            <p class="text-muted">Content can be customized based on business requirements.</p>
          </div>
        </div>
      </div>
    </div>
    <div class="clearfix"></div>
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

function printComprehensiveReport() {
    // Create a new window for printing
    var printWindow = window.open('', '_blank', 'width=1000,height=800,scrollbars=yes');
    
    // Get all printable sections
    var printArea = document.getElementById('printArea');
    var expensesArea = document.getElementById('expensesPrintArea');
    var additionalArea = document.getElementById('additionalReportArea');
    
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
        }
        .print-section { 
          margin-bottom: 25px; 
          page-break-inside: avoid;
          border-bottom: 2px solid #eee;
          padding-bottom: 15px;
        }
        .print-title { 
          font-size: 16px; 
          font-weight: bold; 
          text-align: center; 
          margin-bottom: 15px;
          color: #333;
        }
        .section-title {
          font-size: 14px;
          font-weight: bold;
          margin: 15px 0 10px 0;
          color: #2c3e50;
          border-bottom: 1px solid #bdc3c7;
          padding-bottom: 5px;
        }
        .section-subtitle {
          font-size: 12px;
          font-weight: bold;
          margin: 10px 0 8px 0;
          color: #495057;
          border-bottom: 1px solid #dee2e6;
          padding-bottom: 3px;
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
        }
        th { 
          background-color: #f8f9fa; 
          font-weight: bold;
          color: #495057;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bg-info { background-color: #e7f3ff !important; }
        .bg-success { background-color: #e8f5e8 !important; }
        .bg-warning { background-color: #fff3cd !important; }
        .bg-danger { background-color: #f8d7da !important; }
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
        .col-md-6:last-child {
          margin-right: 0;
        }
        .summary-box {
          padding: 10px;
          margin-bottom: 10px;
          border: 1px solid #ddd;
          border-radius: 4px;
        }
        @media print {
          body { 
            margin: 8mm; 
            font-size: 10px;
          }
          .print-section { 
            page-break-inside: avoid;
            margin-bottom: 15px;
          }
          .row {
            display: flex !important;
            width: 100% !important;
          }
          .col-md-6 {
            width: 48% !important;
            margin-right: 2% !important;
            padding: 0 !important;
          }
          .col-md-6:last-child {
            margin-right: 0 !important;
          }
          .section-subtitle {
            font-size: 11px !important;
            margin: 8px 0 6px 0 !important;
            padding-bottom: 2px !important;
          }
          table {
            font-size: 9px !important;
            margin-bottom: 8px !important;
          }
          th, td {
            padding: 2px 3px !important;
            line-height: 1.1 !important;
          }
          /* Print-specific column widths for better item name display */
          .expenses-table th:nth-child(1),
          .expenses-table td:nth-child(1) {
            width: 50% !important;
            min-width: 120px !important;
          }
          .expenses-table th:nth-child(2),
          .expenses-table td:nth-child(2) {
            width: 18% !important;
            max-width: 60px !important;
          }
          .expenses-table th:nth-child(3),
          .expenses-table td:nth-child(3) {
            width: 15% !important;
            max-width: 50px !important;
          }
          .expenses-table th:nth-child(4),
          .expenses-table td:nth-child(4) {
            width: 17% !important;
            max-width: 70px !important;
          }
          .no-print { display: none !important; }
        }
      </style>
    </head>
    <body>`;
    
    // Add main comprehensive report section
    if (printArea) {
        printHTML += `
        <div class="print-section">
          <div class="section-title">Daily Comprehensive Report</div>
          ${printArea.innerHTML}
        </div>`;
    }
    
    // Add expenses and additional reports in two-column layout
    if (expensesArea || additionalArea) {
        printHTML += `
        <div class="print-section">
          <div class="section-title">Daily Reports</div>
          <div class="row">
            <div class="col-md-6">
              ${expensesArea ? `
                <div class="section-subtitle">Expenses Report</div>
                ${expensesArea.innerHTML}
              ` : ''}
            </div>
            <div class="col-md-6">
              ${additionalArea ? `
                <div class="section-subtitle">Additional Reports</div>
                ${additionalArea.innerHTML}
              ` : ''}
            </div>
          </div>
        </div>`;
    }
    
    printHTML += `
    </body>
    </html>`;
    
    // Write content to print window
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