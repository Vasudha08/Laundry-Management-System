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

// Get the selected date or use today's date
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : date('Y-m-d');

// SQL queries to count laundry bookings based on different statuses for the selected date
$total_bookings_query = "SELECT COUNT(*) AS total_bookings FROM laundry_bookings WHERE slot_date = '$date_filter'";
$in_progress_query = "SELECT COUNT(*) AS in_progress FROM laundry_bookings WHERE slot_date = '$date_filter' AND status = 'In Progress'";
$pending_query = "SELECT COUNT(*) AS pending FROM laundry_bookings WHERE slot_date = '$date_filter' AND status = 'Pending'";
$ready_for_collection_query = "SELECT COUNT(*) AS ready_for_collection FROM laundry_bookings WHERE slot_date = '$date_filter' AND status = 'Ready for Collection'";
$collected_query = "SELECT COUNT(*) AS collected FROM laundry_bookings WHERE slot_date = '$date_filter' AND status = 'Collected'";
$cancelled_query = "SELECT COUNT(*) AS cancelled FROM laundry_bookings WHERE slot_date = '$date_filter' AND status = 'Cancelled'";

// Execute total bookings query and fetch the result
$total_bookings_result = $conn->query($total_bookings_query);
if ($total_bookings_result) {
    $total_bookings = $total_bookings_result->fetch_assoc(); // Fetch as associative array
} else {
    die("Error fetching total bookings: " . $conn->error);
}

// Execute in progress query and fetch the result
$in_progress_result = $conn->query($in_progress_query);
if ($in_progress_result) {
    $in_progress = $in_progress_result->fetch_assoc(); // Fetch as associative array
} else {
    die("Error fetching in-progress bookings: " . $conn->error);
}

// Execute pending query and fetch the result
$pending_result = $conn->query($pending_query);
if ($pending_result) {
    $pending = $pending_result->fetch_assoc(); // Fetch as associative array
} else {
    die("Error fetching pending bookings: " . $conn->error);
}

// Execute ready for collection query and fetch the result
$ready_for_collection_result = $conn->query($ready_for_collection_query);
if ($ready_for_collection_result) {
    $ready_for_collection = $ready_for_collection_result->fetch_assoc(); // Fetch as associative array
} else {
    die("Error fetching ready for collection bookings: " . $conn->error);
}

// Execute collected query and fetch the result
$collected_result = $conn->query($collected_query);
if ($collected_result) {
    $collected = $collected_result->fetch_assoc(); // Fetch as associative array
} else {
    die("Error fetching collected bookings: " . $conn->error);
}

// Execute cancelled query and fetch the result
$cancelled_result = $conn->query($cancelled_query);
if ($cancelled_result) {
    $cancelled = $cancelled_result->fetch_assoc(); // Fetch as associative array
} else {
    die("Error fetching cancelled bookings: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard - Laundry Management System</title>
    <style>
        /* General styling for the entire page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f6f6f6;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        /* Styling for header and footer */
        header,
        footer {
            background-color: #94618E;
            color: #fff;
            text-align: center;
            padding: 10px;
        }

        header p {
            font-style: italic;
        }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
        }

        /* Layout styling for the side panel and content */
        .container {
            display: flex;
            flex-grow: 1;
        }

        /* Side panel styling */
        .side-panel {
            background-color: #F8EEE7;
            width: 250px;
            padding: 20px;
            box-sizing: border-box;
            color: black;
        }

        .side-panel h2 {
            color: black;
            font-size: 18px;
            margin-bottom: 20px;
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

        /* Main content area */
        .main-content {
            margin-left: 20px; /* Keep this to allow space for the side panel */
            padding: 10px;
            margin-top: 0; /* No margin at the top to utilize space */
            flex-grow: 1;
            height: calc(100vh - 140px);
        }

        /* Styling for the current bookings section */
        .current-bookings {
            background-color: #ffffff;
            padding: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin: 50px 10px 20px 0; /* Set top, right, bottom, left margins */
            width: calc(100% - 70px); /* Adjust width to take full available space */
        }

        /* Title styling */
        .current-bookings h2 {
            margin-top: 0;
            color: #94618E;
            font-size: 24px;
            border-bottom: 2px solid #94618E;
            padding-bottom: 10px;
        }

        /* Table styling */
        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #F8EEE7;
            color: black;
        }

        /* Hover effect for table rows */
        tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        .cancel-button {
            background-color: #d9534f; /* Red color for cancel button */
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .cancel-button:hover {
            background-color: #c9302c; /* Darker red on hover */
        }
    </style>
</head>

<body>

    <header>
        <h1>LaundroMate - Staff Dashboard</h1>
        <p><i>Manage the day's laundry operations with ease.</i></p>
    </header>

    <!-- Side panel and main content container -->
    <div class="container">

        <!-- Fixed side panel -->
        <div class="side-panel">
            <h2>Navigation</h2>
            <a href="staff_dashboard.php">Dashboard</a>
            <a href="manage_bookings.php">Manage Bookings</a>
            <a href="slot_management.php">Slot Management</a>
            <a href="reports.php">Reports</a>
            <a href="logout.php">Log Out</a>
        </div>

        <!-- Main content section -->
        <div class="main-content">
            <div id="current-bookings" class="current-bookings">
                <h2>Today's Overview</h2>

                <!-- Date filter form -->
                <form method="GET" action="">
                    <label for="date_filter">Select Date:</label>
                    <input type="date" id="date_filter" name="date_filter" value="<?= $date_filter ?>">
                    <button type="submit">Filter</button>
                </form>

                <table>
                    <tr>
                        <th>Total Bookings</th>
                        <th>In Progress</th>
                        <th>Pending</th>
                        <th>Ready for Collection</th>
                        <th>Collected</th>
                        <th>Cancelled</th>
                    </tr>
                    <tr>
                        <td><?= $total_bookings['total_bookings'] ?></td>
                        <td><?= $in_progress['in_progress'] ?></td>
                        <td><?= $pending['pending'] ?></td>
                        <td><?= $ready_for_collection['ready_for_collection'] ?></td>
                        <td><?= $collected['collected'] ?></td>
                        <td><?= $cancelled['cancelled'] ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 LaundroMate. All rights reserved.</p>
    </footer>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
