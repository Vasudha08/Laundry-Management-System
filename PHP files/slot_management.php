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

// Initialize slots array
$slots = [];
$slot_times = [];

// Generate time slots from 9 AM to 5 PM in 30-minute intervals
$start_time = new DateTime('09:00');
$end_time = new DateTime('17:00');
$interval = new DateInterval('PT30M');

while ($start_time <= $end_time) {
    $slot_times[] = $start_time->format('H:i');
    $start_time->add($interval);
}

// Fetch current slot availability from the database
foreach ($slot_times as $time) {
    $sql = "SELECT is_available, capacity FROM slots WHERE slot_time = '$time'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $slots[$time] = [
            'is_available' => $row['is_available'],
            'capacity' => $row['capacity']
        ];
    } else {
        // Default to available if no entry exists
        $slots[$time] = [
            'is_available' => true,
            'capacity' => 10
        ];
        // Insert default value into the database
        $insert_sql = "INSERT INTO slots (slot_time, is_available, capacity) VALUES ('$time', true, 10)";
        $conn->query($insert_sql);
    }
}

// Update slot availability if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $slot_time = $_POST['slot_time'];
    $is_available = $_POST['is_available']; // '1' for available, '0' for unavailable

    $update_sql = "UPDATE slots SET is_available = '$is_available' WHERE slot_time = '$slot_time'";
    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Slot availability updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating slot availability: " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slot Management - Laundry Management System</title>
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

        header,
        footer {
            background-color: #94618E;
            color: #fff;
            text-align: center;
            padding: 10px;
        }

        header h1 {
            margin: 0;
        }

        header p {
            font-style: italic;
        }

        footer {
            position: fixed;
            bottom: 0;
            height: 5%;
            padding-top: 5px;
            width: 100%;
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
            width: 80%;
        }

        .current-bookings {
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin: 20px 20px 20px 20px; /* Set top, right, bottom, left margins */
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

        h2 {
            margin-bottom: 10px;
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-left: 100px;
            max-width :800px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
            margin-left: 20px;

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
            margin-left: 10px;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }

        .update-button:hover {
            background-color: #31b0d5; /* Darker blue on hover */
        }
    </style>
</head>

<body>

    <header>
        <h1>LaundroMate - Slot Management</h1>
        <p><i>Manage time slots for student bookings.</i></p>
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
        <div id="current-bookings" class="current-bookings">
            <h2>Available Time Slots</h2>
            <table>
                <tr>
                    <th>Slot Time</th>
                    <th>Available</th>
                    <th>Capacity</th>
                    <th>Actions</th>
                </tr>

                <?php foreach ($slots as $time => $slot): ?>
                    <tr>
                        <td><?= $time ?></td>
                        <td><?= $slot['is_available'] ? 'Yes' : 'No' ?></td>
                        <td><?= $slot['capacity'] ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="slot_time" value="<?= $time ?>">
                                <select name="is_available">
                                    <option value="1" <?= $slot['is_available'] ? 'selected' : '' ?>>Available</option>
                                    <option value="0" <?= !$slot['is_available'] ? 'selected' : '' ?>>Unavailable</option>
                                </select>
                                <button type="submit" class="update-button">Update</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    </div>

</body>

<footer>
        <p>&copy; 2024 LaundroMate. All rights reserved.</p>
    </footer>

</html>

<?php
// Close the database connection
$conn->close();
?>
