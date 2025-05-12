<?php
// saved_jobs.php
$mysqli = new mysqli("localhost", "root", "", "job");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$query = "SELECT s.saved_id, a.job_title, a.company_name, a.location, a.job_type, s.saved_at
          FROM saved_jobs s
          JOIN applications a ON s.application_id = a.application_id
          ORDER BY s.saved_at DESC";

$result = $mysqli->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Saved Jobs</title>
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

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: white;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
      border-radius: 12px;
      overflow: hidden;
    }

    th, td {
      padding: 14px 16px;
      text-align: left;
      border-bottom: 1px solid #E0E0E0;
    }

    th {
      background-color: #F7F9FC;
      color: #2C2C2C;
      font-weight: 600;
    }

    tr:hover {
      background-color: #F3F4F6;
    }

    .action-buttons {
      display: flex;
      gap: 8px;
    }

    .apply-btn {
      background-color: #7ED6DF;
      color: #2C2C2C;
      font-weight: 500;
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .apply-btn:hover {
      background-color: #6FC8CF;
    }

    .remove-btn {
      background-color: #FF7675;
      color: white;
      font-weight: 500;
      padding: 6px 12px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.2s;
    }

    .remove-btn:hover {
      background-color: #E55B5B;
    }
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
                <a href="saved_jobs.php"class="font-semibold border-b-2 border-black text-dark">Saved Jobs</a>
                <a href="calendar.php">Calendar</a>
                <a href="resume_bank.php">Resume Bank</a>
                <a href="analytics.php">Analytics</a>
                <a href="interview_prep.php" >Interview Prep</a>
                <a href="upcoming_interviews.php">Upcoming Interviews</a>
                <a href="settings.php">Settings</a>
            </nav>
        </div>
    </header>
    
    <main>
    <div class="bg-white p-6 rounded-xl shadow-md">
      <h2 class="text-xl font-bold text-dark mb-4">💾 Saved Jobs</h2>
      <?php if ($result && $result->num_rows > 0): ?>
        <div class="overflow-x-auto rounded-xl">
          <table class="text-sm text-dark">
            <thead>
              <tr>
                <th>Job Title</th>
                <th>Company</th>
                <th>Location</th>
                <th>Job Type</th>
                <th>Date Saved</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php while($row = $result->fetch_assoc()): ?>
              <tr>
                <td><?= htmlspecialchars($row['job_title']) ?></td>
                <td><?= htmlspecialchars($row['company_name']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td><?= htmlspecialchars($row['job_type']) ?></td>
                <td><?= htmlspecialchars($row['saved_at']) ?></td>
                <td class="action-buttons">
                  <button class="apply-btn" onclick="alert('Redirect to application page or open modal');">Apply</button>
                  <form method="POST" action="remove_saved_job.php" style="display:inline;">
                    <input type="hidden" name="saved_id" value="<?= $row['saved_id'] ?>">
                    <button type="submit" class="remove-btn">Remove</button>
                  </form>
                </td>
              </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-dark">No saved jobs found.</p>
      <?php endif; ?>
    </div>
  </div>
</body>
</html>
