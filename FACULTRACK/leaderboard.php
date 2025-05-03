<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Leaderboard | FaculTrack</title>
  <link rel="icon" type="image/png" href="/images/white.png" />
  <script src="https://cdn.tailwindcss.com"></script>
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
      <a href="/leaderboard.php" class="text-highlight">Leaderboard</a>
      <a href="/leadergraph.php" class="hover:text-highlight">Graph</a>
    </nav>
  </header>

  <!-- Leaderboard -->
  <section class="py-12 px-4">
    <h1 class="text-4xl font-heading font-bold text-center text-primary mb-10">📅 Year-wise Top 20 Faculty Leaderboard</h1>

    <?php
      $conn = new mysqli("localhost", "root", "", "facultrack");
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      // Get distinct years
      $yearQuery = "SELECT DISTINCT year FROM faculty ORDER BY year DESC";
      $yearResult = $conn->query($yearQuery);

      $years = [];
      if ($yearResult->num_rows > 0) {
        while ($yearRow = $yearResult->fetch_assoc()) {
          $years[] = $yearRow['year'];
        }
      }

      // Render dropdown
      if (count($years) > 0) {
        echo "<div class='max-w-xs mx-auto mb-10 text-center'>
                <label for='yearSelect' class='block mb-2 font-semibold text-lg'>🔽 Select Year</label>
                <select id='yearSelect' onchange='filterYear()' class='w-full px-4 py-2 rounded-lg border border-primary text-primary bg-white font-medium shadow-sm'>
                  <option value='all'>Show All</option>";
        foreach ($years as $yr) {
          echo "<option value='year-$yr'>$yr</option>";
        }
        echo "</select>
              </div>";
      }

      // Render leaderboards
      foreach ($years as $year) {
        echo "<div id='year-$year' class='year-block mb-8 bg-white rounded-xl shadow-lg p-6 border-t-4 border-accent'>
                <h2 class='text-2xl font-heading font-semibold text-primary mb-4 text-center'>Year: $year</h2>";

        $sql = "SELECT name, department, total_score 
                FROM faculty 
                WHERE year = $year 
                ORDER BY total_score DESC 
                LIMIT 20";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          echo "<div class='overflow-x-auto'>
                  <table class='w-full max-w-4xl mx-auto bg-white text-left border border-gray-200 rounded-lg'>
                    <thead class='bg-primary text-white'>
                      <tr>
                        <th class='px-6 py-3'>#</th>
                        <th class='px-6 py-3'>Name</th>
                        <th class='px-6 py-3'>Department</th>
                        <th class='px-6 py-3'>Total Score</th>
                      </tr>
                    </thead>
                    <tbody class='text-text-main font-medium'>";
          $rank = 1;
          while ($row = $result->fetch_assoc()) {
            echo "<tr class='hover:bg-gray-100 border-t border-gray-100'>
                    <td class='px-6 py-4'>" . $rank++ . "</td>
                    <td class='px-6 py-4'>" . htmlspecialchars($row["name"]) . "</td>
                    <td class='px-6 py-4'>" . htmlspecialchars($row["department"]) . "</td>
                    <td class='px-6 py-4 text-accent font-bold'>" . htmlspecialchars($row["total_score"]) . "</td>
                  </tr>";
          }
          echo "</tbody></table></div>";
        } else {
          echo "<p class='text-center text-gray-500'>No data found for $year.</p>";
        }

        echo "</div>"; // Close year block
      }

      $conn->close();
    ?>
  </section>

  <!-- Footer -->
  <footer class="bg-primary text-white text-center py-3 mt-10 rounded-t-xl shadow-inner">
    <p class="font-semibold">&copy; 2024 FaculTrack - All Rights Reserved.</p>
  </footer>

  <!-- Dropdown Filter Script -->
  <script>
    function filterYear() {
      const selected = document.getElementById('yearSelect').value;
      const yearBlocks = document.querySelectorAll('.year-block');
      yearBlocks.forEach(block => {
        block.style.display = (selected === 'all' || block.id === selected) ? 'block' : 'none';
      });
    }
  </script>

</body>
</html>
