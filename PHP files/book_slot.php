<?php
session_start();

// Check if roll_number is set in session
if (!isset($_SESSION['roll_number'])) {
    echo "<p>You must be logged in to book a slot.</p>";
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
// Other code above remains unchanged...

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll_number = $_SESSION['roll_number'];
    $type = $_POST['type'];
    $slot_date = $_POST['slot_date'];
    $slot_time = $_POST['slot_time'];
    $collection_time = $_POST['collection_time'];

    // Check current bookings for the selected slot
    $check_sql = "SELECT COUNT(*) as booking_count FROM laundry_bookings WHERE slot_date = ? AND slot_time = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $slot_date, $slot_time);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    $booking_count = $check_result->fetch_assoc()['booking_count'];

    // Ensure the maximum of 10 students for the slot
    if ($booking_count >= 10) {
        $_SESSION['booking_message'] = "Booking failed: Maximum capacity for this time slot reached.";
        $_SESSION['booking_type'] = 'error'; // Set the type of message
    } else {
        $slot_time_obj = DateTime::createFromFormat('H:i', $slot_time);
        if ($slot_time_obj) {
            // Add 1 hour to the slot time for expected collection time
            $slot_time_obj->modify('+1 hour');
            $expected_collection_time = $slot_date . ' ' . $slot_time_obj->format('H:i:s'); // Combine date and time
        
            $sql = "INSERT INTO laundry_bookings (roll_number, type, slot_date, slot_time, expected_collection_time, status, cancelled) 
        VALUES (?, ?, ?, ?, ?, 'Pending', 0)";

// Prepare and execute the SQL statement to insert a booking
$stmt = $conn->prepare($sql);

// Bind parameters (remove collection_time from binding)
$stmt->bind_param("sssss", $roll_number, $type, $slot_date, $slot_time, $expected_collection_time);

    
            if ($stmt->execute()) {
                // Get the last inserted ID
                $booking_id = $stmt->insert_id; 
                $_SESSION['booking_message'] = "Booking successful! Your Booking ID is: " . $booking_id;
                $_SESSION['booking_type'] = 'success'; // Set the type of message
            } else {
                $_SESSION['booking_message'] = "Error: " . $stmt->error;
                $_SESSION['booking_type'] = 'error'; // Set the type of message
            }
        }
        // Close the statement
        $stmt->close();
    }
    $check_stmt->close();
}

// Predefined time slots (from 9:00 AM to 5:00 PM)
// The rest of the HTML code remains unchanged...


// Predefined time slots (from 9:00 AM to 5:00 PM)
$predefined_slots = [
    "09:00", "09:30", "10:00", "10:30", "11:00", "11:30", 
    "12:00", "12:30", "13:00", "13:30", "14:00", "14:30", 
    "15:00", "15:30", "16:00", "16:30"
];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Slot - Laundry Management System</title>
    <style>
         /* (Your existing styles) */
         body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f6f6f6;
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }

        header, footer {
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
            margin-left: 200px; /* Keep this to allow space for the side panel */
            padding: 10px;
            margin-top: 0%; /* No margin at the top to utilize space */
            flex-grow: 1;
            width: 20%;
            height: calc(100vh - 140px);
        }

        /* Form styling */
        .booking-form {
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin: 20px 10px 20px 10px; /* Set top, right, bottom, left margins */
            width: calc(100% - 500px); /* Adjust width to take full available space */
        }

        .booking-form h2 {
            margin-top: 0;
            color: #94618E;
            font-size: 24px;
            border-bottom: 2px solid #94618E;
            padding-bottom: 10px;
        }

        .booking-form input, .booking-form select, .booking-form button {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .booking-form button {
            background-color: #94618E;
            width: 50%;
            margin-left: 150px;
            
            color: white;
            border: none;
            cursor: pointer;
        }

        .booking-form button:hover {
            background-color: #7A4B6C; /* Darker shade on hover */
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
            <a href="dashboard.php">Current Bookings</a>
            <a href="previous_bookings.php">Previous Bookings</a>
            <a href="change_password.php">Change Password</a>
            <a href="logout.php">Log Out</a>
        </div>

        <div class="main-content">
            <div class="booking-form">
                <h2>Book a Slot</h2>
                <form method="POST" action="">
                    <label for="type">Booking Type:</label>
                    <select name="type" required>
                        <option value="normal">Normal</option>
                        <option value="urgent">Urgent</option>
                    </select>

                    <label for="slot_date">Date:</label>
                    <input type="date" name="slot_date" min="<?= date('Y-m-d'); ?>" required>

                    <label for="slot_time">Time Slot:</label>
                    <select name="slot_time" required>
                        <option value="">Select a time slot</option>
                        <?php foreach ($predefined_slots as $slot) : ?>
                            <option value="<?= $slot; ?>"><?= $slot; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <label for="collection_time">Collection Time:</label>
                    <input type="time" name="collection_time" placeholder="Enter preferred collection time" required>

                    <button type="submit">Book Now</button>
                </form>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 LaundroMate. All rights reserved.</p>
    </footer>

</body>

</html>
<?php if (isset($_SESSION['booking_message'])): ?>
<script>
    alert("<?= $_SESSION['booking_message'] ?>");
</script>
<?php 
    // Clear the message after displaying
    unset($_SESSION['booking_message']);
endif; 
?>