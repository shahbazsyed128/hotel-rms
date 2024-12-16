<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Items Sales Report</title>

    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        table th, table td {
            text-align: left;
            padding: 12px;
            border: 1px solid #ddd;
        }
        table th {
            background-color: #4CAF50; /* Darker green */
            color: white;
        }
        table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }
        table tbody tr:hover {
            background-color: #e1f5e1; /* Light green on hover */
        }

        .walk-in-row {
            background-color: #d1e7dd; /* Light green for Walk-in Customers */
        }
        .employee-row {
            background-color: #cfe2f3; /* Light blue for Employees */
        }
        .charity-row {
            background-color: #fef2f2; /* Light pink for Charity */
        }
        .guest-row {
            background-color: #fdfd96; /* Light yellow for Guests */
        }

        .table-container {
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <h1>Items Sales Report</h1>

    <!-- Sales Report Table -->
    <div class="table-container">
        <table id="kitchenReportTable">
            <thead>
                <tr>
                    <th>Customer Type</th>
                    <th>Product</th>
                    <th>Variant</th>
                    <th>Quantity</th>
                    <th>Price (<?php echo $currency->curr_icon; ?>)</th>
                    <th>Total Price (<?php echo $currency->curr_icon; ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $grandTotal = 0;

                foreach ($items as $customerType => $products) {
                    $rowCount = count($products);
                    $customerTotal = 0;

                    // Set class based on customer type
                    $rowClass = '';
                    switch ($customerType) {
                        case 'walk in customer':
                            $rowClass = 'walk-in-row';
                            break;
                        case 'employee':
                            $rowClass = 'employee-row';
                            break;
                        case 'charity':
                            $rowClass = 'charity-row';
                            break;
                        case 'guest':
                            $rowClass = 'guest-row';
                            break;
                    }

                    foreach ($products as $index => $product) {
                        $totalPrice = $product->totalqty * $product->price;
                        $grandTotal += $totalPrice;
                        $customerTotal += $totalPrice;

                        echo "<tr class='{$rowClass}'>";
                        // Print customer type only for the first row
                        if ($index === 0) {
                            echo "<td rowspan='{$rowCount}'>" . ucfirst($customerType) . "</td>";
                        }

                        echo "<td>{$product->ProductName}</td>";
                        echo "<td>{$product->variantName}</td>";
                        echo "<td>{$product->totalqty}</td>";
                        echo "<td>" . number_format($product->price, 2) . "</td>";
                        echo "<td>" . number_format($totalPrice, 2) . "</td>";
                        echo "</tr>";
                    }

                    // Add customer type total
                    echo "<tr class='{$rowClass}'>";
                    echo "<td colspan='5' style='text-align:right;'><strong>Total for " . ucfirst($customerType) . "</strong></td>";
                    echo "<td><strong>" . number_format($customerTotal, 2) . "</strong></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="5" style="text-align:right;">Grand Total</th>
                    <th><?php echo number_format($grandTotal, 2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Summary Table -->
    <div class="table-container">
        <table id="summaryTable">
            <thead>
                <tr>
                    <th>Customer Type</th>
                    <th>Total Amount (<?php echo $currency->curr_icon; ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($items as $customerType => $products) {
                    $customerTotal = array_sum(array_map(function ($product) {
                        return $product->totalqty * $product->price;
                    }, $products));

                    echo "<tr>";
                    echo "<td>" . ucfirst($customerType) . "</td>";
                    echo "<td>" . number_format($customerTotal, 2) . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <th style="text-align:right;">Grand Total</th>
                    <th><?php echo number_format($grandTotal, 2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- DataTables Script -->
    <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
    <!-- <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script> -->
    <script>
        $(document).ready(function () {
            $('#kitchenReportTable, #summaryTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                responsive: true,
                autoWidth: false
            });
        });
    </script>
</body>
</html>
