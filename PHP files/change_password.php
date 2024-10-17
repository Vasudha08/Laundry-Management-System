<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['roll_number'])) {
    echo "<p>You must be logged in to change your password.</p>";
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

// Assuming roll number is stored in session
$roll_number = $_SESSION['roll_number'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $retype_password = $_POST['retype_password'];

    // Fetch the user's current password from the database
    $sql = "SELECT password FROM students WHERE roll_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $roll_number);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Compare the current password directly
        if ($current_password === $user['password']) {
            if ($new_password === $retype_password) {
                // Update the password in the database
                $update_sql = "UPDATE students SET password = ? WHERE roll_number = ?";
                $update_stmt = $conn->prepare($update_sql);
                $update_stmt->bind_param("ss", $new_password, $roll_number);

                if ($update_stmt->execute()) {
                    echo "<script>alert('Password changed successfully!');</script>";
                } else {
                    $message = "Error updating password. Please try again.";
                }
                $update_stmt->close();
            } else {
                echo "<script>alert('New passwords do not match. Please try again.');</script>";
                
            }
        } else {
            echo "<script>alert('Current password is incorrect. Please try again.');</script>";
            
        }
    } else {
        echo "<script>alert('User not found. Please contact support.');</script>";
       
    }

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Laundry Management System</title>
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
            margin-left: 150px;
            padding: 10px;
            margin-top: 30px;
            flex-grow: 1;
            height: calc(100vh - 200px);
        }

        .change-password {
            background-color: #ffffff;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            margin: 20px 10px 20px 10px;
            
            width: calc(100% - 300px);
        }

        .change-password h2 {
            margin-top: 0;
            color: #94618E;
            font-size: 24px;
            border-bottom: 2px solid #94618E;
            padding-bottom: 10px;
        }

        input[type="password"] {
            width: 95%;
            padding: 10px;
            margin-top: 10px;
            margin-bottom: 10px;
            margin-right: 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .submit-button {
            background-color: #94618E;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            margin-top: 20px;
            cursor: pointer;
        }

        .submit-button:hover {
            background-color: #805d8c;
        }

        .message {
            margin: 10px 0;
            color: #d9534f; /* Red color for error message */
        }

        .success-message {
            color: #5cb85c; /* Green color for success message */
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
        <div class="change-password">
    <h2>Change Password</h2>
    <form method="POST" action="" onsubmit="return validatePasswords()">
        <label for="current_password">Current Password:</label>
        <input type="password" name="current_password" id="current_password" required>
        
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" id="new_password" required>

        <label for="retype_password">Re-type Password:</label>
        <input type="password" name="retype_password" id="retype_password" required>

        <button type="submit" class="submit-button">Change Password</button>
    </form>
    <?php if (!empty($message)): ?>
        <p class="message <?= (strpos($message, 'success') !== false) ? 'success-message' : '' ?>">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>
</div>

<script>
function validatePasswords() {
    var newPassword = document.getElementById('new_password').value;
    var retypePassword = document.getElementById('retype_password').value;

    if (newPassword !== retypePassword) {
        alert("The new passwords do not match. Please try again.");
        return false; // Prevent form submission
    }
    return true; // Allow form submission
}
</script>

        </div>
    </div>

    <footer>
        <p>&copy; 2024 Laundry Management System. All rights reserved.</p>
    </footer>

</body>

</html>
