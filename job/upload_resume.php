<?php
// upload_resume.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mysqli = new mysqli("localhost", "root", "", "job");

    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $candidate_name = $_POST['candidate_name'];
    $position = $_POST['position'];

    $upload_dir = 'uploads/resumes/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_path = basename($_FILES["resume"]["name"]);
    $target_path = $upload_dir . $file_path;

    if (move_uploaded_file($_FILES["resume"]["tmp_name"], $target_path)) {
        $stmt = $mysqli->prepare("INSERT INTO resumes (candidate_name, position, file_path, uploaded_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $candidate_name, $position, $file_path);
        $stmt->execute();
        $stmt->close();
        header("Location: resume_bank.php");
        exit();
    } else {
        echo "❌ Failed to upload resume.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Resume</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="min-h-screen flex items-center justify-center" 
      style="background: linear-gradient(135deg, #FFE3D3, rgb(239, 237, 126), rgb(191, 203, 121));">

  <div class="w-full max-w-xl bg-white p-8 shadow-lg rounded-2xl border border-gray-200">
    <h2 class="text-3xl font-semibold text-center text-green-700 mb-6">📤 Upload Resume</h2>

    <form method="POST" enctype="multipart/form-data">
      <div class="mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1">👤 Candidate Name</label>
        <input type="text" name="candidate_name" required 
               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
      </div>

      <div class="mb-5">
        <label class="block text-sm font-medium text-gray-700 mb-1">💼 Position</label>
        <input type="text" name="position" required 
               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
      </div>

      <div class="mb-6">
        <label class="block text-sm font-medium text-gray-700 mb-1">📎 Resume File</label>
        <input type="file" name="resume" accept=".pdf,.doc,.docx" required 
               class="w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0
                      file:text-sm file:font-semibold
                      file:bg-green-100 file:text-green-700
                      hover:file:bg-green-200">
      </div>

      <div class="flex items-center justify-between">
        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-medium px-5 py-2 rounded-lg shadow">
          Upload
        </button>
        <a href="resume_bank.php" class="text-sm text-gray-600 hover:underline">Cancel</a>
      </div>
    </form>
  </div>

</body>
</html>
