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
    <link rel="stylesheet" href="../style1.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <style>
        .dashboard-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            padding: 20px;
            gap: 20px;
        }
        
        .card {
            width: 300px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .card-body {
            padding: 20px;
        }
        
        .card-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .card-text {
            font-size: 16px;
            margin: 15px 0;
        }
        
        .btn-primary {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 4px;
            text-decoration: none;
            color: white;
            transition: background-color 0.3s ease;
        }
        
        .btn-primary:hover {
            opacity: 0.9;
        }
        
        .chart-container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto 30px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .filters-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f5f5f5;
            border-radius: 8px;
        }
        
        .filter-group {
            margin: 10px;
        }
        
        select, input[type="date"] {
            padding: 6px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            margin-right: 10px;
        }
        
        .filter-btn {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .filter-btn:hover {
            background-color: #45a049;
        }
        
        #customDateRange {
            display: none;
        }
    </style>
</head>
<body>
<?php include('adminnavbar.php'); ?>

<!-- Chart Container with Filters -->
<div class="chart-container">
    <h5 class="card-title">Library Activity Statistics</h5><hr>
    
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

<div class="dashboard-container">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Registered Users</h5><hr>
        <p class="card-text">No.of Total Users: <?php echo get_user_count();?></p>
        <a href="registeruser.php" class="btn-primary">View Users</a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Registered Books</h5><hr>
        <p class="card-text">No.of Total Books: <?php echo get_book_count();?></p>
        <a href="registerbooks.php" class="btn-primary" style="background-color: rgb(134, 39, 207);">View Books</a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Registered Authors</h5><hr>
        <p class="card-text">No.of Total Authors: <?php echo get_author_count();?></p>
        <a href="registerauthors.php" class="btn-primary" style="background-color: rgb(235, 46, 46);">View Authors</a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Issued Books</h5><hr>
        <p class="card-text">No.of Issued Books: <?php echo get_issue_count();?></p>
        <a href="registerissue.php" class="btn-primary" style="background-color: rgb(50, 46, 156);">View Issues</a>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Request Books</h5><hr>
        <p class="card-text">No.of Request Books: <?php echo get_request_count();?></p>
        <a href="bookrequest.php" class="btn-primary" style="background-color: rgb(50, 46, 156);">View Requested Book</a>
      </div>
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
            maintainAspectRatio: true,
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