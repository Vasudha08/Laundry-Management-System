<?php
session_start();

// Check if roll_number is set in session
if (!isset($_SESSION['roll_number'])) {
    echo "<p>You must be logged in to view your previous bookings.</p>";
    exit;
}

// Database connection
$servername = "localhost"; // Adjust if necessary
$username = "root";         // Adjust if necessary
$password = "vasu0812";    // Adjust if necessary
$dbname = "laundry_management"; // Adjust if necessary

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname, 3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming the roll number is stored in session
$roll_number = $_SESSION['roll_number'];

// Prepare and execute the SQL statement to fetch previous bookings
$sql = "SELECT id, slot_date, slot_time, expected_collection_time, collection_time, status, cancelled FROM laundry_bookings WHERE roll_number = ? AND cancelled = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $roll_number);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Previous Bookings - Laundry Management System</title>
    <style>
        /* (Your existing styles) */
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
            padding: 3px;
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
        .previous-bookings {
            background-color: #ffffff;
            padding: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin: 10px 10px 20px 0; /* Set top, right, bottom, left margins */
            width: calc(100% - 70px); /* Adjust width to take full available space */
        }

        /* Title styling */
        .previous-bookings h2 {
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

    <script>
        function cancelBooking(bookingId) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "cancel_booking.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    alert(response.message);
                    location.reload(); // Reload the page to see the updated bookings
                }
            };
            xhr.send("booking_id=" + bookingId);
        }
    </script>
</head>

<body>

    <header>
        <h1>LaundroMate</h1>
        <p><i>Manage your laundry bookings with ease.</i></p>
    </header>

    <div class="container">
        <div class="side-panel">
            <h2>Navigation</h2>
            <a href="profile.php">My Profile</a>
            <a href="book_slot.php">Book Slot</a>
            <a href="dashboard.php">Current Bookings</a>
            <a href="previous_bookings.php">Previous Bookings</a>
            <a href="change_password.php">Change Password</a>
            <a href="logout.php">Log Out</a>
        </div>

        <div class="main-content">
            <div id="previous-bookings" class="previous-bookings">
                <h2>Previous Bookings</h2>

                <?php
                if ($result->num_rows > 0) {
                    echo "<table>";
                    echo "<tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Expected Collection Time</th>
                            <th>Collection Time</th>
                            <th>Status</th>
                            <th>Cancel Booking</th>
                          </tr>";
                    while ($booking = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$booking['id']}</td>
                                <td>{$booking['slot_date']}</td>
                                <td>{$booking['slot_time']}</td>
                                <td>{$booking['expected_collection_time']}</td>
                                <td>{$booking['collection_time']}</td>
                                <td>{$booking['status']}</td>";
                        // Only show cancel button if booking can still be canceled
                        if (!$booking['cancelled']) {
                            echo "<td><button class='cancel-button' onclick='cancelBooking({$booking['id']})'>Cancel</button></td>";
                        } else {
                            echo "<td>Cancelled</td>";
                        }
                        echo "</tr>";
                    }
                    echo "</table>";
                } else {
                    echo "<p>You have no previous bookings at the moment.</p>";
                }

                // Close the statement and connection
                $stmt->close();
                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Laundry Management System. All rights reserved.</p>
    </footer>

</body>

</html>
