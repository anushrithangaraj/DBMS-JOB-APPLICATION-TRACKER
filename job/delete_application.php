<?php
$mysqli = new mysqli("localhost", "root", "", "job");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$id = isset($_GET['application_id']) ? (int)$_GET['application_id'] : 0;

if ($id > 0) {
    // Optionally delete the attachment file
    $stmt = $mysqli->prepare("SELECT attachment FROM applications WHERE application_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if ($row && !empty($row['attachment'])) {
        $file_path = 'uploads/' . $row['attachment'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }

    // Delete the record
    $delete = $mysqli->prepare("DELETE FROM applications WHERE application_id = ?");
    $delete->bind_param("i", $id);
    $delete->execute();
}

header("Location: applications.php");
exit;
