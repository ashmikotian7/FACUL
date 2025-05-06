<?php
session_start();
include 'db.php';  // Ensure this file exists in the same directory

$login_error = "";
$signup_msg = "";

// Admin Login logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'admin_login') {
    $adminID = $_POST['adminID'];
    $passwordInput = $_POST['password'];

    $sql = "SELECT * FROM admin WHERE adminID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $adminID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($passwordInput, $row['password'])) {
            $_SESSION['adminID'] = $adminID;
            header("Location: /admin/admin.php");
            exit();
        } else {
            $login_error = "Invalid password.";
        }
    } else {
        $login_error = "Invalid Admin ID.";
    }

    $stmt->close();
}

// Admin Signup logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'admin_signup') {
    $adminID = $_POST['adminID'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    if (!str_ends_with($email, "@sode-edu.in")) {
        $signup_msg = "This platform is only for @sode-edu.in email addresses.";
    } else {
        $sql = "INSERT INTO admin (adminID, name, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $adminID, $name, $email, $password);

        if ($stmt->execute()) {
            $signup_msg = "Signup successful! You can now login.";
        } else {
            $signup_msg = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Login / Signup</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="icon" type="image/png" href="/images/white.png">
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
  <!-- Header with Hamburger Menu -->
  <header class="bg-primary text-white px-5 py-4 flex justify-between items-center">
    <div class="flex items-center gap-2">
      <img src="/images/white.jpg" alt="Admin Logo" class="w-12 h-12" />
      <h2 class="text-2xl font-heading font-bold">Admin Panel</h2>
    </div>

    <!-- Hamburger -->
    <div class="relative">
      <div id="hamburger" class="flex flex-col justify-between w-7 h-6 cursor-pointer" onclick="toggleMenu()">
        <div class="h-1 bg-white rounded"></div>
        <div class="h-1 bg-white rounded"></div>
        <div class="h-1 bg-white rounded"></div>
      </div>

      <!-- Dropdown Menu -->
      <div id="dropdown" class="absolute right-0 mt-2 bg-gray-800 text-white rounded-md shadow-lg hidden z-50 w-40">
        <a href="/index.html" class="block px-4 py-2 hover:bg-gray-700">Home</a>
        <a href="/faculty/faclogin.php" class="block px-4 py-2 hover:bg-gray-700">Faculty Panel</a>
        <a href="/admin/adminlog.php" class="block px-4 py-2 hover:bg-gray-700">Admin Panel</a>
        <a href="/aboutus.html" class="block px-4 py-2 hover:bg-gray-700">About Us</a>
        <a href="/contactus.html" class="block px-4 py-2 hover:bg-gray-700">Contact Us</a>
      </div>
    </div>
  </header>

  <!-- Login/Signup Box -->
  <main class="flex flex-col items-center justify-center flex-grow px-4 py-10">
    <div class="relative bg-white max-w-xl w-full rounded-xl shadow-2xl overflow-hidden">
      <!-- Tabs -->
      <div class="flex justify-center bg-accent text-white">
        <button id="loginTab" onclick="showForm('admin_login')" class="w-1/2 py-3 font-semibold hover:bg-primary transition">Login</button>
        <button id="signupTab" onclick="showForm('admin_signup')" class="w-1/2 py-3 font-semibold hover:bg-primary transition">Signup</button>
      </div>

      <!-- Form Slides -->
      <div id="formSlider" class="form-slider">
        <!-- Admin Login Form -->
        <div class="form-section">
          <h2 class="text-2xl font-heading text-center mb-4 text-primary">Admin Login</h2>
          <?php if ($login_error): ?>
            <div class="text-red-600 text-center font-medium mb-4"><?= $login_error ?></div>
          <?php endif; ?>

          <form action="" method="POST" class="space-y-4">
            <input type="hidden" name="action" value="admin_login">
            <input type="text" name="adminID" placeholder="Admin ID" required class="w-full border p-2 rounded">
            <input type="password" name="password" placeholder="Password" required class="w-full border p-2 rounded">
            <button type="submit" class="w-full bg-accent text-white py-2 rounded hover:bg-primary">Login</button>
          </form>

          <div class="text-sm mt-4 text-center">
            <a href="/admin/aforgot-password.php" class="text-primary hover:underline">Forgot Password?</a>
          </div>

          <div class="mt-6 bg-gray-100 p-4 rounded-lg text-sm shadow-inner">
            <h3 class="text-lg font-semibold text-primary mb-2">Login Instructions:</h3>
            <ul class="list-disc list-inside space-y-1">
              <li>Enter your Admin ID and password correctly.</li>
              <li>Click "Signup" tab if you're a new admin.</li>
            </ul>
          </div>
        </div>

        <!-- Admin Signup Form -->
        <div class="form-section">
          <h2 class="text-2xl font-heading text-center mb-4 text-primary">Admin Signup</h2>
          <?php if ($signup_msg): ?>
            <div class="text-center font-medium mb-4 <?= str_contains($signup_msg, 'successful') ? 'text-green-600' : 'text-red-600' ?>">
              <?= $signup_msg ?>
            </div>
          <?php endif; ?>

          <form action="" method="POST" class="space-y-3">
            <input type="hidden" name="action" value="admin_signup">
            <input type="text" name="adminID" placeholder="Admin ID" required class="w-full border p-2 rounded">
            <input type="text" name="name" placeholder="Full Name" required class="w-full border p-2 rounded">
            <input type="email" name="email" placeholder="Email (must be @sode-edu.in)" required class="w-full border p-2 rounded">
            <input type="password" name="password" placeholder="Password" required class="w-full border p-2 rounded">
            <button type="submit" class="w-full bg-accent text-white py-2 rounded hover:bg-primary">Signup</button>
          </form>

          <div class="mt-6 bg-gray-100 p-4 rounded-lg text-sm shadow-inner">
            <h3 class="text-lg font-semibold text-primary mb-2">Signup Instructions:</h3>
            <ul class="list-disc list-inside space-y-1">
              <li>All fields are mandatory to complete registration.</li>
              <li>Email must be a valid <strong>@sode-edu.in</strong> address.</li>
              <li>Password must be strong and memorable.</li>
              <li>After signing up, use your Admin ID to log in.</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-primary text-white text-center py-4">
    <p>&copy; 2025 <span class="italic font-bold">FaculTrack</span>. All Rights Reserved.</p>
  </footer>

  <!-- Scripts -->
  <script>
    function showForm(type) {
      const slider = document.getElementById('formSlider');
      const loginTab = document.getElementById('loginTab');
      const signupTab = document.getElementById('signupTab');

      if (type === 'admin_signup') {
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

    // Default tab
    showForm('admin_login');
  </script>
</body>
</html>
