<?php
session_start();

include 'db.php';

// Sanitize and validate inputs
$email = trim($_POST['email']);
$plain_password = trim($_POST['password']);

// Check if email is valid
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: /faculty/resetpass.php?email=" . urlencode($email) . "&status=invalid_email");
    exit;
}

if (empty($email) || empty($plain_password)) {
    header("Location: /faculty/resetpass.php?email=" . urlencode($email) . "&status=invalid_input");
    exit;
}

$hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

// Prepare the SQL statement
$sql = "UPDATE faculty SET password = ? WHERE email = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    header("Location: /faculty/resetpass.php?email=" . urlencode($email) . "&status=stmt_error");
    $conn->close();
    exit;
}

$stmt->bind_param("ss", $hashed_password, $email);

if ($stmt->execute()) {
    $stmt->close();
    $conn->close();
    header("Location: /faculty/resetpass.php?email=" . urlencode($email) . "&status=success");
    exit;
} else {
    error_log("Execute failed: " . $stmt->error);
    $stmt->close();
    $conn->close();
    header("Location: /faculty/resetpass.php?email=" . urlencode($email) . "&status=update_error");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - FaculTrack</title>
    <link rel="icon" type="image/png" href="/images/white.png">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(to bottom right, #F0F4F8, #264653, #F0F4F8);
            color: #333;
        }

        .reset-password-container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .reset-password-container h2 {
            font-size: 1.8em;
            color: #264653;
            margin-bottom: 20px;
        }

        .reset-password-container label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: bold;
            text-align: left;
        }

        .reset-password-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }

        .reset-password-container button {
            background-color: #2A9D8F;
            color: #fff;
            border: none;
            padding: 10px 15px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
            width: 100%;
        }

        .reset-password-container button:hover {
            background-color: #21867a;
        }

        .reset-password-container a {
            color: #2A9D8F;
            text-decoration: none;
            margin-top: 15px;
            display: inline-block;
        }

        .reset-password-container a:hover {
            text-decoration: underline;
        }

        .success-message {
            color: green;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="reset-password-container">
        <h2>Reset Password</h2>
        <?php
        if (isset($_GET['status']) && $_GET['status'] === 'success') {
            echo '<p class="success-message">Password has been reset successfully!</p>';
        } elseif (isset($_GET['status']) && $_GET['status'] === 'invalid_input') {
            echo '<p class="error-message">Please fill in all fields.</p>';
        } elseif (isset($_GET['status']) && $_GET['status'] === 'stmt_error') {
            echo '<p class="error-message">Something went wrong. Try again later.</p>';
        } elseif (isset($_GET['status']) && $_GET['status'] === 'update_error') {
            echo '<p class="error-message">Failed to update password. Please try again.</p>';
        } elseif (isset($_GET['status']) && $_GET['status'] === 'invalid_email') {
            echo '<p class="error-message">Invalid email address.</p>';
        }
        ?>
        <form action="/faculty/resetpass.php" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" placeholder="Enter new password" required>
            <button type="submit">Reset Password</button>
        </form>
        <a href="faclogin.php">Back to Login</a>
    </div>
</body>
</html>
