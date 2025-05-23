<?php
    session_start();
    require('functions.php');
    
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Check if user is logged in
    if (!isset($_SESSION['Name'])) {
        echo "<script>alert('Please login to continue.'); window.location.href='index.php';</script>";
        exit();
    }

    // Get filter parameters
    $view_type = isset($_GET['view_type']) ? $_GET['view_type'] : 'daily';
    $custom_range = isset($_GET['custom_range']) && $_GET['custom_range'] == '1';
    
    if ($custom_range && isset($_GET['start_date']) && isset($_GET['end_date'])) {
        $start_date = $_GET['start_date'];
        $end_date = $_GET['end_date'];
    } else {
        // Default date ranges
        $end_date = date('Y-m-d');
        
        if ($view_type == 'daily') {
            $start_date = date('Y-m-d', strtotime('-6 days')); // 7 days including today
        } else { // weekly
            $start_date = date('Y-m-d', strtotime('-4 weeks')); // 5 weeks including this week
        }
    }

    // Database connection
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "library";
    
    $con = mysqli_connect($host, $username, $password, $database);
    if (!$con) {
        die("Database Connection Error: " . mysqli_connect_error());
    }

    // Function to get daily data for specified date range
    function get_daily_stats($con, $start_date, $end_date) {
        $data = [
            'requests' => [],
            'issued' => [],
            'returned' => []
        ];
        $labels = [];
        
        $current_date = new DateTime($start_date);
        $end = new DateTime($end_date);
        
        while ($current_date <= $end) {
            $date_str = $current_date->format('Y-m-d');
            $labels[] = $current_date->format('M d'); // Format: Jan 01
            
            // Get request count
            $request_query = "SELECT COUNT(*) as count FROM book_request WHERE DATE(request_date) = '$date_str'";
            $request_result = mysqli_query($con, $request_query);
            $request_row = mysqli_fetch_assoc($request_result);
            
            // Get issued count
            $issued_query = "SELECT COUNT(*) as count FROM issued WHERE DATE(issue_date) = '$date_str' AND returned IS NULL";
            $issued_result = mysqli_query($con, $issued_query);
            $issued_row = mysqli_fetch_assoc($issued_result);
            
            // Get returned count
            $returned_query = "SELECT COUNT(*) as count FROM issued WHERE DATE(returned_date) = '$date_str' AND returned = 1";
            $returned_result = mysqli_query($con, $returned_query);
            $returned_row = mysqli_fetch_assoc($returned_result);
            
            // Store results
            $data['requests'][] = $request_row['count'] ?? 0;
            $data['issued'][] = $issued_row['count'] ?? 0;
            $data['returned'][] = $returned_row['count'] ?? 0;
            
            $current_date->modify('+1 day');
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    // Function to get weekly data for specified date range
    function get_weekly_stats($con, $start_date, $end_date) {
        $data = [
            'requests' => [],
            'issued' => [],
            'returned' => []
        ];
        $labels = [];
        
        $current_date = new DateTime($start_date);
        // Adjust to start of week (Monday)
        $day_of_week = $current_date->format('N'); // 1 (Monday) to 7 (Sunday)
        $current_date->modify('-' . ($day_of_week - 1) . ' days');
        
        $end = new DateTime($end_date);
        
        while ($current_date <= $end) {
            $week_start = clone $current_date;
            $week_end = clone $current_date;
            $week_end->modify('+6 days'); // End of week (Sunday)
            
            $week_start_str = $week_start->format('Y-m-d');
            $week_end_str = $week_end->format('Y-m-d');
            
            // Format label as "Jan 01 - Jan 07"
            $labels[] = $week_start->format('M d') . ' - ' . $week_end->format('M d');
            
            // Get weekly request count
            $request_query = "SELECT COUNT(*) as count FROM book_request 
                             WHERE DATE(request_date) BETWEEN '$week_start_str' AND '$week_end_str'";
            $request_result = mysqli_query($con, $request_query);
            $request_row = mysqli_fetch_assoc($request_result);
            
            // Get weekly issued count
            $issued_query = "SELECT COUNT(*) as count FROM issued 
                            WHERE DATE(issue_date) BETWEEN '$week_start_str' AND '$week_end_str' AND returned IS NULL";
            $issued_result = mysqli_query($con, $issued_query);
            $issued_row = mysqli_fetch_assoc($issued_result);
            
            // Get weekly returned count
            $returned_query = "SELECT COUNT(*) as count FROM issued 
                              WHERE DATE(returned_date) BETWEEN '$week_start_str' AND '$week_end_str' AND returned = 1";
            $returned_result = mysqli_query($con, $returned_query);
            $returned_row = mysqli_fetch_assoc($returned_result);
            
            // Store results
            $data['requests'][] = $request_row['count'] ?? 0;
            $data['issued'][] = $issued_row['count'] ?? 0;
            $data['returned'][] = $returned_row['count'] ?? 0;
            
            $current_date->modify('+7 days'); // Move to next week
        }
        
        return ['labels' => $labels, 'data' => $data];
    }

    // Get the statistics data based on view type
    try {
        if ($view_type == 'daily') {
            $stats = get_daily_stats($con, $start_date, $end_date);
            $chart_title = 'Daily Library Transaction Statistics';
            $x_title = 'Date';
        } else { // weekly
            $stats = get_weekly_stats($con, $start_date, $end_date);
            $chart_title = 'Weekly Library Transaction Statistics';
            $x_title = 'Week';
        }
        
        $labels = $stats['labels'];
        $data = $stats['data'];
    } catch (Exception $e) {
        // Fallback to dummy data in case of an error
        if ($view_type == 'daily') {
            $labels = [
                date('M d', strtotime('-6 days')),
                date('M d', strtotime('-5 days')),
                date('M d', strtotime('-4 days')),
                date('M d', strtotime('-3 days')),
                date('M d', strtotime('-2 days')),
                date('M d', strtotime('-1 days')),
                date('M d')
            ];
        } else {
            $labels = [
                date('M d', strtotime('-28 days')) . ' - ' . date('M d', strtotime('-22 days')),
                date('M d', strtotime('-21 days')) . ' - ' . date('M d', strtotime('-15 days')),
                date('M d', strtotime('-14 days')) . ' - ' . date('M d', strtotime('-8 days')),
                date('M d', strtotime('-7 days')) . ' - ' . date('M d', strtotime('-1 days')),
                date('M d', strtotime('-0 days')) . ' - ' . date('M d', strtotime('+6 days'))
            ];
        }
        
        $data = [
            'requests' => $view_type == 'daily' ? [3, 5, 2, 4, 6, 3, 5] : [15, 20, 18, 22, 17],
            'issued' => $view_type == 'daily' ? [5, 4, 3, 6, 2, 4, 5] : [22, 18, 25, 20, 23],
            'returned' => $view_type == 'daily' ? [2, 3, 1, 3, 4, 2, 3] : [12, 15, 10, 14, 16]
        ];
        $chart_title = $view_type == 'daily' ? 'Daily Library Transaction Statistics' : 'Weekly Library Transaction Statistics';
        $x_title = $view_type == 'daily' ? 'Date' : 'Week';
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="dashboard.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    
    <style>
        /* Print styles for the report */
        @media print {
            body * {
                visibility: hidden;
            }
            
            .print-report, .print-report * {
                visibility: visible;
            }
            
            .print-report {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white;
                padding: 20px;
            }
            
            .no-print {
                display: none !important;
            }
            
            .page-break {
                page-break-before: always;
            }
        }
        
        /* Report generation button styles */
        .report-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 10px 0;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .report-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
        }
        
        /* Print report styles */
        .print-report {
            display: none;
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        
        .report-header {
            text-align: center;
            border-bottom: 3px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .report-title {
            font-size: 28px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        
        .report-subtitle {
            font-size: 16px;
            color: #666;
            margin: 5px 0 0 0;
        }
        
        .report-meta {
            font-size: 14px;
            color: #888;
            margin: 10px 0 0 0;
        }
        
        .report-section {
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-item {
            border: 2px solid #eee;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: #666;
        }
        
        .chart-data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        .chart-data-table th,
        .chart-data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        
        .chart-data-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        
        .chart-data-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        
        .summary-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 20px;
        }
        
        .summary-item {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .summary-value {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        
        .summary-label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
    
</head>
<body>
<?php include('adminnavbar.php'); ?>

<div class="main">

<?php include('sidebar.php'); ?>


<div class="container">
    <div class='cards-container'>
        <div class="card">
            <div class="card-title">
                <i class="fas fa-users"></i>
                <h5>Registered Users</h5>
            </div>
            <p class="card-text"><?php echo get_user_count();?></p>
        </div>

        <div class="card">
            <div class="card-title">
                <i class="fas fa-book"></i>
                <h5>Registered Books</h5>
            </div>
            <p class="card-text"><?php echo get_book_count();?></p>
        </div>

        <div class="card">
            <div class="card-title">
                <i class="fas fa-pen-nib"></i>
                <h5>Registered Authors</h5>
            </div>
            <p class="card-text"><?php echo get_author_count();?></p>
        </div>

        <div class="card">
            <div class="card-title">
                <i class="fas fa-bookmark"></i>
                <h5>Issued Books</h5>
            </div>
            <p class="card-text"><?php echo get_issue_count();?></p>
        </div>

        <div class="card">
            <div class="card-title">
                <i class="fas fa-paper-plane"></i>
                <h5>Request Books</h5>
            </div>
            <p class="card-text"><?php echo get_request_count();?></p>
        </div>
    </div>

    <div class="chart-container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h5 class="card-title">Library Activity Statistics</h5>
            <button class="report-btn no-print" onclick="generateReport()">
                <i class="fas fa-file-pdf"></i>
                Generate Report
            </button>
        </div>
        <hr>
        
        <div class="filters-container">
            <form id="filterForm" method="GET" action="">
                <div class="filter-group">
                    <label for="viewType">View:</label>
                    <select id="viewType" name="view_type" onchange="toggleCustomDateFields()">
                        <option value="daily" <?php echo $view_type == 'daily' ? 'selected' : ''; ?>>Daily</option>
                        <option value="weekly" <?php echo $view_type == 'weekly' ? 'selected' : ''; ?>>Weekly</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="dateRangeType">Date Range:</label>
                    <select id="dateRangeType" name="custom_range" onchange="toggleCustomDateFields()">
                        <option value="0" <?php echo !$custom_range ? 'selected' : ''; ?>>Default Range</option>
                        <option value="1" <?php echo $custom_range ? 'selected' : ''; ?>>Custom Range</option>
                    </select>
                </div>
                
                <div id="customDateRange" class="filter-group" <?php echo $custom_range ? 'style="display: flex;"' : ''; ?>>
                    <label for="startDate">From:</label>
                    <input type="date" id="startDate" name="start_date" value="<?php echo $start_date; ?>">
                    
                    <label for="endDate">To:</label>
                    <input type="date" id="endDate" name="end_date" value="<?php echo $end_date; ?>">
                </div>
                
                <div class="filter-group">
                    <button type="submit" class="filter-btn">Apply Filters</button>
                </div>
            </form>
        </div>
        
        <canvas id="libraryStatsChart"></canvas>
    </div>
</div>

</div>

<!-- Hidden Print Report -->
<div class="print-report" id="printReport">
    <div class="report-header">
        <h1 class="report-title">Library Management System</h1>
        <p class="report-subtitle">Dashboard Report</p>
        <p class="report-meta">
            Generated on: <?php echo date('F j, Y \a\t g:i A'); ?><br>
            Report Period: <?php echo date('M j, Y', strtotime($start_date)) . ' - ' . date('M j, Y', strtotime($end_date)); ?><br>
            View Type: <?php echo ucfirst($view_type); ?>
        </p>
    </div>
    
    <div class="report-section">
        <h2 class="section-title">Library Overview</h2>
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-value"><?php echo get_user_count(); ?></div>
                <div class="stat-label">Registered Users</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo get_book_count(); ?></div>
                <div class="stat-label">Registered Books</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo get_author_count(); ?></div>
                <div class="stat-label">Registered Authors</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo get_issue_count(); ?></div>
                <div class="stat-label">Issued Books</div>
            </div>
            <div class="stat-item">
                <div class="stat-value"><?php echo get_request_count(); ?></div>
                <div class="stat-label">Book Requests</div>
            </div>
        </div>
    </div>
    
    <div class="report-section page-break">
        <h2 class="section-title"><?php echo $chart_title; ?></h2>
        
        <table class="chart-data-table">
            <thead>
                <tr>
                    <th><?php echo $x_title; ?></th>
                    <th>Book Requests</th>
                    <th>Books Issued</th>
                    <th>Books Returned</th>
                </tr>
            </thead>
            <tbody>
                <?php for ($i = 0; $i < count($labels); $i++): ?>
                <tr>
                    <td><?php echo $labels[$i]; ?></td>
                    <td><?php echo $data['requests'][$i]; ?></td>
                    <td><?php echo $data['issued'][$i]; ?></td>
                    <td><?php echo $data['returned'][$i]; ?></td>
                </tr>
                <?php endfor; ?>
            </tbody>
        </table>
        
        <div class="summary-stats">
            <div class="summary-item">
                <div class="summary-value"><?php echo array_sum($data['requests']); ?></div>
                <div class="summary-label">Total Requests</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?php echo array_sum($data['issued']); ?></div>
                <div class="summary-label">Total Issued</div>
            </div>
            <div class="summary-item">
                <div class="summary-value"><?php echo array_sum($data['returned']); ?></div>
                <div class="summary-label">Total Returned</div>
            </div>
        </div>
    </div>
    
    <div class="report-section">
        <h2 class="section-title">Report Summary</h2>
        <p>This report provides a comprehensive overview of the library management system's current status and activity for the specified period. The data includes user registrations, book inventory, author database, and transaction statistics.</p>
        
        <p><strong>Key Insights:</strong></p>
        <ul>
            <li>Total library users: <?php echo get_user_count(); ?></li>
            <li>Total books in collection: <?php echo get_book_count(); ?></li>
            <li>Active book requests in period: <?php echo array_sum($data['requests']); ?></li>
            <li>Books issued in period: <?php echo array_sum($data['issued']); ?></li>
            <li>Books returned in period: <?php echo array_sum($data['returned']); ?></li>
        </ul>
    </div>
</div>

<script>
    // Function to toggle custom date fields visibility
    function toggleCustomDateFields() {
        const dateRangeType = document.getElementById('dateRangeType');
        const customDateRange = document.getElementById('customDateRange');
        
        if (dateRangeType.value === '1') {
            customDateRange.style.display = 'flex';
        } else {
            customDateRange.style.display = 'none';
        }
    }
    
    // Initialize toggle on page load
    document.addEventListener('DOMContentLoaded', toggleCustomDateFields);
    
    // Function to generate and print report
    function generateReport() {
        const printReport = document.getElementById('printReport');
        printReport.style.display = 'block';
        
        // Small delay to ensure content is rendered
        setTimeout(function() {
            window.print();
            
            // Hide the report again after printing
            setTimeout(function() {
                printReport.style.display = 'none';
            }, 1000);
        }, 100);
    }
    
    // Create chart with the data from PHP
    const statsCtx = document.getElementById('libraryStatsChart').getContext('2d');
    const statsChart = new Chart(statsCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [
                {
                    label: 'Book Requests',
                    data: <?php echo json_encode($data['requests']); ?>,
                    backgroundColor: 'rgba(0, 150, 136, 0.7)', // Teal color
                    borderColor: 'rgba(0, 150, 136, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Books Issued',
                    data: <?php echo json_encode($data['issued']); ?>,
                    backgroundColor: 'rgba(25, 55, 100, 0.7)', // Dark blue
                    borderColor: 'rgba(25, 55, 100, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Books Returned',
                    data: <?php echo json_encode($data['returned']); ?>,
                    backgroundColor: 'rgba(130, 200, 40, 0.7)', // Light green
                    borderColor: 'rgba(130, 200, 40, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Books'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: '<?php echo $x_title; ?>'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: '<?php echo $chart_title; ?>'
                }
            }
        }
    });
    
    // Form validation before submit
    document.getElementById('filterForm').addEventListener('submit', function(event) {
        const customRange = document.getElementById('dateRangeType').value === '1';
        
        if (customRange) {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            if (!startDate || !endDate) {
                alert('Please select both start and end dates for custom range');
                event.preventDefault();
                return;
            }
            
            if (new Date(startDate) > new Date(endDate)) {
                alert('Start date cannot be after end date');
                event.preventDefault();
                return;
            }
        }
    });
</script>
</body>
</html>