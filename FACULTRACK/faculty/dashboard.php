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

// Fetch yearly growth data
$growthData = [];
$yearSql = "SELECT year, total_score FROM faculty WHERE facultyID = ? ORDER BY year ASC";
$yearStmt = $conn->prepare($yearSql);
$yearStmt->bind_param("s", $facultyID);
$yearStmt->execute();
$yearResult = $yearStmt->get_result();

while ($row = $yearResult->fetch_assoc()) {
    $growthData[] = $row;
}

$yearStmt->close();
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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Roboto', sans-serif;
    }
  </style>
</head>
<body style="background: linear-gradient(to bottom right, #F0F4F8, #264653, #F0F4F8);" class="min-h-screen flex items-center justify-center text-gray-800">
  <div class="max-w-4xl w-full p-6">
    <!-- Welcome Header -->
    <header class="bg-[#264653] border-4 border-white text-white text-center py-8 px-6 rounded-2xl shadow-lg relative">
      <i class="ph ph-user-circle text-5xl absolute top-4 left-4 text-white opacity-60"></i>
      <h1 class="text-3xl md:text-4xl font-bold">Welcome to Your Dashboard</h1>
      <p class="text-sm mt-2 opacity-80">Faculty Portal</p>
    </header>

    <!-- Info Box -->
    <section id="user-info" class="mt-10">
      <div class="border-4 border-[#2A9D8F] bg-white bg-opacity-70 backdrop-blur-sm rounded-xl p-6 shadow-xl hover:shadow-2xl transition duration-300">
        <p class="text-lg mb-4"><strong>👤 Username:</strong> <?= htmlspecialchars($name); ?></p>
        <p class="text-lg"><strong>📧 Email ID:</strong> <?= htmlspecialchars($email); ?></p>
      </div>
    </section>

    <!-- Growth Chart -->
    <section class="mt-10">
      <div class="bg-white bg-opacity-80 border-4 border-[#E9C46A] rounded-xl p-6 shadow-xl">
        <h2 class="text-xl font-bold text-center text-[#264653] mb-4">📈 Your Yearly Growth</h2>
        <canvas id="growthChart" height="100"></canvas>
      </div>
    </section>

    <!-- Buttons -->
    <section class="mt-8 flex flex-col md:flex-row items-center justify-around gap-4">
      <button onclick="window.location.href='/form/all.html';"
        class="border-4 border-[#2A9D8F] bg-[#2A9D8F] text-white text-lg px-6 py-3 rounded-lg shadow-md hover:shadow-xl hover:bg-[#21867b] transition duration-300 transform hover:scale-105">
        ✍️ Appraisal
      </button>

      <button onclick="window.location.href='/logout.php';"
        class="border-4 border-[#E76F51] bg-[#E76F51] text-white text-lg px-6 py-3 rounded-lg shadow-md hover:shadow-xl hover:bg-[#cc553e] transition duration-300 transform hover:scale-105">
        🚪 Logout
      </button>
    </section>
  </div>

  <script>
    const data = <?= json_encode($growthData); ?>;
    const years = data.map(entry => entry.year);
    const scores = data.map(entry => entry.total_score);

    const ctx = document.getElementById('growthChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: years,
        datasets: [{
          label: 'Total Score',
          data: scores,
          borderColor: '#2A9D8F',
          backgroundColor: 'rgba(42, 157, 143, 0.2)',
          borderWidth: 3,
          tension: 0.3,
          pointRadius: 5,
          pointBackgroundColor: '#264653',
          fill: true,
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Score',
              color: '#264653',
              font: { weight: 'bold' }
            }
          },
          x: {
            title: {
              display: true,
              text: 'Year',
              color: '#264653',
              font: { weight: 'bold' }
            }
          }
        }
      }
    });
  </script>
</body>
</html>
