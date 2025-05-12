<?php
$mysqli = new mysqli("localhost", "root", "", "job");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT * FROM applications WHERE application_status = 'Interview' ORDER BY applied_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upcoming Interviews</title>
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
  </style>
</head>
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
                <a href="resume_bank.php">Resume Bank</a>
                <a href="analytics.php">Analytics</a>
                <a href="interview_prep.php" >Interview Prep</a>
                <a href="upcoming_interviews.php"class="font-semibold border-b-2 border-black text-dark">Upcoming Interviews</a>
                <a href="settings.php">Settings</a>
            </nav>
        </div>
    </header>

    <main>
    <div class="bg-white p-6 rounded-xl shadow-md">
      <h2 class="text-xl font-semibold mb-4 text-dark">Interviews Scheduled</h2>
      <?php if ($result && $result->num_rows > 0): ?>
        <table class="w-full text-left text-dark text-sm">
          <thead>
            <tr class="bg-gray-100">
              <th class="px-4 py-2">Job Title</th>
              <th class="px-4 py-2">Company</th>
              <th class="px-4 py-2">Location</th>
              <th class="px-4 py-2">Job Type</th>
              <th class="px-4 py-2">Applied Date</th>
              <th class="px-4 py-2">Notes</th>
              <th class="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
              <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-2"><?= htmlspecialchars($row['job_title']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['company_name']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['location']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['job_type']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['applied_date']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['notes']) ?></td>
                <td class="px-4 py-2">
                  <a href="view_application.php?application_id=<?= $row['application_id'] ?>" class="text-blue-600 hover:underline">🔍 View</a> |
                  <a href="edit_application.php?application_id=<?= $row['application_id'] ?>" class="text-green-600 hover:underline">✏️ Edit</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-dark">No upcoming interviews found.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
