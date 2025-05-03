<?php
session_start();

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    $sql = "SELECT * FROM faculty WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: /faculty/validation.php?email=" . urlencode($email));
        exit;
    } else {
        $error = "Email not found. Please try again.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Forgot Password - FaculTrack</title>
  <link rel="icon" type="image/png" href="/images/white.png" />
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
      background: linear-gradient(to bottom right, #F0F4F8, #264653, #F0F4F8);
      color: #fff;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .container {
      background-color: rgba(255, 255, 255, 0.95);
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      width: 100%;
      max-width: 400px;
      color: #333;
      text-align: center;
    }

    .container h2 {
      margin-bottom: 20px;
      font-family: 'Poppins', sans-serif;
      font-size: 26px;
      color: #264653;
    }

    .container label {
      font-weight: 600;
    }

    .container input[type="email"] {
      width: 100%;
      padding: 10px;
      margin: 10px 0 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }

    .container button {
      width: 100%;
      background-color: #2A9D8F;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
      font-size: 14px;
      transition: background-color 0.3s ease;
    }

    .container button:hover {
      background-color: #21867a;
    }

    .error {
      color: red;
      margin-top: 10px;
    }

    a {
      display: block;
      margin-top: 15px;
      color: #2A9D8F;
      text-decoration: none;
      font-weight: 600;
    }

    a:hover {
      text-decoration: underline;
    }
  </style>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="container">
    <h2>Forgot Password?</h2>
    <form method="POST" action="forget.php">
      <label for="email">Enter your registered Email</label>
      <input type="email" name="email" id="email" required />
      <button type="submit">Verify Email</button>
    </form>
    <?php if (!empty($error)) echo '<div class="error">' . htmlspecialchars($error) . '</div>'; ?>
    <a href="/faculty/faclogin.php">Back to Login</a>
  </div>
</body>
</html>
