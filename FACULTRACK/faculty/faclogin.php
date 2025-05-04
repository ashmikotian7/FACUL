<?php
session_start();

$login_error = "";
$signup_msg = "";
$signup_success = false;

if (file_exists('db.php')) {
    include 'db.php';
} else {
    die("Database connection file 'db.php' is missing.");
}

// Login logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'login') {
    $facultyID = $_POST['facultyID'];
    $passwordInput = $_POST['password'];

    $sql = "SELECT * FROM faculty WHERE facultyID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $facultyID);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($passwordInput, $row['password'])) {
            $_SESSION['facultyID'] = $facultyID;
            header("Location: dashboard.php");
            exit();
        } else {
            $login_error = "Invalid password.";
        }
    } else {
        $login_error = "Invalid Faculty ID.";
    }

    $stmt->close();
}

// Signup logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'signup') {
    $facultyID = $_POST['facultyID'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $birthdate = $_POST['birthdate'];
    $department = $_POST['department'];

    if (!str_ends_with($email, "@sode-edu.in")) {
        $signup_msg = "This platform is only for @sode-edu.in email addresses.";
    } else {
        $check_sql = "SELECT * FROM faculty WHERE facultyID = ? OR email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("ss", $facultyID, $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $signup_msg = "Faculty ID or Email already exists.";
        } else {
            $sql = "INSERT INTO faculty (facultyID, name, email, password, birthdate, department, grade, allowance, drive_link, total_score) 
                    VALUES (?, ?, ?, ?, ?, ?, '', 0, '', 0)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssss", $facultyID, $name, $email, $password, $birthdate, $department);

            if ($stmt->execute()) {
                $signup_msg = "Signup successful! You can now login.";
                $signup_success = true;
            } else {
                $signup_msg = "Error: " . $stmt->error;
            }

            $stmt->close();
        }

        $check_stmt->close();
    }
}

if (isset($conn)) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <link rel="icon" type="image/png" href="/images/white.png">
  <title>FaculTrack - Login / Signup</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
            heading: ['Poppins', 'sans-serif'],
            body: ['Open Sans', 'sans-serif'],
          },
        },
      },
    };
  </script>
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
  <style>
    .form-slider {
      transition: transform 0.5s ease-in-out;
      width: 200%;
      display: flex;
    }
    .form-section {
      width: 100%;
      padding: 2rem;
    }
    .tab-active {
      background-color: #264653 !important;
    }
  </style>
</head>

