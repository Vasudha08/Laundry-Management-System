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

// Initialize filter variables
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';
$roll_number_filter = isset($_GET['roll_number_filter']) ? $_GET['roll_number_filter'] : '';
$status_filter = isset($_GET['status_filter']) ? $_GET['status_filter'] : '';
$slot_time_filter = isset($_GET['slot_time_filter']) ? $_GET['slot_time_filter'] : '';

// SQL query to get bookings based on filters
$sql = "SELECT * FROM laundry_bookings WHERE 1=1";

// Apply filters
if ($date_filter) {
    $sql .= " AND slot_date = '$date_filter'";
}
if ($roll_number_filter) {
    $sql .= " AND roll_number = '$roll_number_filter'";
}
if ($status_filter) {
    $sql .= " AND status = '$status_filter'";
}
if ($slot_time_filter) {
    $sql .= " AND slot_time = '$slot_time_filter'";
}

// Execute query and fetch results
$result = $conn->query($sql);

// Check for errors
if (!$result) {
    die("Error fetching bookings: " . $conn->error);
}

// Update booking status if the form is submitted
if (isset($_POST['update_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['status'];

    $update_sql = "UPDATE laundry_bookings SET status='$new_status' WHERE id='$booking_id'";
    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Booking status updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating status: " . $conn->error . "');</script>";
    }
}

// Cancel booking if the cancel button is pressed
if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];

    $cancel_sql = "UPDATE laundry_bookings SET cancelled = true , status = 'Cancelled' WHERE id='$booking_id'";

    if ($conn->query($cancel_sql) === TRUE) {
        echo "<script>alert('Booking canceled successfully!');</script>";
    } else {
        echo "<script>alert('Error canceling booking: " . $conn->error . "');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Laundry Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f6f6f6;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        header {
            background-color: #94618E;
            color: #fff;
            text-align: center;
            padding: 10px;
        }

        header p {
            font-style: italic;
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

        .main-content {
            margin-left: 20px;
            padding: 10px;
            flex-grow: 1;
        }

        .filter-form {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .filter-form label {
            margin-right: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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

        

        tr:hover {
            background-color: #f1f1f1;
            cursor: pointer;
        }

        .update-button {
            background-color: #5bc0de; /* Blue color for update button */
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .update-button:hover {
            background-color: #31b0d5; /* Darker blue on hover */
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
        <h1>LaundroMate - Manage Bookings</h1>
        <p><i>View and manage student bookings efficiently.</i></p>
    </header>

    <div class="container">
        <div class="side-panel">
            <h2>Navigation</h2>
            <a href="staff_dashboard.php">Dashboard</a>
            <a href="manage_bookings.php">Manage Bookings</a>
            <a href="slot_management.php">Slot Management</a>
            <a href="reports.php">Reports</a>
            <a href="logout.php">Log Out</a>
        </div>

        <div class="main-content">
            <div class="filter-form">
                <form method="GET" action="">
                    <label for="date_filter">Date:</label>
                    <input type="date" id="date_filter" name="date_filter" value="<?= htmlspecialchars($date_filter) ?>">
                    <label for="roll_number_filter">Student ID:</label>
                    <input type="text" id="roll_number_filter" name="roll_number_filter" value="<?= htmlspecialchars($roll_number_filter) ?>">
                    <label for="status_filter">Status:</label>
                    <select id="status_filter" name="status_filter">
                        <option value="">All</option>
                        <option value="Pending" <?= $status_filter === 'Pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="Collected" <?= $status_filter === 'Collected' ? 'selected' : '' ?>>Collected</option>
                        
                        <option value="In Progress" <?= $status_filter === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="Ready for Collection" <?= $status_filter === 'Ready for Collection' ? 'selected' : '' ?>>Ready for Collection</option>
                    </select>
                    <label for="slot_time_filter">Slot Time:</label>
                    <input type="time" id="slot_time_filter" name="slot_time_filter" value="<?= htmlspecialchars($slot_time_filter) ?>">
                    <button type="submit">Filter</button>
                </form>
            </div>

            <table>
                <tr>
                    <th>Booking ID</th>
                    <th>Student ID</th>
                    <th>Slot Date</th>
                    <th>Slot Time</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>

                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['roll_number']) ?></td>
                            <td><?= htmlspecialchars($row['slot_date']) ?></td>
                            <td><?= htmlspecialchars($row['slot_time']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                    <select name="status">
                                        <option value="Pending" <?= $row['status'] === 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Collected" <?= $row['status'] === 'Collected' ? 'selected' : '' ?>>Collected</option>
                                        
                                        <option value="In Progress" <?= $row['status'] === 'In Progress' ? 'selected' : '' ?>>In Progress</option>
                                        <option value="Ready for Collection" <?= $row['status'] === 'Ready for Collection' ? 'selected' : '' ?>>Ready for Collection</option>
                                    </select>
                                    <button type="submit" name="update_status" class="update-button">Update</button>
                                </form>
                                <form method="POST" action="" style="display: inline;">
                                    <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                    <button type="submit" name="cancel_booking" class="cancel-button">Cancel</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No bookings found.</td>
                    </tr>
                <?php endif; ?>
            </table>
        </div>
    </div>

</body>

</html>

<?php
// Close the database connection
$conn->close();
?>
