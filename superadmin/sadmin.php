<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "student_magement"); // Fixed typo in DB name

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission for editing user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $role = $_POST['role'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, role = ?, email = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $role, $email, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Handle form submission for deleting user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];

    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch users
$result = $conn->query("SELECT id, name, role, email FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="icon" type="image/png" href="assents/images/favicon.png">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-k6d4wzSIapyDyv1kpU366/PK5hCdSbCRGRCMv+eplOQJWyd1fbcAu9OCUj5zNLiq" crossorigin="anonymous"></script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <style>
    td button, td form {
        display: inline-block;
        margin-right: 5px;
    }
    td button {
        padding: 5px 10px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    td button:hover {
        background-color: #45a049;
    }
    td form button {
        padding: 5px 10px;
        background-color: #f44336;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }
    td form button:hover {
        background-color: #d32f2f;
    }
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        h1, h2 {
            text-align: center;
            color: #4CAF50;
        }
        table {
            width: 90%;
            margin: 20px auto;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #4CAF50;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
       
        form {
            margin: 20px auto;
            width: 90%;
            max-width: 600px;
            background-color: #fff;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="email"] {
            font-family: inherit;
        }
       
    </style>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<
</head>

<body>

    <h1>Manage Users</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['name']) ?></td>
                    <td><?= htmlspecialchars($row['role']) ?></td>
                    <td><?= htmlspecialchars($row['email']) ?></td>
                    <td>
                        <button onclick="editUser(<?= $row['id'] ?>, '<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['role'], ENT_QUOTES) ?>', '<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>')">Edit</button>
                        
                            <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                            <button type="submit" name="delete_user" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                      
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Edit User</h2>
    <form method="POST">
        <input type="hidden" name="user_id" id="user_id">
        <label for="name">Name:</label>
        <input type="text" name="name" id="name" required>
        <label for="role">Role:</label>
        <select name="role" id="role" required>
            <option value="superadmin">Superadmin</option>
            <option value="admin">Admin</option>
            <option value="users">Users</option>
        </select>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
        
        <button type="submit" name="edit_user">Save</button>
        <a href="studentdetails.php" style="display: inline-block; margin-top: 10px; text-decoration: none; color: #2563eb;">View Details</a>
    </form>

    <script>
        function editUser(id, name, role, email) {
            document.getElementById('user_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('role').value = role;
            document.getElementById('email').value = email;
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>