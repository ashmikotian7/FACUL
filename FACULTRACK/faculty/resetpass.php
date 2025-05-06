<?php
session_start();
include 'db.php';

$status = "";

// Run password reset logic only if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $email = trim($_POST['email'] ?? '');
    $plain_password = trim($_POST['password'] ?? '');

    // Validate inputs
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $status = "invalid_email";
    } elseif (empty($email) || empty($plain_password)) {
        $status = "invalid_input";
    } else {
        $hashed_password = password_hash($plain_password, PASSWORD_BCRYPT);

        $sql = "UPDATE faculty SET password = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("ss", $hashed_password, $email);
            if ($stmt->execute()) {
                $status = "success";
            } else {
                error_log("Execute failed: " . $stmt->error);
                $status = "update_error";
            }
            $stmt->close();
        } else {
            error_log("Prepare failed: " . $conn->error);
            $status = "stmt_error";
        }
    }

    $conn->close();
    // Redirect with email and status to prevent resubmission on refresh
    header("Location: resetpass.php?email=" . urlencode($email) . "&status=" . $status);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - FaculTrack</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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

        h2 {
            font-size: 1.8em;
            color: #264653;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            color: #333;
            font-weight: bold;
            text-align: left;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        button {
            background-color: #2A9D8F;
            color: #fff;
            border: none;
            padding: 10px 15px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }

        button:hover {
            background-color: #21867a;
        }

        a {
            color: #2A9D8F;
            text-decoration: none;
            margin-top: 15px;
            display: inline-block;
        }

        a:hover {
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
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
            switch ($status) {
                case 'success':
                    echo '<p class="success-message">Password has been reset successfully!</p>';
                    break;
                case 'invalid_input':
                    echo '<p class="error-message">Please fill in all fields.</p>';
                    break;
                case 'stmt_error':
                    echo '<p class="error-message">Something went wrong. Try again later.</p>';
                    break;
                case 'update_error':
                    echo '<p class="error-message">Failed to update password. Please try again.</p>';
                    break;
                case 'invalid_email':
                    echo '<p class="error-message">Invalid email address.</p>';
                    break;
            }
        }
        ?>
        <form method="POST" action="resetpass.php">
            <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>">
            <label for="password">New Password</label>
            <input type="password" name="password" id="password" placeholder="Enter new password" required>
            <button type="submit">Reset Password</button>
        </form>
        <a href="faclogin.php">Back to Login</a>
    </div>
</body>
</html>
