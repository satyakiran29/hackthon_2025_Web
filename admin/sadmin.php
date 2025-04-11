<?php

// DB connection
$host = "localhost";
$user = "root";
$pass = "";
$db = "student_magement";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $title = $_POST['title'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $event_category = $_POST['event_category'];
    $student_chapter = $_POST['student_chapter'];
    $year_group = $_POST['year_group'];

    // Prepared statement to insert the data into the database
    $stmt = $conn->prepare("INSERT INTO events (title, event_date, event_time, location, description, event_category, student_chapter, year_group) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $title, $event_date, $event_time, $location, $description, $event_category, $student_chapter, $year_group);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Event Management</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
        .card { background: white; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background: #eee; }
        input, textarea, select { width: 100%; padding: 8px; margin-bottom: 10px; }
        button { padding: 10px 15px; background: #2563eb; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>

<div class="card">
    <h2>Event List</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Date</th>
                <th>Time</th>
                <th>Location</th>
                <th>Description</th>
                <th>Category</th>
                <th>Status</th>
                <th>Year</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $result = $conn->query("SELECT * FROM events ORDER BY event_date ASC");
        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= $row['event_date'] ?></td>
                <td><?= $row['event_time'] ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['event_category']) ?></td>
                <td><?= htmlspecialchars($row['student_chapter']) ?></td>
                <td><?= htmlspecialchars($row['year_group']) ?></td>
            </tr>
        <?php
            endwhile;
        else:
        ?>
            <tr><td colspan="9">No events found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="card">
    <h2>Add New Event</h2>
    <form method="POST">
        <input type="text" name="title" placeholder="Event Title" required>
        <input type="date" name="event_date" required>
        <input type="text" name="event_time" placeholder="Event Time (e.g. 10:00 AM)" required>
        <input type="text" name="location" placeholder="Location" required>
        <textarea name="description" placeholder="Event Description"></textarea>
        <select name="student_chapter" required>
            <option value="">Select Category</option>
            <option value="CSI">CSI</option>
            <option value="ACM">ACM</option>
            <option value="ISTE">ISTE</option>
            <option value="Sports">Sports</option>
            <option value="NCC">NCC</option>
            <option value="NSS">NSS</option>
        </select>
        <select name="event_category" required>
            <option value="">Select Status</option>
            <option value="Current Events">Upcoming</option>
            <option value="Ongoing">Ongoing</option>
            <option value="Completed">Completed</option>
        </select>
        <select name="year_group" required>
            <option value="">Select Year</option>
            <option value="1st">1st Year</option>
            <option value="2nd">2nd Year</option>
            <option value="3rd">3rd Year</option>
            <option value="4th">4th Year</option>
        </select>
        <button type="submit">Add Event</button>
        <a href="/web/studentdetails.php" style="display: inline-block; margin-top: 10px; text-decoration: none; color: #2563eb;">View Details</a>
    </form>
</div>

</body>
</html>
<?php $conn->close(); ?>
