<?php
// Start session if necessary
session_start();

// Database connection (adjust parameters if necessary)
$servername = "localhost";
$username = "root"; // Adjust according to your database user
$password = "vasu0812"; // Adjust according to your database password
$dbname = "laundry_management"; // Adjust according to your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize arrays for storing report data
$usage_data = [];
$peak_hours = array_fill(0, 16, 0); // For each hour from 9 AM to 5 PM

// Fetch bookings data for report (no status filter)
$sql = "SELECT slot_time, COUNT(*) as total_bookings FROM laundry_bookings GROUP BY slot_time";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Log the slot_time value for debugging
        error_log("Processing slot_time: " . $row['slot_time']);
        
        $slot_time = DateTime::createFromFormat('H:i:s', $row['slot_time']);

        
        // Check if slot_time creation was successful
        if (!$slot_time) {
            error_log("Invalid time format: " . $row['slot_time']);
            continue; // Skip this entry if the time is invalid
        }
        
        // Calculate the hour based on the time slot (9 AM is 0)
        $hour = (int)$slot_time->format('H') - 9; // 0 for 9 AM, 1 for 10 AM, etc.
        if ($hour >= 0 && $hour < 16) {
            $peak_hours[$hour] += $row['total_bookings'];
        }
        $usage_data[$row['slot_time']] = $row['total_bookings'];
    }
}
else {
    echo "No bookings found.";
}

// Prepare data for charts
$labels = array_map(function ($hour) {
    return (new DateTime("09:00 +$hour hour"))->format('H:i');
}, array_keys($peak_hours));

// Prepare total bookings data for graph
$total_bookings = array_values($peak_hours);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports and Analytics - Laundry Management System</title>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f6f6f6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #94618E;
            color: #fff;
            text-align: center;
            padding: 10px;
        }
        .container {
            display: flex;
            flex-grow: 1;
        }
        .side-panel {
            background-color: #F8EEE7;
            width: 250px;
            padding: 20px;
            box-sizing: border-box;
            color: black;
        }
        .side-panel a {
            display: block;
            color: black;
            padding: 10px 0;
            text-decoration: none;
            font-size: 16px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.2);
        }
        .side-panel a:hover {
            background-color: #94618E;
            color: white;
            cursor: pointer;
        }
        .main-content {
            margin-left: 20px;
            padding: 10px;
            flex-grow: 1;
        }
        h2 {
            color: #94618E;
        }

        canvas {
    width: 30px;  /* Adjust this value to make the chart smaller */
    height: 10px; /* Adjust this value to make the chart smaller */
}
    </style>
</head>
<body>

<header>
    <h1>LaundroMate - Reports and Analytics</h1>
    <p><i>View usage reports and peak hours.</i></p>
</header>

<div class="container">
    <div class="side-panel">
        <h2>Navigation</h2>
        <a href="staff_dashboard.php">Dashboard</a>
        <a href="manage_bookings.php">Manage Bookings</a>
        <a href="slot_management.php">Slot Management</a>
        <a href="report.php">Reports</a>
        <a href="logout.php">Log Out</a>
    </div>

    <div class="main-content">
        <h2>Usage Report</h2>
        <div style="width: 700px; height: 400px; margin-left:100px">
    <canvas id="usageChart"></canvas>
</div>

        <h2>Peak Hours</h2>
        <div style="width: 700px; height: 400px; margin-left: 100px;">
    <canvas id="peakHoursChart"></canvas>
</div>
    </div>
</div>

<script>
    const usageData = <?= json_encode($usage_data) ?>;
    const peakHoursData = <?= json_encode($total_bookings) ?>;
    const labels = <?= json_encode($labels) ?>;

    // Chart for usage report
    const usageChartCtx = document.getElementById('usageChart').getContext('2d');
    const usageChart = new Chart(usageChartCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(usageData),
            datasets: [{
                label: 'Total Bookings',
                data: Object.values(usageData),
                backgroundColor: 'rgba(148, 97, 142, 0.5)',
                borderColor: 'rgba(148, 97, 142, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Chart for peak hours
    const peakHoursChartCtx = document.getElementById('peakHoursChart').getContext('2d');
    const peakHoursChart = new Chart(peakHoursChartCtx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Peak Hour Bookings',
                data: peakHoursData,
                fill: false,
                borderColor: 'rgba(75, 192, 192, 1)',
                tension: 0.1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    console.log('Usage Data:', usageData);
console.log('Peak Hours Data:', peakHoursData);
console.log('Labels:', labels);

</script>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
