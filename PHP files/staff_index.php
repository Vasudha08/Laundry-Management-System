<?php
session_start();

// Database connection
$servername = "localhost"; // Adjust if necessary
$username = "root";         // Adjust if necessary
$password = "vasu0812";    // Adjust if necessary
$dbname = "laundry_management"; // Adjust if necessary

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname,3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $staff_id = $_POST['staff_id']; // Make sure this matches the input field name
    $password = $_POST['password'];

    // Check if student ID exists
    $sql = "SELECT * FROM staff WHERE staff_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $staff_id); // Use $roll_number here
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        

        if ($password == $row['password']) {
            // Success: Redirect to the next page
            $_SESSION['staff_id'] = $row['staff_id']; 
            header("Location: staff_dashboard.php");
            exit();
        } else {
            echo "<script>alert('Incorrect password.');</script>";
        }
        
    } else {
        // Student ID does not exist
        echo "<script>alert('Staff ID not found.');</script>";
    }

    $stmt->close();
}

// Close connection
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laundry Management System</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #94618E;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-image: url('32446.jpg');
            background-size: cover;
            background-position: center;
        }

        .container {
            display: flex;
            max-width: 1200px;
            width: 75%;
            height: 55%;
            background-color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            overflow: hidden;
            transform: scale(1.25);
        }

        .left-section {
            flex: 1;
            padding: 20px;
            background-color: #F8EEE7;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .left-section img {
            max-width: 90%;
            height: auto;
        }

        .right-section {
            flex: 1;
            padding: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .right-section h2 {
            font-size: 28px;
            color: #94618E;;
            background-color: #F8EEE7;
            padding: 10px;
            text-align: center;
            border-radius: 5px;
            width: 75%;
            margin: auto;
        }

        .right-section h4 {
            font-size: 20px;
            color: black;
            padding: 5px;
            text-align: center;
            border-radius: 2px;
            margin-bottom: 1px;
            margin-top: 5px;
        }

        .tagline {
            font-size: 16px;
            color: #666;
            margin-bottom: 20px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            width : 80%;
            margin: auto;
            
        }

        .login-form input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-form button {
            padding: 10px;
            background-color: #94618E;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            
        }

        .login-form button:hover {
            background-color: #218838;
        }

        .staff-login {
            
            font-size: 14px;
            margin: auto;
        }

        .staff-login a {
            color: #007bff;
            text-decoration: none;
            margin: auto;
        }

        .staff-login a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="left-section">
        <img src="home.jpg" alt="Laundry Management">
    </div>
    <div class="right-section">
        <h2>LaundroMate</h2>
        <h4> STAFF LOGIN</h4>
        <form class="login-form" action="" method="POST">
            <input type="text" name="staff_id" placeholder="Staff ID" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        
    </div>
</div>

</body>
</html>
