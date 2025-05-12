<?php
// view_resume.php
if (isset($_GET['resume_id'])) {
    $resume_id = $_GET['resume_id'];

    $mysqli = new mysqli("localhost", "root", "", "job");

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Get the file path of the resume
    $stmt = $mysqli->prepare("SELECT file_path, candidate_name, position FROM resumes WHERE resume_id = ?");
    $stmt->bind_param("i", $resume_id);
    $stmt->execute();
    $stmt->bind_result($file_path, $candidate_name, $position);
    $stmt->fetch();
    $stmt->close();

    $mysqli->close();

    if ($file_path) {
        $file_path = "uploads/resumes/" . $file_path;

        if (file_exists($file_path)) {
            // Serve the file to the browser
            $file_info = pathinfo($file_path);
            $file_extension = strtolower($file_info['extension']);
            $mime_type = "application/octet-stream";

            if ($file_extension === "pdf") {
                $mime_type = "application/pdf";
            } elseif ($file_extension === "doc" || $file_extension === "docx") {
                $mime_type = "application/msword";
            }

            header("Content-Type: $mime_type");
            header("Content-Disposition: inline; filename=\"" . basename($file_path) . "\"");
            readfile($file_path);
            exit;
        } else {
            echo "File not found!";
        }
    } else {
        echo "Invalid resume ID!";
    }
} else {
    echo "No resume ID provided!";
}
