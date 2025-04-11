<?php
// DB connection details
$host = "localhost";
$user = "root";
$pass = "";
$db = "student_magement";

// Create a new PDO instance to connect to the database
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle delete request
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    try {
        $sql = "DELETE FROM event_registrations WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $delete_id]);
        $success = "Record deleted successfully.";
    } catch (PDOException $e) {
        $error = "Failed to delete record: " . $e->getMessage();
    }
}

// Fetch all records from event_registrations table
try {
    $query = "SELECT id, name, email, phone, event, message FROM event_registrations";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Failed to fetch registrations: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Event Registrations</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      padding: 20px;
      background-color: #f2f2f2;
    }
    .container {
      background-color: white;
      padding: 30px;
      border-radius: 8px;
      max-width: 900px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: left;
    }
    th {
      background-color: #f4f4f4;
    }
    .delete-btn {
      color: red;
      cursor: pointer;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Event Registrations</h2>

    <?php if (isset($success)): ?>
      <div style="color: green; font-weight: bold;">
        <?php echo $success; ?>
      </div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
      <div style="color: red; font-weight: bold;">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <table>
      <thead>
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Phone</th>
          <th>Event</th>
          <th>Message</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($registrations)): ?>
          <?php foreach ($registrations as $registration): ?>
            <tr>
              <td><?php echo htmlspecialchars($registration['name']); ?></td>
              <td><?php echo htmlspecialchars($registration['email']); ?></td>
              <td><?php echo htmlspecialchars($registration['phone']); ?></td>
              <td><?php echo htmlspecialchars($registration['event']); ?></td>
              <td><?php echo nl2br(htmlspecialchars($registration['message'])); ?></td>
              <td>
                <a href="?delete_id=<?php echo $registration['id']; ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this registration?')">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="6">No registrations found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</body>
</html>
