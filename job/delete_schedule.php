<?php
// DB connection
$conn = new mysqli("localhost", "root", "", "job");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    $month = isset($_POST['month']) ? (int)$_POST['month'] : date('n');
    $year = isset($_POST['year']) ? (int)$_POST['year'] : date('Y');

    // Delete query
    $stmt = $conn->prepare("DELETE FROM schedules WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: calendar.php?month=$month&year=$year");
exit;
