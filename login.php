<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "student_magement"; // make sure this matches your actual DB name

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle login
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
    $password = isset($_POST["password"]) ? $_POST["password"] : '';

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows > 0) {
            $user = $res->fetch_assoc();

            if ($password === $user['password']) {
                switch ($user['role']) {
                    case 'superadmin':
                        header("Location: /web/superadmin/sadmin.php");
                        break;
                    case 'admin':
                        header("Location: /web/admin/sadmin.php");
                        break;
                    case 'student':
                        header("Location: /web/index.php");
                        break;
                    default:
                        $error = "Unknown user role.";
                        break;
                }
                exit;
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "User not found.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="login.css">
</head>

<body>
    <div class="container">
        <form class="login-form" method="POST" action="">
            <h2>Student Login</h2>

            <?php if (isset($error)): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <div class="input-group">
                <label for="email">Email / Phno</label>
                <input type="text" id="email" name="email" required>
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Login</button>

            <p class="message">Not registered? <a href="register.php">Create an account</a></p>
           
        </form>
    </div>
</body>
</html>
