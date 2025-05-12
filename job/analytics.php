<?php
// analytics.php
$mysqli = new mysqli("localhost", "root", "", "job");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Main statistics
$query = "SELECT COUNT(*) AS total_applications, 
                 SUM(CASE WHEN application_status = 'Applied' THEN 1 ELSE 0 END) AS applied, 
                 SUM(CASE WHEN application_status = 'Interview' THEN 1 ELSE 0 END) AS interviewed,
                 SUM(CASE WHEN application_status = 'Rejected' THEN 1 ELSE 0 END) AS rejected,
                 SUM(CASE WHEN application_status = 'Hired' THEN 1 ELSE 0 END) AS hired,
                 SUM(CASE WHEN application_status = 'Saved' THEN 1 ELSE 0 END) AS saved,
                 SUM(CASE WHEN application_status = 'Offered' THEN 1 ELSE 0 END) AS offered
          FROM applications";
$result = $mysqli->query($query);
$data = $result ? $result->fetch_assoc() : null;

// Additional insights
$companies_result = $mysqli->query("SELECT company_name, COUNT(*) AS total FROM applications GROUP BY company_name ORDER BY total DESC LIMIT 5");
$months_result = $mysqli->query("SELECT DATE_FORMAT(applied_date, '%Y-%m') AS month, COUNT(*) AS total FROM applications GROUP BY month ORDER BY month ASC");
$titles_result = $mysqli->query("SELECT job_title, COUNT(*) AS total FROM applications GROUP BY job_title ORDER BY total DESC LIMIT 5");

// Conversion rate
$conversionRate = ($data && $data['total_applications'] > 0)
    ? round(($data['hired'] / $data['total_applications']) * 100, 2)
    : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics</title>
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
                <a href="applications.php">Applications</a>
                <a href="saved_jobs.php">Saved Jobs</a>
                <a href="calendar.php">Calendar</a>
                <a href="resume_bank.php">Resume Bank</a>
                <a href="analytics.php"class="font-semibold border-b-2 border-black text-dark">Analytics</a>
                <a href="interview_prep.php" >Interview Prep</a>
                <a href="upcoming_interviews.php">Upcoming Interviews</a>
                <a href="settings.php">Settings</a>
            </nav>
        </div>
    </header>

    <main>

    <!-- Main Analytics -->
    <div class="bg-white p-6 rounded-xl shadow-md">
        <h2 class="text-xl font-bold text-dark mb-6">📊 Analytics Overview</h2>

        <?php if ($data): ?>
            <div class="grid grid-cols-2 gap-6">
                <!-- Total Applications -->
                <div class="bg-blue-100 p-4 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold text-dark">Total Applications</h3>
                    <p class="text-2xl font-bold text-blue-600"><?php echo $data['total_applications']; ?></p>
                </div>

                <!-- Applications by Status -->
                <div class="bg-yellow-100 p-4 rounded-lg shadow-lg">
                    <h3 class="text-lg font-semibold text-dark">Applications by Status</h3>
                    <ul class="space-y-2">
                        <li class="text-lg">Applied: <span class="font-bold text-green-600"><?php echo $data['applied']; ?></span></li>
                        <li class="text-lg">Interview: <span class="font-bold text-yellow-600"><?php echo $data['interviewed']; ?></span></li>
                        <li class="text-lg">Rejected: <span class="font-bold text-red-600"><?php echo $data['rejected']; ?></span></li>
                        <li class="text-lg">Hired: <span class="font-bold text-blue-600"><?php echo $data['hired']; ?></span></li>
                        <li class="text-lg">Saved: <span class="font-bold text-purple-600"><?php echo $data['saved']; ?></span></li>
                        <li class="text-lg">Offered: <span class="font-bold text-pink-600"><?php echo $data['offered']; ?></span></li>
                    </ul>
                </div>
            </div>
        <?php else: ?>
            <p class="text-lg text-red-600">No data available for analytics.</p>
        <?php endif; ?>

        <!-- Additional Insights -->
        <div class="mt-8">
            <h3 class="text-xl font-semibold text-dark mb-4">📌 Additional Insights</h3>

            <!-- Conversion Rate -->
            <div class="bg-green-100 p-4 rounded-lg shadow-md mb-4">
                <p class="text-lg">🎯 Conversion Rate (Hired): <span class="font-bold text-green-700"><?php echo $conversionRate; ?>%</span></p>
            </div>

            <!-- Top Companies -->
            <div class="bg-white p-4 rounded-lg shadow-md mb-4">
                <h4 class="text-lg font-bold mb-2 text-dark">🏢 Top Companies Applied To</h4>
                <ul class="list-disc list-inside">
                    <?php while ($row = $companies_result->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($row['company_name']); ?> (<?php echo $row['total']; ?> applications)</li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <!-- Applications by Month -->
            <div class="bg-white p-4 rounded-lg shadow-md mb-4">
                <h4 class="text-lg font-bold mb-2 text-dark">📅 Applications by Month</h4>
                <ul class="list-disc list-inside">
                    <?php while ($row = $months_result->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($row['month']); ?>: <?php echo $row['total']; ?> applications</li>
                    <?php endwhile; ?>
                </ul>
            </div>

            <!-- Most Applied Job Titles -->
            <div class="bg-white p-4 rounded-lg shadow-md">
                <h4 class="text-lg font-bold mb-2 text-dark">💼 Most Applied Job Titles</h4>
                <ul class="list-disc list-inside">
                    <?php while ($row = $titles_result->fetch_assoc()): ?>
                        <li><?php echo htmlspecialchars($row['job_title']); ?> (<?php echo $row['total']; ?> times)</li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
</body>
</html>
