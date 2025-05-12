<?php
$mysqli = new mysqli("localhost", "root", "", "job");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['saved_id'])) {
    $saved_id = intval($_POST['saved_id']);

    $stmt = $mysqli->prepare("DELETE FROM saved_jobs WHERE saved_id = ?");
    $stmt->bind_param("i", $saved_id);
    $stmt->execute();

    $stmt->close();
}

$mysqli->close();
header("Location: saved_jobs.php");
exit;
