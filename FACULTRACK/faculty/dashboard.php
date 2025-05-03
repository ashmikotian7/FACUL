<?php
session_start();

include 'db.php';

if (!isset($_SESSION['facultyID'])) {
    header("Location: /faculty/faclogin.php");
    exit();
}

$facultyID = $_SESSION['facultyID'];
$sql = "SELECT name, email FROM faculty WHERE facultyID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $facultyID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $name = $row['name'];
    $email = $row['email'];
} else {
    session_destroy();
    header("Location: /faculty/faclogin.php");
    exit();
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="/images/white.png">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <style>
    body {
      font-family: 'Roboto', sans-serif;
    }
  </style>
</head>
<body style="background: linear-gradient(to bottom right, #F0F4F8, #264653, #F0F4F8);" class="min-h-screen flex items-center justify-center text-gray-800">
  <div class="max-w-3xl w-full p-6">
    <!-- Welcome Header -->
    <header class="bg-[#264653] border-4 border-white text-white text-center py-8 px-6 rounded-2xl shadow-lg relative">
      <i class="ph ph-user-circle text-5xl absolute top-4 left-4 text-white opacity-60"></i>
      <h1 class="text-3xl md:text-4xl font-bold">Welcome to Your Dashboard</h1>
      <p class="text-sm mt-2 opacity-80">Faculty Portal</p>
    </header>

    <!-- Info Box -->
    <section id="user-info" class="mt-10">
      <div class="border-4 border-[#2A9D8F] bg-white bg-opacity-70 backdrop-blur-sm rounded-xl p-6 shadow-xl hover:shadow-2xl transition duration-300">
        <p class="text-lg mb-4"><strong>👤 Username:</strong> <?php echo htmlspecialchars($name); ?></p>
        <p class="text-lg"><strong>📧 Email ID:</strong> <?php echo htmlspecialchars($email); ?></p>
      </div>
    </section>

    <!-- Buttons -->
    <section class="mt-8 flex flex-col md:flex-row items-center justify-around gap-4">
      <!-- Appraisal -->
      <button 
        onclick="window.location.href='/form/all.html';" 
        class="border-4 border-[#2A9D8F] bg-[#2A9D8F] text-white text-lg px-6 py-3 rounded-lg shadow-md hover:shadow-xl hover:bg-[#21867b] transition duration-300 transform hover:scale-105">
        ✍️ Appraisal
      </button>

      <!-- Logout -->
      <button 
        onclick="window.location.href='/logout.php';" 
        class="border-4 border-[#E76F51] bg-[#E76F51] text-white text-lg px-6 py-3 rounded-lg shadow-md hover:shadow-xl hover:bg-[#cc553e] transition duration-300 transform hover:scale-105">
        🚪 Logout
      </button>
    </section>
  </div>
</body>
</html>
