<?php
$conn = new mysqli("localhost", "root", "", "job");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$date = $_POST['schedule_date'];
$title = $_POST['title'];
$desc = $_POST['description'];

$stmt = $conn->prepare("INSERT INTO schedules (schedule_date, title, description) VALUES (?, ?, ?)");
$stmt->bind_param("sss", $date, $title, $desc);
$stmt->execute();

header("Location: calendar.php");
?>
