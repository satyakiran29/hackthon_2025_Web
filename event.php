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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate input data
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
    $event = filter_input(INPUT_POST, 'event', FILTER_SANITIZE_STRING);
    $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

    // Check for empty required fields
    if (empty($name) || empty($email) || empty($phone) || empty($event)) {
        $error = "Please fill in all required fields.";
    } else {
        try {
            // Prepare SQL query to insert the form data into the database
            $sql = "INSERT INTO event_registrations (name, email, phone, event, message) 
                    VALUES (:name, :email, :phone, :event, :message)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':event' => $event,
                ':message' => $message
            ]);
            // If the insertion is successful, show the success message
            $success = true;
        } catch (PDOException $e) {
            $error = "Failed to register: " . $e->getMessage();
        }
    }
}

// Fetch event names from the events table
try {
    $eventQuery = "SELECT id, title, event_date, event_time, location FROM events";
    $eventStmt = $pdo->prepare($eventQuery);
    $eventStmt->execute();
    $events = $eventStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Failed to fetch events: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Event Registration Form</title>
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
      max-width: 500px;
      margin: auto;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    input, select, textarea {
      width: 100%;
      padding: 10px;
      margin-top: 8px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    label {
      font-weight: bold;
    }
    button {
      background-color: #4CAF50;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
    }
    button:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>

  <div class="container">
    <h2>Event Registration Form</h2>

    <?php if (isset($error)): ?>
      <div style="color: red; font-weight: bold;">
        <?php echo $error; ?>
      </div>
    <?php endif; ?>

    <?php if (isset($success)): ?>
      <div style="color: green; font-weight: bold;">
        Registration Successful!
      </div>
      <p><strong>Name:</strong> <?php echo htmlspecialchars($name); ?></p>
      <p><strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
      <p><strong>Phone:</strong> <?php echo htmlspecialchars($phone); ?></p>
      <p><strong>Event:</strong> <?php echo htmlspecialchars($event); ?></p>
      <?php if (!empty($message)): ?>
        <p><strong>Message:</strong> <?php echo nl2br(htmlspecialchars($message)); ?></p>
      <?php endif; ?>
    <?php else: ?>
      <form action="" method="POST">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" />

        <label for="email">Email Address:</label>
        <input type="email" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" />

        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" required value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" />

        <label for="event">Select Event:</label>
        <select id="event" name="event" required>
  <option value="">--Please choose an event--</option>
  <?php foreach ($events as $eventRow): ?>
    <option value="<?php echo htmlspecialchars($eventRow['title']); ?>" <?php echo (isset($event) && $event == $eventRow['title']) ? 'selected' : ''; ?>>
      <?php echo htmlspecialchars($eventRow['title']); ?>
    </option>
  <?php endforeach; ?>
</select>

        <label for="message">Additional Message (Optional):</label>
        <textarea id="message" name="message" rows="4"><?php echo isset($message) ? htmlspecialchars($message) : ''; ?></textarea>

        <button type="submit">Register</button>
      </form>
    <?php endif; ?>
  </div>

</body>
</html>
