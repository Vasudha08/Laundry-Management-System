<?php
session_start();

// Check if roll_number is set in session
if (!isset($_SESSION['roll_number'])) {
    echo "<p>You must be logged in to view your profile.</p>";
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

// Prepare and execute the SQL statement to fetch student profile
$sql = "SELECT roll_number, name, email, phone, room_number, created_at FROM students WHERE roll_number = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $roll_number);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the student details
$student = $result->fetch_assoc();

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Laundry Management System</title>
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
            height: calc(100vh - 200px);
        }

        /* Profile section styling */
        .profile {
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin: 50px 10px 20px 200px; /* Set top, right, bottom, left margins */
            width: calc(100% - 500px); /* Adjust width to take full available space */
        }

        /* Title styling */
        .profile h2 {
            margin-top: 0;
            color: #94618E;
            font-size: 24px;
            border-bottom: 2px solid #94618E;
            padding-bottom: 10px;
        }

        /* Table styling */
        table {
            width: 80%;
            border-collapse: collapse;
            margin-left: 100px;
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
    </style>
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
            <a href="current_bookings.php">Current Bookings</a>
            <a href="previous_bookings.php">Previous Bookings</a>
            <a href="change_password.php">Change Password</a>
            <a href="logout.php">Log Out</a>
        </div>

        <div class="main-content">
            <div id="profile" class="profile">
                <h2>My Profile</h2>

                <?php if ($student): ?>
                <table>
                    <tr>
                        <th>Roll Number</th>
                        <td><?= htmlspecialchars($student['roll_number']) ?></td>
                    </tr>
                    <tr>
                        <th>Name</th>
                        <td><?= htmlspecialchars($student['name']) ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td><?= htmlspecialchars($student['phone']) ?></td>
                    </tr>
                    <tr>
                        <th>Room Number</th>
                        <td><?= htmlspecialchars($student['room_number']) ?></td>
                    </tr>
                    <tr>
                        <th>Account Created At</th>
                        <td><?= htmlspecialchars($student['created_at']) ?></td>
                    </tr>
                </table>
                <?php else: ?>
                <p>No profile information found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 Laundry Management System. All rights reserved.</p>
    </footer>

</body>

</html>
