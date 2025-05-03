<?php
session_start();

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Database connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "facultrack";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Sanitize inputs
    $email = trim($_POST['email']);
    $birthdate = trim($_POST['birthdate']);

    // Prepare the SQL statement
    $sql = "SELECT * FROM admin WHERE email = ? AND birthdate = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error preparing SQL statement: " . $conn->error);
    }
    $stmt->bind_param("ss", $email, $birthdate);

    // Execute the query
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if record exists
    if ($result->num_rows > 0) {
        // Redirect to reset password page
        header("Location: /admin/areset_password.php?email=" . urlencode($email));
        exit;
    } else {
        $error = "Validation failed. Please check your information.";
    }

    // Close resources
    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/images/white.png">
    <title>Validate Identity</title>
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

        .validate-container {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(42, 157, 143, 0.1));
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 40px 30px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .validate-container h2 {
            font-size: 2em;
            color: #264653;
            margin-bottom: 20px;
        }

        .validate-container label {
            display: block;
            margin-bottom: 8px;
            color: #1F2937;
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
            box-shadow: 0 0 8px rgba(42, 157, 143, 0.5);
        }

        .validate-container button {
            background: linear-gradient(135deg, #2A9D8F, #E9C46A);
            color: #fff;
            border: none;
            padding: 12px 20px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 10px;
            transition: transform 0.3s, box-shadow 0.3s;
            width: 100%;
        }

        .validate-container button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .validate-container .error {
            color: red;
            font-size: 0.9em;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="validate-container">
        <h2>Validate Identity</h2>
        <form action="" method="POST">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
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
