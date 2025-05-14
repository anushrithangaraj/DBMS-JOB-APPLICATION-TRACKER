<?php
// save_job.php
$mysqli = new mysqli("localhost", "root", "", "job");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_GET['application_id'])) {
    $application_id = intval($_GET['application_id']);

    // Check if already saved
    $check = $mysqli->query("SELECT * FROM saved_jobs WHERE application_id = $application_id");
    if ($check && $check->num_rows > 0) {
        header("Location: saved_jobs.php?msg=already_saved");
        exit();
    }

    $stmt = $mysqli->prepare("INSERT INTO saved_jobs (application_id) VALUES (?)");
    $stmt->bind_param("i", $application_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: saved_jobs.php?msg=saved");
exit();
?>
