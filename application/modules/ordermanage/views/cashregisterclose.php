



      <div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
  <h3 class="m-0 p-0">Current Register <span id="rpth">( <?php echo $newDate = date("d M, Y H:i", strtotime($registerinfo->opendate));?> - <?php echo date('d M, Y H:i')?> )</span></h3>
</div>

<div class="modal-body">
  <div class="row">
    <div class="col-12">
      <div class="panel">
        <div class="panel-body">
          <input name="counter" id="pcounter" type="hidden" value="<?php echo $registerinfo->counter_no;?>" />
          <input name="user" id="puser" type="hidden" value="<?php echo $userinfo->firstname.' '.$userinfo->lastname;?>" />

          <table class="table table-bordered table-striped table-hover" id="RoleTbl">
            <thead>
              <tr>
                <th align="left"><?php echo display('sl_no') ?></th>
                <th align="left"><?php echo display('payment_type') ?></th>
                <th align="right"><?php echo display('total_price') ?></th>
              </tr>
            </thead>
            <tbody>
              <?php  
              $total = 0; 
              if (!empty($totalamount)) { 
                $sl = 1; 
                foreach ($totalamount as $amount) { 
                  $total = $total + $amount->totalamount;
              ?>
              <tr>
                <td><?php echo $sl++; ?></td>
                <td><?php echo $amount->payment_method; ?></td>
                <td align="right"><?php echo number_format($amount->totalamount, 2); ?></td>
              </tr>
              <?php } } ?>
            </tbody>
            <tfoot>
              <tr>
                <td align="right" colspan="2"><?php echo display('total') ?>:</td>
                <td align="right"><?php echo number_format($customertypewise->total_amount, 2); ?></td>
              </tr>
              <tr>
                <td align="right" colspan="2">
                  Employee Sales<br>
                  Guest Sales<br>
                  Charity Sales
                </td>
                <td align="right">
                  - <?php echo number_format($customertypewise->employee_sales, 2); ?><br>
                  - <?php echo number_format($customertypewise->guest_sales, 2); ?><br>
                  - <?php echo number_format($customertypewise->charity_sales, 2); ?>
                </td>
              </tr>
              <?php 
              // Calculate shop-related amounts
              $shopBeveragesSales = 0;
              $shopExpenses = 0;
              $otherExpenses = 0;
              $totalKitchenSales = 0;
              
              // Get Shop - Beverages sales (Kitchen ID: 13)
              if (!empty($kitchenItemsReport)) {
                foreach ($kitchenItemsReport as $kitchen) {
                  if ($kitchen['kitchenid'] == 13 && !empty($kitchen['items']['total']) && $kitchen['items']['total']->total_price > 0) {
                    $shopBeveragesSales = $kitchen['items']['total']->total_price;
                    $totalKitchenSales = $shopBeveragesSales;
                    break;
                  }
                }
              }
              
              // Separate Shop expenses from other expenses
              if (!empty($expensesByCategory)) {
                foreach ($expensesByCategory as $categoryName => $amount) {
                  if (strtolower($categoryName) == 'shop' || strpos(strtolower($categoryName), 'shop') !== false) {
                    $shopExpenses += $amount;
                  } else {
                    $otherExpenses += $amount;
                  }
                }
              }
              
              // Calculate total shop amount (sales + expenses)
              $totalShopAmount = $shopBeveragesSales + $shopExpenses;
              ?>
              
              <?php if ($shopBeveragesSales > 0 || $shopExpenses > 0) { ?>
              <tr style="background-color: #e8f4fd;">
                <td align="right" colspan="2">
                  <em>Shop - Beverages Sales (Info Only)</em><br>
                  <?php if ($shopExpenses > 0) { ?>Expenses - Shop<br><?php } ?>
                  <strong>Amount to be Given to Shop</strong>          
                </td>
                <td align="right">
                  <em><?php echo number_format($shopBeveragesSales, 2); ?></em><br>
                  <?php if ($shopExpenses > 0) { ?>- <?php echo number_format($shopExpenses, 2); ?><br><?php } ?>
                  <strong><?php echo number_format($totalShopAmount, 2); ?></strong>
                </td>
              </tr>
              <?php } ?>
              
              <?php if ($otherExpenses > 0) { ?>
              <tr>
                <td align="right" colspan="2">
                  <?php 
                  $expenseLines = array();
                  foreach ($expensesByCategory as $categoryName => $amount) {
                    if (!(strtolower($categoryName) == 'shop' || strpos(strtolower($categoryName), 'shop') !== false)) {
                      $expenseLines[] = 'Expenses - ' . htmlspecialchars($categoryName);
                    }
                  }
                  echo implode('<br>', $expenseLines);
                  ?>
                </td>
                <td align="right">
                  <?php 
                  $amountLines = array();
                  foreach ($expensesByCategory as $categoryName => $amount) {
                    if (!(strtolower($categoryName) == 'shop' || strpos(strtolower($categoryName), 'shop') !== false)) {
                      $amountLines[] = '- ' . number_format($amount, 2);
                    }
                  }
                  echo implode('<br>', $amountLines);
                  ?>
                </td>
              </tr>
              <tr style="background-color: #f8f9fa;">
                <td align="right" colspan="2"><strong>Total Other Expenses</strong></td>
                <td align="right"><strong> - <?php echo number_format($otherExpenses, 2); ?></strong></td>
              </tr>
              <?php } ?>
              <tr>
                <td align="right" colspan="2">Opening Balance</td>
                <td align="right"> + <?php echo number_format($registerinfo->opening_balance, 2); ?></td>
              </tr>
              <tr>
                <td align="right" colspan="2">Remaining Balance</td>
                <td align="right"><?php echo number_format($customertypewise->total_sales + $registerinfo->opening_balance - $otherExpenses - $shopExpenses, 2); ?></td>
              </tr>
              <!-- Expenses Total Row Added -->
              
            </tfoot>
          </table>

          <!-- Kitchen Items Report Section -->
          <?php if (!empty($kitchenItemsReport)) { ?>
          <div class="panel mt-3">
            <div class="panel-heading">
              <h4 class="panel-title">Kitchen Items Report</h4>
            </div>
            <div class="panel-body">
              <?php foreach ($kitchenItemsReport as $kitchen) { ?>
                <?php if (!empty($kitchen['items']['total']) && $kitchen['items']['total']->total_qty > 0) { ?>
                <div class="kitchen-section mb-3">
                  <h5 class="text-primary"><?php echo $kitchen['kitchen_name']; ?></h5>
                  
                  <table class="table table-bordered table-sm">
                    <thead class="thead-light">
                      <tr>
                        <th>Customer Type</th>
                        <th class="text-right">Total Amount</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php if (!empty($kitchen['items']['by_type'])) { ?>
                        <?php foreach ($kitchen['items']['by_type'] as $type) { ?>
                        <tr>
                          <td><?php echo $type->type_name ?: 'Regular'; ?></td>
                          <td class="text-right"><?php echo number_format($type->total_price, 2); ?></td>
                        </tr>
                        <?php } ?>
                      <?php } ?>
                    </tbody>
                    <tfoot class="font-weight-bold">
                      <tr class="table-info">
                        <td><strong><?php echo $kitchen['kitchen_name']; ?> Total</strong></td>
                        <td class="text-right"><strong><?php echo number_format($kitchen['items']['total']->total_price, 2); ?></strong></td>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <?php } ?>
              <?php } ?>
            </div>
          </div>
          <?php } ?>

          <!-- Expenses by Category Section -->
          <?php if (!empty($expensesByCategory)) { ?>
          <div class="panel mt-3">
            <div class="panel-heading">
              <h4 class="panel-title">Expenses by Category</h4>
            </div>
            <div class="panel-body">
              <table class="table table-bordered table-sm">
                <thead class="thead-light">
                  <tr>
                    <th>Category</th>
                    <th class="text-right">Amount</th>
                  </tr>
                </thead>
                <tbody>
                  <?php 
                  $totalCategoryExpenses = 0;
                  foreach ($expensesByCategory as $categoryName => $amount) { 
                    $totalCategoryExpenses += $amount;
                  ?>
                  <tr>
                    <td><?php echo htmlspecialchars($categoryName); ?></td>
                    <td class="text-right"><?php echo number_format($amount, 2); ?></td>
                  </tr>
                  <?php } ?>
                </tbody>
                <tfoot class="font-weight-bold">
                  <tr class="table-warning">
                    <td><strong>Total Expenses</strong></td>
                    <td class="text-right"><strong><?php echo number_format($totalCategoryExpenses, 2); ?></strong></td>
                  </tr>
                </tfoot>
              </table>
            </div>
          </div>
          <?php } ?>

          <?php echo form_open('', 'method="post" name="cashopen" id="cashopenfrm"') ?>
            <input type="hidden" id="registerid" name="registerid" value="<?php echo $registerinfo->id;?>" />
            <div class="col-md-12">
              <div class="form-group row">
                <label for="totalamount" class="col-sm-4 col-form-label"><?php echo display('total_amount');?></label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="totalamount" name="totalamount" value="<?php echo number_format($customertypewise->total_sales + $registerinfo->opening_balance - $otherExpenses - $shopExpenses, 2); ?>"/>
                </div>
              </div>
              <div class="form-group row">
                <label for="closingnote" class="col-sm-4 col-form-label"><?php echo "Note";?></label>
                <div class="col-sm-7">
                  <textarea id="closingnote" class="form-control" name="closingnote" cols="30" rows="3" placeholder="Closing Note"></textarea>
                </div>
              </div>
              <div class="form-group text-right">
                <div class="col-sm-12 pr-0">
                  <button type="button" id="openclosecash" class="btn btn-success w-md m-b-5" onclick="closeandprintcashregister()">Close Register and Print Summary</button>
                  <button type="button" id="openclosecash" class="btn btn-primary w-md m-b-5" onclick="closecashregister()">Add Closing Balance</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
      