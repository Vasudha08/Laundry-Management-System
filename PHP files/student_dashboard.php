<?php
session_start();

// Database connection
$servername = "localhost"; // Adjust if necessary
$username = "root";         // Adjust if necessary
$password = "vasu0812";    // Adjust if necessary
$dbname = "laundry_management"; // Adjust if necessary

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$student_id = $_SESSION['roll_number']; // Assuming student ID is stored in session

// Handle booking form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_slot'])) {
    $slot_date = $_POST['slot_date'];
    $slot_time = $_POST['slot_time'];

    // Insert booking into the database
    $sql = "INSERT INTO laundry_bookings (roll_number, slot_date, slot_time, status, created_at, cancelled) 
            VALUES (?, ?, ?, 'Pending', NOW(), FALSE)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $student_id, $slot_date, $slot_time);
    $stmt->execute();
    $stmt->close();
}

// Handle booking cancellation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];

    // Update the booking status to cancelled
    $sql = "UPDATE laundry_bookings SET cancelled = TRUE WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch current bookings
$currentBookings = [];
$sql = "SELECT * FROM laundry_bookings WHERE roll_number = ? AND cancelled = FALSE";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $currentBookings[] = $row;
}
$stmt->close();

// Fetch previous bookings
$previousBookings = [];
$sql = "SELECT * FROM laundry_bookings WHERE roll_number = ? AND cancelled = TRUE";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $previousBookings[] = $row;
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #F8EEE7; /* Soft off-white background */
            color: #94618E; /* Rich purple text */
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1, h2 {
            color: #94618E; /* Rich purple */
            text-align: center;
        }

        .container {
            width: 100%;
            max-width: 1200px;
            background-color: #fff; /* White background for contrast */
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #94618E; /* Rich purple border */
        }

        th {
            background-color: #94618E; /* Rich purple header */
            color: #F8EEE7; /* Off-white text */
        }

        tr:hover {
            background-color: #F1E3E1; /* Light hover effect */
        }

        form {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        button {
            background-color: #94618E; /* Rich purple button */
            color: #F8EEE7; /* Off-white button text */
            border: none;
            padding: 10px 15px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s, color 0.3s;
        }

        button:hover {
            background-color: #F8EEE7; /* Light hover button */
            color: #94618E; /* Darker text on hover */
            border: 1px solid #94618E; /* Border on hover */
        }

        input[type="date"],
        input[type="time"] {
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #94618E; /* Rich purple border */
            border-radius: 5px;
        }

        input[type="date"]:focus,
        input[type="time"]:focus {
            outline: none;
            border-color: #F8EEE7; /* Change border color on focus */
        }

        .booking-actions {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .booking-actions form {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <h1>Welcome to LaundroMate</h1>

    <div class="container">
        <h2>Book a Laundry Slot</h2>
        <form action="" method="POST">
            <input type="date" name="slot_date" required>
            <input type="time" name="slot_time" required>
            <button type="submit" name="book_slot">Book Slot</button>
        </form>

        <h2>Current Bookings</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Slot Date</th>
                <th>Slot Time</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            <?php foreach ($currentBookings as $booking): ?>
            <tr>
                <td><?php echo $booking['id']; ?></td>
                <td><?php echo $booking['slot_date']; ?></td>
                <td><?php echo $booking['slot_time']; ?></td>
                <td><?php echo $booking['status']; ?></td>
                <td class="booking-actions">
                    <?php if ($booking['status'] == 'Pending' || $booking['status'] == 'In Progress'): ?>
                    <form action="" method="POST" style="display:inline;">
                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                        <button type="submit" name="cancel_booking">Cancel Booking</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>

        <h2>Previous Bookings</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Slot Date</th>
                <th>Slot Time</th>
                <th>Status</th>
            </tr>
            <?php foreach ($previousBookings as $booking): ?>
            <tr>
                <td><?php echo $booking['id']; ?></td>
                <td><?php echo $booking['slot_date']; ?></td>
                <td><?php echo $booking['slot_time']; ?></td>
                <td><?php echo $booking['status']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
