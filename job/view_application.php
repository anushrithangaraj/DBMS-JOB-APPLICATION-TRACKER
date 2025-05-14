<?php
$mysqli = new mysqli("localhost", "root", "", "job");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$id = isset($_GET['application_id']) ? (int)$_GET['application_id'] : 0;

$stmt = $mysqli->prepare("SELECT * FROM applications WHERE application_id = ?");
if ($stmt === false) {
    die("Error preparing the SQL query: " . $mysqli->error);
}

$stmt->bind_param("i", $id);
if (!$stmt->execute()) {
    die("Error executing the query: " . $stmt->error);
}

$result = $stmt->get_result();
$application = $result->fetch_assoc();

if (!$application) {
    die("Application not found.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen flex items-center justify-center" 
      style="background: linear-gradient(135deg, #FFE3D3, rgb(239, 237, 126), rgb(191, 203, 121));">

    <div class="max-w-3xl w-full mx-4 sm:mx-auto bg-white rounded-2xl shadow-lg p-8 border border-gray-200">
        <h2 class="text-3xl font-bold text-green-700 mb-6 text-center">🔍 Application Details</h2>
        
        <div class="grid grid-cols-1 gap-4 text-gray-800">
            <p><strong>Job Title:</strong> <?php echo htmlspecialchars($application['job_title']); ?></p>
            <p><strong>Company:</strong> <?php echo htmlspecialchars($application['company_name']); ?></p>
            <p><strong>Location:</strong> <?php echo htmlspecialchars($application['location']); ?></p>
            <p><strong>Job Type:</strong> <?php echo htmlspecialchars($application['job_type']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($application['application_status']); ?></p>
            <p><strong>Date Applied:</strong> <?php echo htmlspecialchars($application['applied_date']); ?></p>
            <p><strong>Source:</strong> <?php echo htmlspecialchars($application['source']); ?></p>
            <p><strong>Salary:</strong> <?php echo htmlspecialchars($application['salary']); ?></p>
            <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($application['notes'])); ?></p>

            <p><strong>Attachment:</strong> 
                <?php 
                    if (!empty($application['attachment'])): 
                        $filename = basename($application['attachment']);
                        $encodedFilename = rawurlencode($filename);
                        $filePath = 'uploads/' . $encodedFilename;

                        if (file_exists($filePath)): 
                            echo '<a href="' . $filePath . '" target="_blank" class="text-blue-600 hover:underline">📎 View</a>';
                        else:
                            echo '<span class="text-red-500">File not found!</span>';
                        endif;
                    else: 
                        echo '-';
                    endif;
                ?>
            </p>
        </div>

        <div class="mt-6 text-center">
            <a href="applications.php" class="text-blue-700 hover:underline font-medium">← Back to Applications</a>
        </div>
    </div>

</body>
</html>
