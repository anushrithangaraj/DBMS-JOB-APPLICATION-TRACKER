<?php
// delete_resume.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resume_id'])) {
    $resume_id = $_POST['resume_id'];

    $mysqli = new mysqli("localhost", "root", "", "job");

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // First get the file path
    $stmt = $mysqli->prepare("SELECT file_path FROM resumes WHERE resume_id = ?");
    $stmt->bind_param("i", $resume_id);
    $stmt->execute();
    $stmt->bind_result($file_path);
    $stmt->fetch();
    $stmt->close();

    if ($file_path) {
        $full_path = "uploads/resumes/" . $file_path;

        // Delete file from server
        if (file_exists($full_path)) {
            unlink($full_path);
        }

        // Delete record from database
        $stmt = $mysqli->prepare("DELETE FROM resumes WHERE resume_id = ?");
        $stmt->bind_param("i", $resume_id);
        $stmt->execute();
        $stmt->close();
    }

    $mysqli->close();
}

header("Location: resume_bank.php");
exit();
