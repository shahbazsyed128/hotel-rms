<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen-wise Report</title>

    <!-- Your existing CSS -->
    <link href="<?php echo base_url('application/modules/report/assets/css/kicanwiseReport.css'); ?>" rel="stylesheet" type="text/css"/>

    <!-- DataTables CSS (Scoped to avoid overriding) -->
    <link href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        /* Scoped styling to preserve your existing layout */
        #kitchenReportTable_wrapper {
            font-size: inherit;
        }
        #kitchenReportTable {
            width: 100%;
        }
        #kitchenReportTable th, #kitchenReportTable td {
            text-align: left;
        }
    </style>
</head>
<body>
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover" id="kitchenReportTable">
            <thead>
                <tr>
                    <th>Kitchen Name</th>
                    <th>Customer Types</th>
                    <th>Total Amount (<?php echo $currency->curr_icon; ?>)</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grandTotal = 0; 
                foreach ($items as $kitchen): 
                    $kitchenTotal = 0;
                ?>
                    <tr>
                        <td><?php echo $kitchen['kiname']; ?></td>
                        <td>
                            <ul>
                                <?php if (!empty($kitchen['report'])): 
                                    foreach ($kitchen['report'] as $report): 
                                        foreach ($report['customer_types'] as $customerType): 
                                            $kitchenTotal += $customerType['totalprice'];
                                            echo "<li>{$customerType['type']}: " . number_format($customerType['totalprice'], 2) . "</li>";
                                        endforeach;
                                    endforeach;
                                else:
                                    echo "<li>No customer types available</li>";
                                endif; ?>
                            </ul>
                        </td>
                        <td class="text-end">
                            <?php echo number_format($kitchenTotal, 2); ?>
                        </td>
                    </tr>
                    <?php $grandTotal += $kitchenTotal; ?>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-end">Grand Total</th>
                    <th class="text-end">
                        <?php echo number_format($grandTotal, 2); ?>
                    </th>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Existing JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#kitchenReportTable').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true
            });
        });
    </script>
</body>
</html>
