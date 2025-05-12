<?php
$mysqli = new mysqli("localhost", "root", "", "job");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Count saved jobs from saved_jobs table
$saved_count_result = $mysqli->query("SELECT COUNT(*) AS saved FROM saved_jobs");
$saved_count = $saved_count_result->fetch_assoc()['saved'];

// Count application statuses
$overview_result = $mysqli->query("
    SELECT
        SUM(application_status = 'Applied') as applied,
        SUM(application_status = 'Offered') as offered,
        SUM(application_status = 'Rejected') as rejected,
        SUM(application_status = 'Interview') as interview,
        SUM(application_status = 'Accepted') as accepted
    FROM applications
");

$overview = $overview_result->fetch_assoc();
$overview['saved'] = $saved_count;

// Upcoming interviews
$interview_result = $mysqli->query("SELECT company_name FROM applications WHERE application_status = 'Interview'");

// Fetch recent applications
$recent_result = $mysqli->query("SELECT job_title, company_name, application_status FROM applications ORDER BY application_id DESC LIMIT 4");

// Fetch saved jobs (from join)
$saved_jobs_result = $mysqli->query("
    SELECT a.job_title, a.company_name, a.application_status
    FROM saved_jobs s
    JOIN applications a ON s.application_id = a.application_id
    ORDER BY s.saved_at DESC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Job Application Tracker</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background: linear-gradient(to bottom right, #FFE3D3, rgb(239, 237, 126), rgb(191, 203, 121));
      margin: 0;
      padding-top: 60px;
      overflow-x: hidden;
    }
    header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 50;
      background: white;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    main {
      margin-top: 80px;
    }
    .text-dark { color:rgb(221, 112, 149); }
    .btn-orange { background-color: #FF5722; color: white; }
  </style>
</head>
<body class="min-h-screen p-4 font-sans">
  <div class="max-w-7xl mx-auto">
    <!-- Navbar -->
    <header class="w-full top-0 left-0">
      <div class="flex items-center justify-between p-4 mb-6">
        <h1 class="text-2xl font-bold text-dark">🏠 Job Application Tracker</h1>
        <nav class="space-x-4 text-gray-700 text-sm">
          <a href="index.php" class="font-semibold border-b-2 border-black text-dark">Home</a>
          <a href="applications.php">Applications</a>
          <a href="saved_jobs.php">Saved Jobs</a>
          <a href="calendar.php">Calendar</a>
          <a href="resume_bank.php">Resume Bank</a>
          <a href="analytics.php">Analytics</a>
          <a href="interview_prep.php">Interview Prep</a>
          <a href="upcoming_interviews.php">Upcoming Interviews</a>
          <a href="settings.php">Settings</a>
        </nav>
      </div>
    </header>

    <main>
      <!-- Overview Cards -->
      <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-8">
        <?php
          $labels = ['saved', 'applied', 'offered', 'rejected', 'interview', 'accepted'];
          foreach ($labels as $label):
        ?>
        <div class="text-center bg-white p-4 rounded-xl shadow">
          <div class="text-xl font-bold"><?= (int)$overview[$label] ?></div>
          <div class="text-sm text-gray-700"><?= ucfirst($label) ?></div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Add New, Interviews, Chart -->
      <div class="grid md:grid-cols-3 gap-6 mb-6">
        <div class="p-6 bg-white rounded-xl shadow-md">
          <h2 class="text-xl font-semibold mb-4 text-dark">Stay on top<br>Your job hunt!</h2>
          <a href="add_application.php" class="btn-orange py-2 px-4 rounded inline-block hover:opacity-90">+ Add New Application</a>
        </div>

        <div class="p-6 bg-white rounded-xl shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-dark">Upcoming Interviews</h2>
          <?php if ($interview_result && $interview_result->num_rows > 0): ?>
            <?php while($row = $interview_result->fetch_assoc()): ?>
              <p class="mb-2 text-dark">✅ <strong><?= htmlspecialchars($row['company_name']) ?></strong></p>
            <?php endwhile; ?>
          <?php else: ?>
            <p>No upcoming interviews.</p>
          <?php endif; ?>
        </div>

        <div class="p-6 bg-white rounded-xl shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-dark">Applications Overview</h2>
          <canvas id="statusChart"></canvas>
        </div>
      </div>

      <!-- Recent Applications, Saved Jobs, Tips -->
      <div class="grid md:grid-cols-3 gap-6">
        <!-- Recent Applications -->
        <div class="p-6 bg-white rounded-xl shadow-md">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-dark">Recent Applications</h2>
            <a href="applications.php" class="text-blue-600">View All</a>
          </div>
          <table class="w-full text-left text-dark text-sm">
            <thead>
              <tr>
                <th>Job Title</th>
                <th>Company</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if ($recent_result && $recent_result->num_rows > 0): ?>
                <?php while($row = $recent_result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['job_title']) ?></td>
                    <td><?= htmlspecialchars($row['company_name']) ?></td>
                    <td><?= htmlspecialchars($row['application_status']) ?></td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr><td colspan="3">No recent applications.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>

        <!-- Saved Jobs -->
        <div class="p-6 bg-white rounded-xl shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-dark">Saved Jobs</h2>
          <?php if ($saved_jobs_result && $saved_jobs_result->num_rows > 0): ?>
            <table class="w-full text-left text-dark text-sm">
              <thead>
                <tr>
                  <th>Job Title</th>
                  <th>Company</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php while($row = $saved_jobs_result->fetch_assoc()): ?>
                  <tr>
                    <td><?= htmlspecialchars($row['job_title']) ?></td>
                    <td><?= htmlspecialchars($row['company_name']) ?></td>
                    <td><?= htmlspecialchars($row['application_status']) ?></td>
                  </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p>No saved jobs.</p>
          <?php endif; ?>
          <a href="saved_jobs.php" class="text-blue-600 mt-4 inline-block">View All</a>
        </div>

        <!-- Tips Section -->
        <div class="p-6 bg-white rounded-xl shadow-md">
          <h2 class="text-lg font-semibold mb-4 text-dark">Tips & Resources</h2>
          <ul class="list-disc list-inside text-dark">
            <li>How to Prepare for Tech Interviews</li>
            <li>Best Resume for Designers</li>
            <li>Top Sites for Remote Jobs</li>
          </ul>
          <a href="interview_prep.php" class="text-blue-600 mt-4 inline-block">View All</a>
        </div>
      </div>
    </main>
  </div>

  <!-- Chart -->
  <script>
    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: ['Saved', 'Applied', 'Offered', 'Rejected', 'Interview', 'Accepted'],
        datasets: [{
          data: [
            <?= (int)$overview['saved'] ?>,
            <?= (int)$overview['applied'] ?>,
            <?= (int)$overview['offered'] ?>,
            <?= (int)$overview['rejected'] ?>,
            <?= (int)$overview['interview'] ?>,
            <?= (int)$overview['accepted'] ?>
          ],
          backgroundColor: ['#FF5722', '#FFEB3B', '#4CAF50', '#F44336', '#03A9F4', '#8BC34A'],
          borderWidth: 1
        }]
      }
    });
  </script>
</body>
</html>
