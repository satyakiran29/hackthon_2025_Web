<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "student_magement"; // Fixed typo

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$events = [];

// Apply filters if provided
$conditions = [];
if (isset($_GET['event_date']) && !empty($_GET['event_date'])) {
    $selected_date = $conn->real_escape_string($_GET['event_date']);
    $conditions[] = "event_date = '$selected_date'";
}
if (isset($_GET['year_group']) && !empty($_GET['year_group'])) {
    $selected_year = $conn->real_escape_string($_GET['year_group']);
    $conditions[] = "year_group = '$selected_year'";
}
if (isset($_GET['student_chapter']) && !empty($_GET['student_chapter'])) {
    $selected_chapter = $conn->real_escape_string($_GET['student_chapter']);
    $conditions[] = "student_chapter = '$selected_chapter'";
}

$sql = "SELECT * FROM events";
if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY event_date DESC";

$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>GMRIT</title>
  <link rel="stylesheet" href="index.css"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="icon" type="image/png" href="assents/images/favicon.png"/>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
  <header>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
      <div class="container-fluid">
        <img class="nav-logo" src="assents/images/logo2.jpg" alt="logo"/>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav me-auto">
            <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
            <li class="nav-item"><a class="nav-link" href="#">About</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Terms & Conditions</a></li>
            <li class="nav-item"><a class="nav-link disabled">Contact Us</a></li>
          </ul>
          <div class="d-flex">
            <a class="btn btn-primary me-2" href="login.php">Login</a>
            <a class="btn btn-warning" href="register.php">Register</a>
          </div>
        </div>
      </div>
    </nav>
  </header>

  <main>
    <div class="bg-container p-4">
      <div class="text-center mb-4">
        <h1 class="banner-heading">Welcome to GMRIT</h1>
        <p class="baner-description">Your gateway to knowledge and innovation</p>
      </div>

      <!-- Filter Form -->
      <form method="GET" action="" class="mb-4">
        <label for="event_date" class="form-label">Filter by Event Date:</label>
        <input type="date" name="event_date" id="event_date" class="form-control"
               value="<?= isset($_GET['event_date']) ? htmlspecialchars($_GET['event_date']) : '' ?>">
            <label for="year_group" class="form-label mt-3">Filter by Year Group:</label>
            <select name="year_group" id="year_group" class="form-control">
              <option value="">Select Year Group</option>
              <option value="1st" <?= isset($_GET['year_group']) && $_GET['year_group'] == '1st' ? 'selected' : '' ?>>1st</option>
              <option value="2nd" <?= isset($_GET['year_group']) && $_GET['year_group'] == '2nd' ? 'selected' : '' ?>>2nd</option>
              <option value="3rd" <?= isset($_GET['year_group']) && $_GET['year_group'] == '3rd' ? 'selected' : '' ?>>3rd</option>
              <option value="4th" <?= isset($_GET['year_group']) && $_GET['year_group'] == '4th' ? 'selected' : '' ?>>4th</option>
            </select>

        <label for="chapter" class="form-label mt-3">Filter by Chapter:</label>
        <select name="student_chapter" id="chapter" class="form-control">
          <option value="">Select Chapter</option>
          <option value="CSI" <?= isset($_GET['student_chapter']) && $_GET['student_chapter'] == 'CSI' ? 'selected' : '' ?>>CSI</option>
          <option value="ACM" <?= isset($_GET['student_chapter']) && $_GET['student_chapter'] == 'ACM' ? 'selected' : '' ?>>ACM</option>
          <option value="ISTE" <?= isset($_GET['student_chapter']) && $_GET['student_chapter'] == 'ISTE' ? 'selected' : '' ?>>ISTE</option>
          <option value="Sports" <?= isset($_GET['student_chapter']) && $_GET['student_chapter'] == 'Sports' ? 'selected' : '' ?>>Sports</option>
          <option value="NCC" <?= isset($_GET['student_chapter']) && $_GET['student_chapter'] == 'NCC' ? 'selected' : '' ?>>NCC</option>
          <option value="NSS" <?= isset($_GET['student_chapter']) && $_GET['student_chapter'] == 'NSS' ? 'selected' : '' ?>>NSS</option>
        </select>

        <button type="submit" class="btn btn-success mt-3">Filter</button>
      </form>

      <!-- Events Display -->
      <div class="container">
        <h3><?= !empty($conditions) ? 'Filtered Events' : 'All Events' ?></h3>
        <?php if (!empty($events)): ?>
          <?php foreach ($events as $event): ?>
            <div class="card mb-3 shadow-sm">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($event['title']) ?></h5>
                <h6 class="card-subtitle mb-2 text-muted">
                  <strong>Date:</strong> <?= htmlspecialchars($event['event_date']) ?> |
                  <strong>Time:</strong> <?= htmlspecialchars($event['event_time']) ?> |
                  <strong>Venue:</strong> <?= htmlspecialchars($event['location']) ?>
                    | <strong>Chapter:</strong> <?= htmlspecialchars($event['student_chapter']) ?> |
                    <strong>Year:</strong> <?= htmlspecialchars($event['year_group']) ?>
                    <strong>Stauts:</strong> <?= htmlspecialchars($event['event_category']) ?>
                </h6>
                <p class="card-text"><?= nl2br(htmlspecialchars($event['description'])) ?></p>
                <a href="event.php" class="btn btn-primary">Register</a>
              </div>
            </div>
            
          <?php endforeach; ?>
        <?php else: ?>
          <p class="text-muted">No events found<?= !empty($conditions) ? " for the selected filters" : "" ?>.</p>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <script src="script.js"></script>
</body>
</html>