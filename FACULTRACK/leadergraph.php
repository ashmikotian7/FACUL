<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Leaderboard Graph | FaculTrack</title>
  <link rel="icon" type="image/png" href="/images/white.png" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#264653',
            accent: '#2A9D8F',
            background: '#F0F4F8',
            'text-main': '#1F2937',
            highlight: '#E9C46A',
          },
          fontFamily: {
            heading: ['"Poppins"', 'sans-serif'],
            body: ['"Open Sans"', 'sans-serif'],
          }
        }
      }
    }
  </script>
</head>
<body class="font-body bg-gradient-to-br from-background via-primary to-background text-text-main m-0 p-0">

  <!-- Header -->
  <header class="flex items-center bg-primary text-white px-5 py-3">
    <div class="flex items-center gap-2">
      <img src="/images/white.png" alt="SMVITM LOGO" class="w-16" />
      <h2 class="text-2xl font-semibold font-heading">FaculTrack</h2>
    </div>
    <nav class="ml-auto flex gap-6 items-center font-bold">
      <a href="/index.html" class="hover:text-highlight">Home</a>
      <a href="/leaderboard.php" class="hover:text-highlight">Leaderboard</a>
      <a href="/leadergraph.php" class="text-highlight">Graph</a>
    </nav>
  </header>

  <!-- Graph Section -->
  <section class="text-center py-12 px-4">
    <h1 class="text-4xl font-heading font-bold text-primary mb-6">Top 20 Faculty Performance</h1>

    <!-- Year Dropdown -->
    <form method="GET" class="mb-6">
      <label for="year" class="block text-lg font-semibold text-primary mb-2">Select Year:</label>
      <select name="year" id="year" onchange="this.form.submit()" class="mx-auto px-4 py-2 rounded-lg border border-accent focus:outline-none focus:ring-2 focus:ring-accent">
        <?php
          $conn = new mysqli("localhost", "root", "", "facultrack");
          if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

          $yearQuery = "SELECT DISTINCT year FROM faculty WHERE year != 0 ORDER BY year DESC";
          $yearResult = $conn->query($yearQuery);
          $selectedYear = isset($_GET['year']) ? intval($_GET['year']) : date("Y");

          while ($row = $yearResult->fetch_assoc()) {
            $year = $row['year'];
            $selected = ($year == $selectedYear) ? "selected" : "";
            echo "<option value='$year' $selected>$year</option>";
          }
        ?>
      </select>
    </form>

    <!-- Chart Container -->
    <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-lg p-6 overflow-x-auto">
      <canvas id="scoreChart"></canvas>
    </div>

    <!-- Legend Table -->
    <div class="mt-10 text-left">
      <h2 class="text-2xl font-heading font-semibold text-primary mb-4">Grading & Allowance Scheme</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto border border-gray-300 text-sm text-left">
          <thead class="bg-primary text-white">
            <tr>
              <th class="px-4 py-2">Grade</th>
              <th class="px-4 py-2">Grade Points Band</th>
              <th class="px-4 py-2">Performance Allowance</th>
              <th class="px-4 py-2">Color Used</th>
            </tr>
          </thead>
          <tbody class="bg-white text-text-main">
            <tr class="border-t">
              <td class="px-4 py-2 font-bold">A+++</td>
              <td class="px-4 py-2">&gt;= 50% (200 - 400)</td>
              <td class="px-4 py-2">Rs. 5,000</td>
              <td class="px-4 py-2"><span class="inline-block w-4 h-4 rounded-full" style="background-color: #2ecc71;"></span> Green</td>
            </tr>
            <tr class="border-t">
              <td class="px-4 py-2 font-bold">A++</td>
              <td class="px-4 py-2">40 - 49.75% (160 - 199)</td>
              <td class="px-4 py-2">Rs. 3,500</td>
              <td class="px-4 py-2"><span class="inline-block w-4 h-4 rounded-full" style="background-color: #3498db;"></span> Blue</td>
            </tr>
            <tr class="border-t">
              <td class="px-4 py-2 font-bold">A+</td>
              <td class="px-4 py-2">30 - 39.75% (120 - 159)</td>
              <td class="px-4 py-2">Rs. 2,000</td>
              <td class="px-4 py-2"><span class="inline-block w-4 h-4 rounded-full" style="background-color: #f39c12;"></span> Orange</td>
            </tr>
            <tr class="border-t">
              <td class="px-4 py-2 font-bold">A</td>
              <td class="px-4 py-2">&lt; 30% (&lt; 120)</td>
              <td class="px-4 py-2">No Allowance</td>
              <td class="px-4 py-2"><span class="inline-block w-4 h-4 rounded-full" style="background-color: #e74c3c;"></span> Red</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </section>

  <!-- PHP: Score Data Fetch -->
  <?php
    $scoreQuery = "SELECT name, total_score FROM faculty WHERE year = $selectedYear ORDER BY total_score DESC LIMIT 20";
    $scoreResult = $conn->query($scoreQuery);
    $names = [];
    $scores = [];

    while ($row = $scoreResult->fetch_assoc()) {
      $names[] = $row["name"];
      $scores[] = $row["total_score"];
    }
    $conn->close();
  ?>

  <!-- Chart Script (Line Graph) -->
  <script>
    const labels = <?php echo json_encode($names); ?>;
    const scores = <?php echo json_encode($scores); ?>;

    const ctx = document.getElementById('scoreChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Total Score (<?php echo $selectedYear; ?>)',
          data: scores,
          borderColor: '#2A9D8F',
          backgroundColor: '#2A9D8F',
          fill: false,
          tension: 0.3,
          pointRadius: 6,
          pointHoverRadius: 7,
          pointBackgroundColor: scores.map(score => {
            if (score >= 200) return '#2ecc71';
            else if (score >= 160) return '#3498db';
            else if (score >= 120) return '#f39c12';
            else return '#e74c3c';
          }),
          pointBorderColor: '#264653'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: true,
            labels: {
              color: '#264653',
              font: { size: 14 }
            }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return `Score: ${context.parsed.y}`;
              }
            }
          }
        },
        scales: {
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Total Score',
              font: { size: 14 }
            }
          },
          x: {
            title: {
              display: true,
              text: 'Faculty Name',
              font: { size: 14 }
            }
          }
        }
      }
    });
  </script>

  <!-- Footer -->
  <footer class="bg-primary text-white text-center py-3 mt-10 rounded-t-xl shadow-inner">
    <p class="font-semibold">&copy; 2025 FaculTrack - All Rights Reserved.</p>
  </footer>

</body>
</html>
