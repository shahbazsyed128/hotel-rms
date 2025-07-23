<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales</title>
    <style>
        body { background: #f5f6fa; font-family: Arial, sans-serif; }
        .container { max-width: 1100px; margin: 40px auto; background: #fff; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 32px 40px; }
        .title { font-size: 2rem; font-weight: 600; margin-bottom: 24px; color: #333; text-align: center; }
        .summary-row { display: flex; gap: 32px; justify-content: center; margin-bottom: 32px; }
        .summary-card { 
            flex: 1; 
            background: linear-gradient(135deg, #2d3e50 0%, #4b79a1 100%); 
            color: #fff; 
            border-radius: 12px; 
            padding: 24px; 
            text-align: center; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.05); 
        }
        .summary-card.orders { 
            background: linear-gradient(135deg, #283e51 0%, #485563 100%); 
        }
        .summary-card.sales { 
            background: linear-gradient(135deg, #232526 0%, #414345 100%); 
        }
        .summary-card.profit { 
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); 
            color: #222; 
        }
        .summary-label { 
            font-size: 1.1rem; 
            margin-bottom: 8px; 
            opacity: 0.85; 
        }
        .summary-value { font-size: 2rem; font-weight: 700; }
        .section { margin-bottom: 32px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #e0e0e0; padding: 10px; text-align: left; }
        th { background: #f5f6fa; font-weight: 600; }
        tr:nth-child(even) { background: #fafafa; }
        tr:hover { background: #e9ecef; }
        .add-btn { margin: 16px 0; padding: 8px 24px; background: #43e97b; color: #fff; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; transition: background 0.2s; }
        .add-btn:hover { background: #38f9d7; }
        .profit-section { background: #eaffea; border-radius: 12px; padding: 24px; text-align: center; margin-top: 32px; }
        .profit-label { font-size: 1.3rem; color: #333; }
        .profit-value { font-size: 2.2rem; font-weight: bold; color: #27ae60; }
    </style>
</head>
<body>
    <div class="container">
        <div class="title">Hotel Report</div>
        <div class="summary-row">
            <div class="summary-card orders">
                <div class="summary-label">Today Orders</div>
                <div class="summary-value" id="today-orders"><?php echo isset($todayOrders) ? number_format($todayOrders) : 0; ?></div>
            </div>
            <div class="summary-card sales">
                <div class="summary-label">Today Sales</div>
                <div class="summary-value" id="today-sales">₹<?php echo isset($todaySales) ? number_format($todaySales) : 0; ?></div>
            </div>
            <div class="summary-card orders">
                <div class="summary-label">Monthly Orders</div>
                <div class="summary-value" id="monthly-orders"><?php echo isset($monthlyOrders) ? $monthlyOrders : 0; ?></div>
            </div>
            <div class="summary-card sales">
                <div class="summary-label">Monthly Sales</div>
                <div class="summary-value" id="monthly-sales">₹<?php echo isset($monthlySales) ? number_format($monthlySales) : 0; ?></div>
            </div>
            <!-- <div class="summary-card orders">
                <div class="summary-label">Yearly Orders</div>
                <div class="summary-value" id="yearly-orders"><?php echo isset($yearlyOrders) ? $yearlyOrders : 0; ?></div>
            </div>
            <div class="summary-card sales">
                <div class="summary-label">Yearly Sales</div>
                <div class="summary-value" id="yearly-sales">₹<?php echo isset($yearlySales) ? number_format($yearlySales) : 0; ?></div>
            </div> -->
        </div>
        <div class="section">
            <h3>Employees & Daily Salary Expenditure</h3>

            <table id="employee-table">
                <thead>
                    <tr>
                        <th>Emp ID</th>
                        <th>Name</th>
                        <th>Role</th>
                        <th>Salary</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                   
                </tbody>
            </table>
        </div>
        <div class="section">
            <h3>Items Expenditure</h3>
            <table id="items-table">
                <thead>
                    <tr><th>Category</th><th>Subcategory</th><th>Unit</th><th>Quantity</th><th>Price</th></tr>
                </thead>
                <tbody>
                    <!-- Dynamically filled with PHP or JS -->
                </tbody>
            </table>
            <button class="add-btn" onclick="addItemRow()">Add Item</button>
        </div>
        <div class="profit-section">
            <div class="profit-label">Profit (Today's Sales - Total Expenditure)</div>
            <div class="profit-value" id="profit">₹0</div>
        </div>
    </div>
    <script>
        // // Example JS for dynamic item rows (expand as needed)
        // function addItemRow() {
        //     var table = document.getElementById('items-table').getElementsByTagName('tbody')[0];
        //     var row = table.insertRow();
        //     row.innerHTML = '<td><input type="text" name="category[]" placeholder="e.g. Meat"></td>' +
        //                     '<td><input type="text" name="subcategory[]" placeholder="e.g. Chicken"></td>' +
        //                     '<td><select name="unit[]"><option value="kg">kg</option><option value="g">g</option></select></td>' +
        //                     '<td><input type="number" name="quantity[]" min="0" step="0.01"></td>' +
        //                     '<td><input type="number" name="price[]" min="0" step="0.01"></td>';
        // }
    </script>
</body>
</html>
