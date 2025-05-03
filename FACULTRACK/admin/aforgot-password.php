<?php
session_start();

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Check if email exists
    $sql = "SELECT * FROM admin WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Redirect to validation page
        header("Location: /admin/validate_identity.php?email=" . urlencode($email));
    } else {
        echo "Email not found.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - FaculTrack</title>
    <link rel="icon" type="image/png" href="/images/white.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(150deg, #F0F4F8, #264653, #F0F4F8);
            color: #1F2937;
        }

        .forgot-password-container {
            background-color: rgba(255, 255, 255, 0.95);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .forgot-password-container h2 {
            font-size: 1.8em;
            color: #264653;
            margin-bottom: 20px;
        }

        .forgot-password-container p {
            font-size: 0.9em;
            color: #2A9D8F;
            margin-bottom: 20px;
        }

        .forgot-password-container label {
            display: block;
            margin-bottom: 8px;
            color: #264653;
            font-weight: bold;
        }

        .forgot-password-container input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
        }

        .forgot-password-container button {
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

        .forgot-password-container button:hover {
            background-color: #E9C46A;
        }

        .forgot-password-container a {
            color: #2A9D8F;
            text-decoration: none;
            margin-top: 15px;
            display: inline-block;
        }

        .forgot-password-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <h2>Forgot Password?</h2>
        <p>Please enter your registered email address to receive a password reset link.</p>
        <form action="aforgot-password.php" method="POST">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
            <button type="submit">Verification</button>
        </form>
        <a href="/admin/adminlog.php">Back to Login</a>
    </div>
</body>
</html>
