<?php
// Start the session
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_magement"; // Make sure this matches your actual DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize user input
    $first_name = htmlspecialchars($_POST['name']);
    $last_name = htmlspecialchars($_POST['lastname']);
    $phone_number = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);
    $confirm_password = htmlspecialchars($_POST['confirm_password']);
    $role = "Student"; // Default role

    // Check if the email already exists in the database
    $email_check_query = "SELECT email FROM users WHERE email = ?";
    $stmt = $conn->prepare($email_check_query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $error_message = "Email already exists. Please choose another email.";
    } elseif ($password === $confirm_password) {
        // Insert into the database without password hashing
        $stmt = $conn->prepare("INSERT INTO users (name, lastname, phone, email, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $first_name, $last_name, $phone_number, $email, $password, $role);

        if ($stmt->execute()) {
            $success_message = "Registration successful!";
        } else {
            $error_message = "Error: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $error_message = "Passwords do not match. Please try again.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="container">
        <form class="login-form" method="POST" action="">
            <h2>New Register</h2>
            <div class="input-group">
                <label for="name">First Name</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="input-group">
                <label for="lastname">Last Name</label>
                <input type="text" id="lastname" name="lastname" required>
            </div>
            <div class="input-group">
                <label for="phone">Phone Number</label>
                <input type="text" id="phone" name="phone" required>
            </div>
            <div class="input-group">
                <label for="email">Gmail</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="input-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Create account</button>
            <p class="message"><a href="login.php">Login</a></p>
        </form>

        <?php
        if (isset($success_message)) {
            echo "<p style='color: green;'>$success_message</p>";
        }

        if (isset($error_message)) {
            echo "<p style='color: red;'>$error_message</p>";
        }
        ?>
    </div>
</body>
</html>
