<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="icon" type="image/png" href="/images/white.png">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/@phosphor-icons/web"></script>
  <style>
    body {
      font-family: 'Roboto', sans-serif;
    }
    summary {
      cursor: pointer;
    }
  </style>
</head>
<body style="background: linear-gradient(to bottom right, #F0F4F8, #264653, #F0F4F8);" class="min-h-screen text-gray-800">
  <div class="max-w-7xl mx-auto p-4">
    
    <!-- Dashboard Header -->
    <header class="bg-[#264653] border-4 border-white text-white py-6 px-8 rounded-2xl shadow-lg flex justify-between items-center">
      <h1 class="text-2xl md:text-3xl font-bold">Admin Dashboard</h1>
      <button onclick="window.location.href='/index.html'" class="bg-white text-black font-semibold px-4 py-2 rounded-md hover:bg-gray-100 transition">
        🚪 Logout
      </button>
    </header>

    <!-- Yearly Faculty Listing -->
    <div class="mt-6 bg-white bg-opacity-80 backdrop-blur-sm border-4 border-[#2A9D8F] rounded-xl shadow-lg p-6">
      <h2 class="text-2xl font-bold mb-4 text-center">📅 Faculty List by Year</h2>

      <?php
      include 'db.php';

      $sql = "SELECT year, id, facultyID, name, grade, allowance, total_score, department, drive_link 
              FROM faculty 
              ORDER BY year DESC, name ASC";
      $result = $conn->query($sql);

      $facultyByYear = [];

      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $facultyByYear[$row['year']][] = $row;
          }

          foreach ($facultyByYear as $year => $facultyList) {
              echo "<details class='mb-4 border border-gray-300 rounded-md p-4'>
                      <summary class='text-xl font-semibold text-[#264653]'>Year: $year</summary>
                      <div class='mt-4 overflow-x-auto'>
                        <table class='min-w-full text-sm text-center border'>
                          <thead class='bg-gray-100 text-gray-700'>
                            <tr>
                              <th class='px-4 py-2'>ID</th>
                              <th class='px-4 py-2'>Faculty ID</th>
                              <th class='px-4 py-2'>Name</th>
                              <th class='px-4 py-2'>Grade</th>
                              <th class='px-4 py-2'>Allowance</th>
                              <th class='px-4 py-2'>Total Score</th>
                              <th class='px-4 py-2'>Department</th>
                              <th class='px-4 py-2'>Drive Link</th>
                            </tr>
                          </thead>
                          <tbody class='bg-white'>";
              foreach ($facultyList as $row) {
                  echo "<tr class='hover:bg-gray-100 transition'>
                          <td class='px-4 py-2'>" . $row["id"] . "</td>
                          <td class='px-4 py-2'>" . $row["facultyID"] . "</td>
                          <td class='px-4 py-2'>" . $row["name"] . "</td>
                          <td class='px-4 py-2'>" . $row["grade"] . "</td>
                          <td class='px-4 py-2'>₹" . number_format($row["allowance"], 2) . "</td>
                          <td class='px-4 py-2'>" . $row["total_score"] . "</td>
                          <td class='px-4 py-2'>" . $row["department"] . "</td>
                          <td class='px-4 py-2'><a href='" . htmlspecialchars($row["drive_link"]) . "' class='text-blue-600 underline' target='_blank'>Link</a></td>
                        </tr>";
              }
              echo "    </tbody>
                        </table>
                      </div>
                    </details>";
          }
      } else {
          echo "<p class='text-center text-gray-500'>No faculty data found.</p>";
      }

      $conn->close();
      ?>
    </div>
  </div>
</body>
</html>
