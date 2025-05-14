<?php
$mysqli = new mysqli("localhost", "root", "", "job");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Handle search query
$search = isset($_GET['search']) ? $mysqli->real_escape_string($_GET['search']) : '';

$query = "SELECT * FROM applications";
if (!empty($search)) {
    $query .= " WHERE job_title LIKE '%$search%' OR company_name LIKE '%$search%' OR location LIKE '%$search%'";
}
$query .= " ORDER BY application_id ASC"; // Ascending order

$result = $mysqli->query($query);
if (!$result) {
    die("❌ Query failed: " . $mysqli->error);
}

// Upcoming interviews
$interview_result = $mysqli->query("SELECT company_name FROM applications WHERE application_status = 'Interview'");

// Fetch Recent Applications
$recent_result = $mysqli->query("SELECT job_title, company_name, application_status FROM applications ORDER BY application_id DESC LIMIT 4");

// Fetch Saved Jobs (Join saved_jobs with applications)
$saved_jobs_result = $mysqli->query("
    SELECT a.job_title, a.company_name, a.application_status
    FROM saved_jobs s
    JOIN applications a ON s.application_id = a.application_id
    ORDER BY s.saved_at DESC
");

// Application status overview
$overview_result = $mysqli->query("
    SELECT
        SUM(application_status = 'Saved') as saved,
        SUM(application_status = 'Applied') as applied,
        SUM(application_status = 'Offered') as offered,
        SUM(application_status = 'Rejected') as rejected,
        SUM(application_status = 'Interviewed') as interview,
        SUM(application_status = 'Accepted') as accepted
    FROM applications
");
$overview = $overview_result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Job Application Tracker</title>
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
    .text-dark { color:rgb(221, 112, 149); }
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
                <a href="applications.php"class="font-semibold border-b-2 border-black text-dark">Applications</a>
                <a href="saved_jobs.php">Saved Jobs</a>
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
    <!-- Page Header & Search -->
    <main class="mt-20">
      <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
        <!-- Left: Title -->
        <h2 class="text-xl font-bold text-dark">📄 All Applications</h2>

        <!-- Center: Search -->
        <form method="GET" action="applications.php" class="flex flex-grow justify-center max-w-xl w-full">
          <input type="text" name="search" placeholder="🔍 Search by job, company, location..." value="<?= htmlspecialchars($search) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-l-md focus:outline-none focus:ring-2 focus:ring-orange-300">
          <button type="submit" class="btn-orange px-4 rounded-r-md">Search</button>
        </form>

        <!-- Right: Add Button -->
        <a href="add_application.php" class="btn-orange py-2 px-4 rounded hover:opacity-90 whitespace-nowrap">+ Add Application</a>
      </div>

      <!-- Table -->
      <div class="bg-white p-6 rounded-xl shadow-md overflow-x-auto">
        <table class="min-w-full text-sm text-left text-dark">
          <thead class="bg-gray-100 text-gray-700">
            <tr>
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Job Title</th>
              <th class="px-4 py-2">Company</th>
              <th class="px-4 py-2">Location</th>
              <th class="px-4 py-2">Type</th>
              <th class="px-4 py-2">Status</th>
              <th class="px-4 py-2">Applied</th>
              <th class="px-4 py-2">Source</th>
              <th class="px-4 py-2">Salary</th>
              <th class="px-4 py-2">Notes</th>
              <th class="px-4 py-2">Attachment</th>
              <th class="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr class="border-b hover:bg-gray-50">
                <td class="px-4 py-2"><?= htmlspecialchars($row['application_id']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['job_title']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['company_name']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['location']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['job_type']) ?></td>
                <td class="px-4 py-2">
                  <?php
                    $status = htmlspecialchars($row['application_status']);
                    $colorMap = [
                      'Saved' => 'bg-gray-400 text-white',
                      'Applied' => 'bg-blue-300 text-white',
                      'Offered' => 'bg-yellow-300 text-black',
                      'Rejected' => 'bg-red-400 text-white',
                      'Interview' => 'bg-indigo-300 text-white',
                      'Accepted' => 'bg-green-300 text-black'
                    ];
                    $statusClass = $colorMap[$status] ?? 'bg-gray-200';
                  ?>
                  <span class="px-2 py-1 rounded text-xs font-semibold <?= $statusClass ?>">
                    <?= $status ?>
                  </span>
                </td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['applied_date']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['source']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['salary']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['notes']) ?></td>
                <td class="px-4 py-2">
                  <?php if (!empty($row['attachment'])): ?>
                    <a href="uploads/<?= rawurlencode(basename($row['attachment'])) ?>" target="_blank" class="text-blue-600 hover:underline">📎 View</a>
                  <?php else: ?> - <?php endif; ?>
                </td>
                <td class="px-4 py-2 space-x-1">
                  <a href="edit_application.php?application_id=<?= $row['application_id'] ?>" class="text-blue-600 hover:underline">✏️ Edit</a>
                  <a href="view_application.php?application_id=<?= $row['application_id'] ?>" class="text-green-600 hover:underline">🔍 View</a>
                  <a href="delete_application.php?application_id=<?= $row['application_id'] ?>" class="text-red-600 hover:underline" onclick="return confirm('Are you sure you want to delete this application?')">🗑️ Delete</a>
                  <a href="save_job.php?application_id=<?= $row['application_id'] ?>" class="text-yellow-600 hover:underline">💾 Save</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>
</html>
