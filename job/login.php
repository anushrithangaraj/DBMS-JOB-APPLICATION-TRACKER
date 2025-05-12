<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "job");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$message = '';  // Initialize message variable

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    // Query to find the user by email
    $query = "SELECT id, name, password FROM users WHERE email = '$email'";
    $result = $mysqli->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];

            // Redirect to the home page or dashboard
            header("Location: index.php");
            exit;
        } else {
            $message = "Incorrect password. Please try again.";
        }
    } else {
        $message = "No user found with this email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #E0ECFF, #FFF2F2);
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .text-dark { color: #2C2C2C; }
    </style>
</head>
<body>
<div class="max-w-6xl mx-auto">
      <!-- Heading -->
      <h1 class="text-4xl font-bold text-dark text-center my-8">Job Application Tracker</h1>
    <!-- Login Content -->
    <div class="bg-white p-6 rounded-xl shadow-md space-y-8">
        <section>
            <h2 class="text-2xl font-bold text-dark mb-4">🔐 Login</h2>

            <!-- Message Display -->
            <?php if ($message): ?>
                <div class="bg-red-100 text-red-800 p-4 rounded-lg mb-4">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <!-- Login Form -->
            <form method="POST" class="space-y-4 bg-gray-50 p-4 rounded-lg">
                <input type="email" name="email" required class="w-full p-2 border border-gray-300 rounded" placeholder="Your Email">
                <input type="password" name="password" required class="w-full p-2 border border-gray-300 rounded" placeholder="Password">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Login</button>
            </form>

            <!-- Redirect to sign-up page -->
            <p class="mt-4 text-gray-700 text-center">Don't have an account? <a href="signup.php" class="text-blue-600 hover:underline">Sign up here</a></p>
        </section>
    </div>
</div>
</body>
</html>
