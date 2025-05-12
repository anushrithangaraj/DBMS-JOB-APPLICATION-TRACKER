<?php
// Include database connection
include('db_connection.php');

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $application_id = $_POST['application_id'];
    $job_title = $_POST['job_title'];
    $company_name = $_POST['company_name'];
    $location = $_POST['location'];
    $job_type = $_POST['job_type'];
    $application_status = $_POST['application_status'];
    $applied_date = $_POST['applied_date'];
    $source = $_POST['source'];
    $salary = $_POST['salary'];
    $notes = $_POST['notes'];

    // Handle file upload for attachments
    $attachment = '';
    $upload_dir = 'uploads/';

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create directory if not exists
    }

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $attachment = $upload_dir . basename($_FILES['attachment']['name']);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment);
    }

    // Use a prepared statement to update the application
    $stmt = $conn->prepare("UPDATE applications 
                            SET job_title = ?, company_name = ?, location = ?, job_type = ?, 
                                application_status = ?, applied_date = ?, source = ?, 
                                salary = ?, notes = ?, attachment = ? 
                            WHERE application_id = ?");
    $stmt->bind_param("ssssssssssi", $job_title, $company_name, $location, $job_type, 
                      $application_status, $applied_date, $source, $salary, $notes, $attachment, $application_id);

    if ($stmt->execute()) {
        echo "<p style='color: green; text-align:center;'>✅ Application updated successfully!</p>";
    } else {
        echo "<p style='color: red; text-align:center;'>❌ Error: " . $stmt->error . "</p>";
    }

    // Close the prepared statement
    $stmt->close();
}

// Fetch the application to edit based on application_id
if (isset($_GET['application_id'])) {
    $application_id = $_GET['application_id'];
    
    // Use a prepared statement to fetch the application
    $stmt = $conn->prepare("SELECT * FROM applications WHERE application_id = ?");
    $stmt->bind_param("i", $application_id);  // "i" indicates that the parameter is an integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $applications = $result->fetch_assoc();
    } else {
        echo "<p style='color: red; text-align:center;'>❌ Application not found!</p>";
        exit;
    }

    // Close the prepared statement
    $stmt->close();
} else {
    echo "<p style='color: red; text-align:center;'>❌ No application ID provided!</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Job Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: linear-gradient(to bottom right, #FFE3D3, rgb(239, 237, 126), rgb(191, 203, 121));
            margin: 0;
            padding: 0;
        }

        h1 {
            background-color: rgb(215, 61, 104);
            color: white;
            text-align: center;
            padding: 20px;
            margin: 0;
        }

        .container {
            width: 60%;
            margin: 20px auto;
            padding: 25px;
            background-color: linear-gradient(to bottom right, #FFE3D3, rgb(239, 237, 126), rgb(191, 203, 121));
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
            color: #333;
        }

        input[type="text"],
        input[type="date"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 10px;
            margin: 8px 0 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 15px;
        }

        textarea {
            resize: vertical;
        }

        .status-group input[type="radio"] {
            margin-right: 8px;
        }

        .status-group label {
            display: inline-block;
            margin-right: 20px;
        }

        button {
            background-color: rgb(175, 76, 81);
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: rgb(160, 69, 78);
        }

        /* Back Button in Top-Left Corner */
        .back-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 25px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-decoration: none;
            position: absolute;
            top: 20px;
            left: 20px;
        }

        .back-button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <!-- Back Button -->
    <a href="applications.php" class="back-button">Back</a>

    <h1>Edit Job Application</h1>

    <div class="container">
        <form action="edit_application.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="application_id" value="<?php echo $applications['application_id']; ?>">

            <label for="job_title">Job Title:</label>
            <input type="text" name="job_title" id="job_title" value="<?php echo $applications['job_title']; ?>" required>

            <label for="company_name">Company Name:</label>
            <input type="text" name="company_name" id="company_name" value="<?php echo $applications['company_name']; ?>" required>

            <label for="location">Location:</label>
            <input type="text" name="location" id="location" value="<?php echo $applications['location']; ?>" required>

            <label for="job_type">Job Type:</label>
            <select name="job_type" id="job_type" required>
                <option value="Full-time" <?php if ($applications['job_type'] == 'Full-time') echo 'selected'; ?>>Full-time</option>
                <option value="Part-time" <?php if ($applications['job_type'] == 'Part-time') echo 'selected'; ?>>Part-time</option>
                <option value="Freelance" <?php if ($applications['job_type'] == 'Freelance') echo 'selected'; ?>>Freelance</option>
                <option value="Internship" <?php if ($applications['job_type'] == 'Internship') echo 'selected'; ?>>Internship</option>
            </select>

            <label>Application Status:</label>
            <div class="status-group">
                <label><input type="radio" name="application_status" value="Applied" <?php if ($applications['application_status'] == 'Applied') echo 'checked'; ?>> Applied</label>
                <label><input type="radio" name="application_status" value="Interview" <?php if ($applications['application_status'] == 'Interview') echo 'checked'; ?>> Interviewed</label>
                <label><input type="radio" name="application_status" value="Offer" <?php if ($applications['application_status'] == 'Offered') echo 'checked'; ?>> Offered</label>
                <label><input type="radio" name="application_status" value="Rejected" <?php if ($applications['application_status'] == 'Rejected') echo 'checked'; ?>> Rejected</label>
                <label><input type="radio" name="application_status" value="Hired" <?php if ($applications['application_status'] == 'Hired') echo 'checked'; ?>> Hired</label>
            </div>

            <label for="applied_date">Date Applied:</label>
            <input type="date" name="applied_date" id="applied_date" value="<?php echo $applications['applied_date']; ?>" required>

            <label for="source">Source:</label>
            <input type="text" name="source" id="source" value="<?php echo $applications['source']; ?>" required>

            <label for="salary">Salary:</label>
            <input type="text" name="salary" id="salary" value="<?php echo $applications['salary']; ?>">

            <label for="notes">Notes:</label>
            <textarea name="notes" id="notes" rows="4"><?php echo $applications['notes']; ?></textarea>

            <label for="attachment">Attachment (Resume, etc.):</label>
            <input type="file" name="attachment" id="attachment">

            <button type="submit">Update Application</button>
        </form>
    </div>

</body>
</html>