<body class="bg-gradient-to-br from-background via-primary to-background min-h-screen flex flex-col font-body text-text-main">
  <header class="bg-primary text-white px-5 py-4 flex justify-between items-center">
    <div class="flex items-center gap-2">
      <img src="/images/white.jpg" alt="FaculTrack Logo" class="w-12 h-12" />
      <h2 class="text-2xl font-heading font-bold">Faculty Panel</h2>
    </div>

    <div class="relative">
      <div id="hamburger" class="flex flex-col justify-between w-7 h-6 cursor-pointer" onclick="toggleMenu()">
        <div class="h-1 bg-white rounded"></div>
        <div class="h-1 bg-white rounded"></div>
        <div class="h-1 bg-white rounded"></div>
      </div>
      <div id="dropdown" class="absolute right-0 mt-2 bg-gray-800 text-white rounded-md shadow-lg hidden z-50 w-40">
        <a href="/index.html" class="block px-4 py-2 hover:bg-gray-700">Home</a>
        <a href="/faculty/faclogin.php" class="block px-4 py-2 hover:bg-gray-700">FacLogin</a>
        <a href="/admin/adminlog.php" class="block px-4 py-2 hover:bg-gray-700">AdminLogin</a>
        <a href="/aboutus.html" class="block px-4 py-2 hover:bg-gray-700">About Us</a>
        <a href="/contactus.html" class="block px-4 py-2 hover:bg-gray-700">Contact Us</a>
      </div>
    </div>
  </header>

  <main class="flex flex-col items-center justify-center flex-grow px-4 py-10">
    <div class="relative bg-white max-w-xl w-full rounded-xl shadow-2xl overflow-hidden">
      <div class="flex justify-center bg-accent text-white">
        <button id="loginTab" onclick="showForm('login')" class="w-1/2 py-3 font-semibold hover:bg-primary transition">Login</button>
        <button id="signupTab" onclick="showForm('signup')" class="w-1/2 py-3 font-semibold hover:bg-primary transition">Signup</button>
      </div>

      <div id="formSlider" class="form-slider">
        <!-- Login Form -->
        <div class="form-section">
          <h2 class="text-2xl font-heading text-center mb-4 text-primary">Faculty Login</h2>
          <?php if ($login_error): ?>
            <div class="text-red-600 text-center font-medium mb-4"><?= $login_error ?></div>
          <?php endif; ?>

          <form action="" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="login">
            <input type="text" name="facultyID" placeholder="Faculty ID" required class="w-full border p-2 rounded">
            <input type="password" name="password" placeholder="Password" required class="w-full border p-2 rounded">
            <button type="submit" class="w-full bg-accent text-white py-2 rounded hover:bg-primary">Login</button>
          </form>

          <div class="text-sm mt-4 text-center">
            <a href="/faculty/forget.php" class="text-primary hover:underline">Forgot Password?</a>
          </div>
        </div>

        <!-- Signup Form -->
        <div class="form-section">
          <h2 class="text-2xl font-heading text-center mb-4 text-primary">Faculty Signup</h2>
          <?php if ($signup_msg): ?>
            <div class="text-center font-medium mb-4 <?= str_contains($signup_msg, 'successful') ? 'text-green-600' : 'text-red-600' ?>">
              <?= $signup_msg ?>
            </div>
          <?php endif; ?>

          <form action="" method="POST" class="space-y-3">
            <input type="hidden" name="action" value="signup">
            <input type="text" name="facultyID" placeholder="Faculty ID" required class="w-full border p-2 rounded">
            <input type="text" name="name" placeholder="Full Name" required class="w-full border p-2 rounded">
            <input type="email" name="email" placeholder="Email (must be @sode-edu.in)" required class="w-full border p-2 rounded">
            <input type="password" name="password" placeholder="Password" required class="w-full border p-2 rounded">
            <input type="date" name="birthdate" required class="w-full border p-2 rounded">
            <select name="department" required class="w-full border p-2 rounded">
              <option value="" disabled selected>Select Department</option>
              <option value="Mathematics">Mathematics</option>
              <option value="Physics">Physics</option>
              <option value="Chemistry">Chemistry</option>
              <option value="Computer Science">Computer Science Engineering</option>
              <option value="Artificial Intelligence and Data Science">Artificial Intelligence  Data Science</option>
              <option value="Artificial Intelligence and Machine Learning">Artificial Intelligence & Machine Learning</option>
              <option value="Mechanical Engineering">Mechanical Engineering</option>
              <option value="Electronics and Communication">Electronics and Communication Engineering</option>
            </select>
            <button type="submit" class="w-full bg-accent text-white py-2 rounded hover:bg-primary">Signup</button>
          </form>
        </div>
      </div>
    </div>
  </main>

  <footer class="bg-primary text-white text-center py-4">
    <p>&copy; 2025 <span class="italic font-bold">FaculTrack</span>. All Rights Reserved.</p>
  </footer>

  <script>
    function showForm(type) {
      const slider = document.getElementById('formSlider');
      const loginTab = document.getElementById('loginTab');
      const signupTab = document.getElementById('signupTab');

      if (type === 'signup') {
        slider.style.transform = 'translateX(-50%)';
        signupTab.classList.add('tab-active');
        loginTab.classList.remove('tab-active');
      } else {
        slider.style.transform = 'translateX(0%)';
        loginTab.classList.add('tab-active');
        signupTab.classList.remove('tab-active');
      }
    }

    function toggleMenu() {
      const dropdown = document.getElementById("dropdown");
      dropdown.classList.toggle("hidden");
    }

    // Auto-switch to login only if signup was successful
    <?php if ($signup_success): ?>
      showForm('login');
    <?php else: ?>
      showForm('signup');
    <?php endif; ?>
  </script>
</body>
</html>
