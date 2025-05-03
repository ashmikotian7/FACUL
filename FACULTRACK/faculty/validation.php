<?php
session_start();

include 'db.php';

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "facultrack";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize input
    $email = trim($_POST['email']); // Taken from hidden field
    $birthdate = trim($_POST['birthdate']);

    if (!empty($email) && !empty($birthdate)) {
        // Match email and birthdate in faculty table
        $sql = "SELECT * FROM faculty WHERE email = ? AND birthdate = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $birthdate);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // Match found, redirect to resetpass page with email
            header("Location: /faculty/resetpass.php?email=" . urlencode($email));
            exit;
        } else {
            $error = "Validation failed. Birthdate doesn't match.";
        }

        $stmt->close();
    } else {
        $error = "Please fill all the fields.";
    }

    $conn->close();
}

// Get email from URL (to show in the hidden field)
$emailFromURL = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Validate Identity</title>
  <link rel="icon" type="image/png" href="/images/white.png" />
  <style>
    body {
      font-family: 'Open Sans', sans-serif;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      background: linear-gradient(to bottom right, #F0F4F8, #264653, #F0F4F8);
      color: #fff;
    }

    .validate-container {
      background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(230, 230, 250, 0.95));
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
      padding: 40px 30px;
      width: 100%;
      max-width: 400px;
      text-align: center;
    }

    .validate-container h2 {
      font-size: 2em;
      color: #264653;
      margin-bottom: 20px;
      font-family: 'Poppins', sans-serif;
    }

    .validate-container label {
      display: block;
      margin-bottom: 8px;
      color: #333;
      font-weight: bold;
    }

    .validate-container input[type="date"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 10px;
      border: 2px solid #ccc;
      border-radius: 10px;
      font-size: 1em;
      box-sizing: border-box;
      transition: border 0.3s, box-shadow 0.3s;
    }

    .validate-container input[type="date"]:focus {
      border-color: #2A9D8F;
      box-shadow: 0 0 8px rgba(42, 157, 143, 0.4);
    }

    .validate-container button {
      background: linear-gradient(135deg, #2A9D8F, #21867a);
      color: white;
      border: none;
      padding: 12px 20px;
      font-size: 1em;
      cursor: pointer;
      border-radius: 10px;
      transition: transform 0.3s, box-shadow 0.3s;
      width: 100%;
      font-weight: bold;
    }

    .validate-container button:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .validate-container .error {
      color: red;
      font-size: 0.9em;
      margin-bottom: 10px;
    }
  </style>
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600&family=Poppins:wght@700&display=swap" rel="stylesheet">
</head>
<body>
  <div class="validate-container">
    <h2>Validate Identity</h2>
    <form method="POST">
      <input type="hidden" name="email" value="<?php echo htmlspecialchars($emailFromURL); ?>">
      <label for="birthdate">Birthdate:</label>
      <input type="date" id="birthdate" name="birthdate" required>
      
      <?php if (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <button type="submit">Validate</button>
    </form>
  </div>
</body>
</html>
