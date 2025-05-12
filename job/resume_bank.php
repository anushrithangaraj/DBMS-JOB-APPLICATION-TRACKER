<?php
// resume_bank.php
$mysqli = new mysqli("localhost", "root", "", "job");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$query = "SELECT * FROM resumes ORDER BY uploaded_at DESC";
$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Resume Bank</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(to bottom right, #FFE3D3,rgb(239, 237, 126),rgb(191, 203, 121));
      margin: 0;
      padding-top: 60px; /* Space for fixed header */
      overflow-x: hidden;
    }
    header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 50;
      background: white; /* Ensures header remains visible */
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Optional: for shadow effect */
    }
    main {
      margin-top: 80px; /* Adjust for fixed header height */
    }
    .text-dark { color: rgb(221, 112, 149); }
    .btn-orange { background-color: #FF5722; color: white; }
 
    .btn-delete {
      background-color: #ef4444;
      color: white;
      padding: 4px 10px;
      border-radius: 4px;
    }
    .btn-delete:hover {
      background-color: #dc2626;
    }
    .btn-view {
      background-color: #2563eb;
      color: white;
      padding: 4px 10px;
      border-radius: 4px;
    }
    .btn-view:hover {
      background-color: #1d4ed8;
    }
  </style>
</head>
<body>
  <div class="max-w-7xl mx-auto">
  
<body class="min-h-screen p-4 font-sans">
  <div class="max-w-7xl mx-auto">
    <!-- Navbar/Header -->
    <header class="w-full top-0 left-0">
        <div class="flex items-center justify-between p-4 mb-6">
            <h1 class="text-2xl font-bold text-dark">🏠 Job Application Tracker</h1>
            <nav class="space-x-4 text-gray-700 text-sm">
                <a href="index.php">Home</a>
                <a href="applications.php">Applications</a>
                <a href="saved_jobs.php">Saved Jobs</a>
                <a href="calendar.php">Calendar</a>
                <a href="resume_bank.php"class="font-semibold border-b-2 border-black text-dark">Resume Bank</a>
                <a href="analytics.php">Analytics</a>
                <a href="interview_prep.php" >Interview Prep</a>
                <a href="upcoming_interviews.php">Upcoming Interviews</a>
                <a href="settings.php">Settings</a>
            </nav>
        </div>
    </header>

    <main>

    <!-- Content -->
    <div class="bg-white p-6 rounded-xl shadow-md">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-dark">📁 Resume Bank</h2>
        <a href="upload_resume.php" class="bg-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded">
          ➕ Upload Resume
        </a>
      </div>

      <table class="min-w-full text-sm text-left text-dark">
        <thead class="bg-gray-100 text-gray-700">
          <tr>
            <th class="px-4 py-2">Resume ID</th>
            <th class="px-4 py-2">Candidate Name</th>
            <th class="px-4 py-2">Position</th>
            <th class="px-4 py-2">Uploaded At</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $result->fetch_assoc()): ?>
          <tr class="border-b hover:bg-gray-50">
            <td class="px-4 py-2"><?php echo htmlspecialchars($row['resume_id']); ?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($row['candidate_name']); ?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($row['position']); ?></td>
            <td class="px-4 py-2"><?php echo htmlspecialchars($row['uploaded_at']); ?></td>
            <td class="px-4 py-2 space-x-2">
              <a href="uploads/resumes/<?php echo rawurlencode($row['file_path']); ?>" target="_blank" class="btn-view">📄 View</a>
              <form method="POST" action="delete_resume.php" style="display:inline;">
                <input type="hidden" name="resume_id" value="<?php echo $row['resume_id']; ?>">
                <button type="submit" class="btn-delete" onclick="return confirm('Are you sure you want to delete this resume?')">🗑️ Delete</button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
