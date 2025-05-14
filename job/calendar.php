<?php
// DB connection
$conn = new mysqli("localhost", "root", "", "job");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Default month/year
$month = isset($_GET['month']) ? (int)$_GET['month'] : date('n');
$year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');

// Fetch schedules
$stmt = $conn->prepare("SELECT * FROM schedules WHERE MONTH(schedule_date) = ? AND YEAR(schedule_date) = ?");
$stmt->bind_param("ii", $month, $year);
$stmt->execute();
$result = $stmt->get_result();

$schedules = [];
while ($row = $result->fetch_assoc()) {
    $schedules[$row['schedule_date']][] = $row;
}

function getMonthName($num) {
    return date("F", mktime(0, 0, 0, $num, 10));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= getMonthName($month) . " $year Calendar" ?></title>
    <style>

        .calendar {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 1px;
            background: #ccc;
            max-width: 1000px;
            margin: auto;
        }

        .day, .header {
            background: white;
            min-height: 120px;
            padding: 8px;
            position: relative;
            overflow: auto;
        }

        .header {
            background:rgb(234, 115, 78);
            color: white;
            font-weight: bold;
            text-align: center;
        }

        .date-number {
            font-weight: bold;
            color: #444;
        }

        .event {
            background: #ffe082;
            color:rgb(126, 63, 26);
            padding: 4px 6px;
            border-radius: 5px;
            margin-top: 4px;
            font-size: 0.85em;
        }

        .event form {
            display: inline;
        }

        .event button {
            background: #dc3545;
            color: white;
            border: none;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 0.75em;
            cursor: pointer;
            margin-top: 4px;
        }

        .day:hover {
            background:rgb(172, 164, 89);
            cursor: pointer;
        }

        .controls select {
            padding: 5px 10px;
            margin: 0 5px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .calendar {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
</head>
<body class="min-h-screen p-4 font-sans">
  <div class="max-w-7xl mx-auto">
  <head>
  <meta charset="UTF-8">
  <title>Job Application Tracker</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                <a href="calendar.php"class="font-semibold border-b-2 border-black text-dark">Calendar</a>
                <a href="resume_bank.php">Resume Bank</a>
                <a href="analytics.php">Analytics</a>
                <a href="interview_prep.php" >Interview Prep</a>
                <a href="upcoming_interviews.php">Upcoming Interviews</a>
                <a href="settings.php">Settings</a>
            </nav>
        </div>
    </header>

    <main>

    <h2>📅 <?= getMonthName($month) . " $year" ?> Calendar</h2>

    <div class="controls">
        <form method="get">
            <select name="month" onchange="this.form.submit()">
                <?php for ($m = 1; $m <= 12; $m++): ?>
                    <option value="<?= $m ?>" <?= ($m == $month) ? 'selected' : '' ?>><?= getMonthName($m) ?></option>
                <?php endfor; ?>
            </select>

            <select name="year" onchange="this.form.submit()">
                <?php for ($y = 2023; $y <= 2030; $y++): ?>
                    <option value="<?= $y ?>" <?= ($y == $year) ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
        </form>
    </div>

    <div class="calendar">
        <?php
        // Weekday headers
        $weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        foreach ($weekdays as $day) {
            echo "<div class='header'>$day</div>";
        }

        $startDay = date('w', strtotime("$year-$month-01"));
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($i = 0; $i < $startDay; $i++) echo "<div class='day'></div>";

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($day, 2, '0', STR_PAD_LEFT);
            echo "<div class='day' onclick=\"openForm('$date')\">";
            echo "<div class='date-number'>$day</div>";

            if (isset($schedules[$date])) {
                foreach ($schedules[$date] as $event) {
                    echo "<div class='event'>
                            <strong>{$event['title']}</strong><br>{$event['description']}
                            <form method='POST' action='delete_schedule.php' onsubmit=\"return confirm('Delete this schedule?')\">
                                <input type='hidden' name='id' value='{$event['id']}'>
                                <input type='hidden' name='month' value='$month'>
                                <input type='hidden' name='year' value='$year'>
                                <button type='submit'>🗑️ Delete</button>
                            </form>
                          </div>";
                }
            }

            echo "</div>";
        }
        ?>
    </div>

    <!-- Modal Form -->
    <div id="formModal" style="display:none; position:fixed; top:20%; left:35%; background:white; border:1px solid #ccc; padding:20px; z-index:1000;">
        <h3>📌 Add Schedule for <span id="selectedDate"></span></h3>
        <form method="POST" action="save_schedule.php">
            <input type="hidden" name="schedule_date" id="scheduleDateInput">
            <input type="text" name="title" placeholder="Title" required><br><br>
            <textarea name="description" placeholder="Description" rows="3" required></textarea><br><br>
            <button type="submit">Save</button>
            <button type="button" onclick="closeForm()">Cancel</button>
        </form>
    </div>

    <script>
        function openForm(date) {
            document.getElementById("selectedDate").innerText = date;
            document.getElementById("scheduleDateInput").value = date;
            document.getElementById("formModal").style.display = "block";
        }

        function closeForm() {
            document.getElementById("formModal").style.display = "none";
        }
    </script>
  </div>
</body>
</html>
