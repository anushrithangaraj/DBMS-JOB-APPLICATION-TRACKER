<?php
$mysqli = new mysqli("localhost", "root", "", "job");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['mock_file'])) {
    $title = $mysqli->real_escape_string($_POST['title']);
    $file = $_FILES['mock_file'];
    $targetDir = "uploads/mock_interviews/";

    // Ensure the directory exists
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $filename = basename($file['name']);
    $targetFile = $targetDir . time() . "_" . $filename;

    // Move the file to the target directory
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        $stmt = $mysqli->prepare("INSERT INTO mock_interviews (title, file_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $title, $targetFile);
        $stmt->execute();
        $stmt->close();
    }
}

// Handle file deletion
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $res = $mysqli->query("SELECT file_path FROM mock_interviews WHERE id = $id");
    if ($row = $res->fetch_assoc()) {
        // Delete the file from the server
        if (file_exists($row['file_path'])) {
            unlink($row['file_path']);
        }
        // Delete the record from the database
        $mysqli->query("DELETE FROM mock_interviews WHERE id = $id");
    }
}

$uploads = $mysqli->query("SELECT * FROM mock_interviews ORDER BY uploaded_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Interview Prep</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #FFE3D3,rgb(239, 237, 126),rgb(191, 203, 121));
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .text-dark { color:rgb(221, 112, 149); }
    </style>
</head>
<body>
<div class="max-w-6xl mx-auto">
    <!-- Navbar/Header -->
    <header class="fixed w-full top-0 left-0 bg-white shadow-md z-50">
        <div class="flex items-center justify-between p-4 mb-6">
            <h1 class="text-2xl font-bold text-dark">🏠 Job Application Tracker</h1>
            <nav class="space-x-4 text-gray-700 text-sm">
                <a href="index.php">Home</a>
                <a href="applications.php">Applications</a>
                <a href="saved_jobs.php">Saved Jobs</a>
                <a href="calendar.php">Calendar</a>
                <a href="resume_bank.php">Resume Bank</a>
                <a href="analytics.php">Analytics</a>
                <a href="interview_prep.php" class="font-semibold border-b-2 border-black text-dark">Interview Prep</a>
                <a href="upcoming_interviews.php">Upcoming Interviews</a>
                <a href="settings.php">Settings</a>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <div class="mt-20 bg-white p-6 rounded-xl shadow-md space-y-8">
        <section>
            <h2 class="text-2xl font-bold text-dark mb-4">🧠 Interview Preparation</h2>
            <p class="mb-4 text-gray-700">Get ready to ace your interviews with top questions and tips!</p>

            <!-- Common Questions -->
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-dark mb-2">📋 Common Interview Questions</h3>
                <ul class="list-disc list-inside text-gray-800 space-y-2">
                    <li>Tell me about yourself.</li>
                    <li>Why do you want to work for our company?</li>
                    <li>What are your strengths and weaknesses?</li>
                    <li>Describe a challenge you’ve faced and how you handled it.</li>
                    <li>Where do you see yourself in 5 years?</li>
                    <li>Why should we hire you?</li>
                    <li>Tell me about a successful project you worked on.</li>
                </ul>
            </div>

            <!-- Tips Section -->
            <div class="mb-6">
                <h3 class="text-xl font-semibold text-dark mb-2">💡 Tips for Success</h3>
                <ul class="list-disc list-inside text-gray-800 space-y-2">
                    <li>Research the company and the role thoroughly.</li>
                    <li>Practice answering questions out loud or with a friend.</li>
                    <li>Dress professionally, even for virtual interviews.</li>
                    <li>Prepare questions to ask the interviewer.</li>
                    <li>Use the STAR method for behavioral questions.</li>
                    <li>Be confident, but not arrogant.</li>
                    <li>Follow up with a thank-you email after the interview.</li>
                </ul>
            </div>

            <!-- Resources -->
            <div>
                <h3 class="text-xl font-semibold text-dark mb-2">🔗 Resources</h3>
                <ul class="list-disc list-inside text-blue-700 space-y-2">
                    <li><a href="https://www.geeksforgeeks.org/hr-interview-questions/" target="_blank" class="underline">GeeksforGeeks HR Questions</a></li>
                    <li><a href="https://www.interviewbit.com/hr-interview-questions/" target="_blank" class="underline">InterviewBit Interview Questions</a></li>
                    <li><a href="https://www.linkedin.com/learning/" target="_blank" class="underline">LinkedIn Learning</a></li>
                    <li><a href="https://www.pramp.com/" target="_blank" class="underline">Pramp - Practice Interviews</a></li>
                </ul>
            </div>
        </section>

        <!-- Upload Form -->
        <section>
            <h3 class="text-xl font-semibold text-dark mb-4">🎥 Upload Your Mock Interviews</h3>
            <form method="POST" enctype="multipart/form-data" class="space-y-4 bg-gray-50 p-4 rounded-lg">
                <input type="text" name="title" placeholder="Enter title" required class="w-full p-2 border border-gray-300 rounded">
                <input type="file" name="mock_file" required class="w-full">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Upload</button>
            </form>
        </section>

        <!-- Uploaded Files -->
        <section>
            <h3 class="text-xl font-semibold text-dark mb-4">📂 Uploaded Mock Interviews</h3>
            <?php if ($uploads && $uploads->num_rows > 0): ?>
                <table class="min-w-full text-sm text-left text-dark">
                    <thead class="bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">Title</th>
                            <th class="px-4 py-2">Uploaded At</th>
                            <th class="px-4 py-2">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $uploads->fetch_assoc()): ?>
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['title']); ?></td>
                            <td class="px-4 py-2"><?php echo $row['uploaded_at']; ?></td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="<?php echo $row['file_path']; ?>" target="_blank" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">View</a>
                                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this file?')" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">Delete</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-gray-600">No mock interviews uploaded yet.</p>
            <?php endif; ?>
        </section>
    </div>
</div>
</body>
</html>
