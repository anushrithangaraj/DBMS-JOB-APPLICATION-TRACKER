<?php
// Include database connection
include('db_connection.php');

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
        mkdir($upload_dir, 0777, true); // create directory with permissions
    }

    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
        $attachment = $upload_dir . basename($_FILES['attachment']['name']);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment);
    }

    // Insert into database
    $sql = "INSERT INTO applications (application_id, job_title, company_name, location, job_type, application_status, applied_date, source, salary, notes, attachment) 
            VALUES ('$application_id','$job_title', '$company_name', '$location', '$job_type', '$application_status', '$applied_date', '$source', '$salary', '$notes', '$attachment')";

    if (mysqli_query($conn, $sql)) {
        echo "<p style='color: green; text-align:center;'>✅ Application added successfully!</p>";
    } else {
        echo "<p style='color: red; text-align:center;'>❌ Error: " . $sql . "<br>" . mysqli_error($conn) . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Job Application</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to bottom right, #FFE3D3, rgb(239, 237, 126), rgb(191, 203, 121));
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
            background: linear-gradient(to bottom right, #FFE3D3, rgb(239, 237, 126), rgb(191, 203, 121));
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

        .back-button {
            display: inline-block;
            margin-top: 15px;
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 16px;
            text-align: center;
        }

        .back-button:hover {
            background-color: #218838;
        }

        .back-container {
            text-align: center;
        }
    </style>
</head>
<body>

    <h1>Add Job Application</h1>

    <div class="container">
        <form action="add_application.php" method="POST" enctype="multipart/form-data">
            <label for="application_id">Application ID:</label>
            <input type="text" name="application_id" id="application_id" required>
        
            <label for="job_title">Job Title:</label>
            <input type="text" name="job_title" id="job_title" required>

            <label for="company_name">Company Name:</label>
            <input type="text" name="company_name" id="company_name" required>

            <label for="location">Location:</label>
            <input type="text" name="location" id="location" required>

            <label for="job_type">Job Type:</label>
            <select name="job_type" id="job_type" required>
                <option value="Full-time">Full-time</option>
                <option value="Part-time">Part-time</option>
                <option value="Freelance">Freelance</option>
                <option value="Internship">Internship</option>
            </select>

            <label>Application Status:</label>
            <div class="status-group">
                <label><input type="radio" name="application_status" value="Applied" required> Applied</label>
                <label><input type="radio" name="application_status" value="Interview"> Interview</label>
                <label><input type="radio" name="application_status" value="Offer"> Offer</label>
                <label><input type="radio" name="application_status" value="Rejected"> Rejected</label>
                <label><input type="radio" name="application_status" value="Hired"> Hired</label>
            </div>

            <label for="applied_date">Date Applied:</label>
            <input type="date" name="applied_date" id="applied_date" required>

            <label for="source">Source:</label>
            <input type="text" name="source" id="source" required>

            <label for="salary">Salary:</label>
            <input type="text" name="salary" id="salary">

            <label for="notes">Notes:</label>
            <textarea name="notes" id="notes" rows="4"></textarea>

            <label for="attachment">Attachment (Resume, etc.):</label>
            <input type="file" name="attachment" id="attachment">

            <button type="submit">Submit Application</button>
        </form>

        <div class="back-container">
            <a href="applications.php" class="back-button">⬅ Back to Applications</a>
        </div>
    </div>

</body>
</html>
