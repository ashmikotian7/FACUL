<?php
session_start();

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $email = trim($_POST['email']);
    $new_password = password_hash(trim($_POST['password']), PASSWORD_BCRYPT); // Encrypt the password

    if (empty($email) || empty($new_password)) {
        // Redirect back with an error if inputs are invalid
        header("Location: /admin/areset_password.php?status=invalid_input");
        exit;
    }

    // Prepare the SQL statement
    $sql = "UPDATE admin SET password = ? WHERE email = ?";
    $stmt = $conn->prepare($sql);

    // Check if $stmt is valid
    if (!$stmt) {
        error_log("Failed to prepare SQL statement: " . $conn->error); // Log the error
        header("Location: /admin/areset_password.php?status=stmt_error");
        exit;
    }

    $stmt->bind_param("ss", $new_password, $email);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect to reset password page with success message
        header("Location: /admin/areset_password.php?status=success");
        exit;
    } else {
        error_log("Error executing query: " . $stmt->error); // Log the error
        header("Location: /admin/areset_password.php?status=update_error");
        exit;
    }

    // Close resources safely
    if ($stmt) {
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset Password - FaculTrack</title>
  <link rel="icon" type="image/png" href="white.png" />
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
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@700&display=swap"
    rel="stylesheet"
  />
</head>
<body class="font-body bg-gradient-to-br from-background via-primary to-background text-text-main min-h-screen flex justify-center items-center">

  <div class="bg-white bg-opacity-90 rounded-lg shadow-lg p-8 w-full max-w-md text-center">
    <h2 class="text-2xl font-heading text-blue-600 mb-6">Reset Password</h2>

    <?php
    if (isset($_GET['status']) && $_GET['status'] === 'success') {
        echo '<p class="text-green-600 font-bold mb-4">Password has been reset successfully!</p>';
    } elseif (isset($_GET['status']) && $_GET['status'] === 'error') {
        echo '<p class="text-red-600 font-bold mb-4">There was an error resetting your password. Please try again.</p>';
    }
    ?>

    <form action="/admin/areset_password.php" method="POST">
      <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>" />

      <label for="password" class="block text-left font-bold text-gray-700 mb-2">New Password</label>
      <input
        type="password"
        id="password"
        name="password"
        placeholder="Enter new password"
        required
        class="w-full p-3 mb-4 border border-gray-300 rounded-md text-gray-800"
      />

      <button
        type="submit"
        class="w-full bg-orange-500 text-white py-3 rounded-md hover:bg-orange-600 transition-colors duration-300"
      >
        Reset Password
      </button>
    </form>

    <a href="/admin/adminlog.php" class="text-blue-600 mt-4 inline-block hover:underline">Back to Login</a>
  </div>

</body>
</html>
