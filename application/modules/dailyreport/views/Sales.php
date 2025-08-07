<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Daily Report</title>
    <link rel="stylesheet" href="<?= base_url('application/modules/dailyreport/assets/css/bootstrap.min.css'); ?>">
    <script src="https://unpkg.com/feather-icons"></script>
    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8, #e0eafc);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
        }

        .page-title {
            text-align: center;
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 40px;
            color: #2c3e50;
        }

        .report-card {
            background: linear-gradient(135deg, #ffffff, #f9f9ff);
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s ease;
        }

        .report-card:hover {
            transform: translateY(-5px);
        }

        .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6a11cb, #2575fc);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            margin-right: 12px;
        }

        .report-label {
            font-size: 0.95rem;
            font-weight: 500;
            color: #555;
        }

        .report-value {
            font-size: 1.2rem;
            font-weight: bold;
            color: #333;
        }

        .report-section {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .alert {
            margin-top: 40px;
            text-align: center;
        }

        .profit {
            color: #27ae60;
        }
    </style>
</head>
<body>

    <div class="container ">
        <div class="page-title">📊 Hotel Daily Reports</div>

        <?php if (!empty($report)) { ?>
            <div class="row g-4 justify-content-center">
                <?php foreach ($report as $todayReport) { ?>
                    <div class="col-md-8 mx-auto">
    <div class="report-card p-4">
        <div class="text-center mb-4">
            <h4 class="fw-bold text-dark">📅 <?= $todayReport->report_date; ?></h4>
        </div>

        <div class="report-section" style="background: linear-gradient(to right, #ff9a9e, #fad0c4); border-radius: 10px; padding: 15px; margin-bottom: 15px;">
            <div class="icon-circle"><i data-feather="shopping-cart"></i></div>
            <div>
                <div class="report-label">Daily Sales</div>
                <div class="report-value">Rs. <?= number_format($todayReport->daily_sales, 2); ?></div>
            </div>
        </div>

        <div class="report-section" style="background: linear-gradient(to right, #a1c4fd, #c2e9fb); border-radius: 10px; padding: 15px; margin-bottom: 15px;">
            <div class="icon-circle"><i data-feather="package"></i></div>
            <div>
                <div class="report-label">Item Expenses</div>
                <div class="report-value">Rs. <?= number_format($todayReport->item_expenses, 2); ?></div>
            </div>
        </div>

        <div class="report-section" style="background: linear-gradient(to right, #fbc2eb, #a6c1ee); border-radius: 10px; padding: 15px; margin-bottom: 15px;">
            <div class="icon-circle"><i data-feather="users"></i></div>
            <div>
                <div class="report-label">Employee Expenses</div>
                <div class="report-value">Rs. <?= number_format($todayReport->emp_expenses, 2); ?></div>
            </div>
        </div>

        <div class="report-section" style="background: linear-gradient(to right, #d4fc79, #96e6a1); border-radius: 10px; padding: 15px; margin-bottom: 25px;">
            <div class="icon-circle"><i data-feather="trending-up"></i></div>
            <div>
                <div class="report-label">Profit</div>
                <div class="report-value profit">Rs. <?= number_format($todayReport->profit, 2); ?></div>
            </div>
        </div>

        <div class="text-center">
            <a href="<?= base_url('dailyreport/export_pdf') ?>" target="_blank" class="btn btn-danger">
                <i class="fa fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>
</div>

                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="alert alert-warning">🚫 No data found. Please add expense first!</div>
        <?php } ?>
    </div>

    <script src="<?= base_url('/assets/js/jquery-3.3.1.min.js'); ?>"></script>
    <script>
        feather.replace();
    </script>
</body>
</html>

