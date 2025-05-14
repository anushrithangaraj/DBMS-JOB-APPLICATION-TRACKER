<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "job");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Fetch current user details
$userId = $_SESSION['user_id'];  // Assuming user is logged in and user_id is stored in session
$result = $mysqli->query("SELECT * FROM users WHERE id = '$userId'");
$user = $result->fetch_assoc();

// Update profile or password
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $name = $mysqli->real_escape_string($_POST['name']);
        $email = $mysqli->real_escape_string($_POST['email']);
        
        $mysqli->query("UPDATE users SET name = '$name', email = '$email' WHERE id = '$userId'");
        $message = "Profile updated successfully!";
    }

    if (isset($_POST['change_password'])) {
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        // Validate and update password
        if ($newPassword == $confirmPassword) {
            // Use password_verify to check the hashed password
            if (password_verify($currentPassword, $user['password'])) {
                // Hash the new password and update it
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $mysqli->query("UPDATE users SET password = '$newPasswordHash' WHERE id = '$userId'");
                $message = "Password changed successfully!";
            } else {
                $error = "Current password is incorrect.";
            }
        } else {
            $error = "New password and confirmation do not match.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Settings</title>
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
                <a href="upcoming_interviews.php">Upcoming Interviews</a>
                <a href="settings.php"class="font-semibold border-b-2 border-black text-dark">Settings</a>
            </nav>
        </div>
    </header>
<main>
    <!-- Main Content -->
    <div class="main-content max-w-6xl mx-auto p-6">
        <!-- Settings Content -->
        <div class="bg-white p-6 rounded-xl shadow-md space-y-8">
            <section>
                <h2 class="text-2xl font-bold text-dark mb-4">⚙️ Settings</h2>

                <!-- Success or Error Message -->
                <?php if (isset($message)): ?>
                    <div class="bg-green-100 text-green-800 p-4 rounded-lg mb-4">
                        <?php echo $message; ?>
                    </div>
                <?php elseif (isset($error)): ?>
                    <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-4">
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <!-- Profile Update Form -->
                <div>
                    <h3 class="text-xl font-semibold text-dark mb-2">👤 Update Profile</h3>
                    <form method="POST" class="space-y-4 bg-gray-50 p-4 rounded-lg">
                        <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required class="w-full p-2 border border-gray-300 rounded" placeholder="Your Name">
                        <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required class="w-full p-2 border border-gray-300 rounded" placeholder="Your Email">
                        <button type="submit" name="update_profile" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update Profile</button>
                    </form>
                </div>

                <!-- Password Change Form -->
                <div class="mt-8">
                    <h3 class="text-xl font-semibold text-dark mb-2">🔐 Change Password</h3>
                    <form method="POST" class="space-y-4 bg-gray-50 p-4 rounded-lg">
                        <input type="password" name="current_password" required class="w-full p-2 border border-gray-300 rounded" placeholder="Current Password">
                        <input type="password" name="new_password" required class="w-full p-2 border border-gray-300 rounded" placeholder="New Password">
                        <input type="password" name="confirm_password" required class="w-full p-2 border border-gray-300 rounded" placeholder="Confirm New Password">
                        <button type="submit" name="change_password" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Change Password</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</body>
</html>
