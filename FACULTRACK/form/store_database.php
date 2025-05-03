<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['facultyID'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$facultyID = $_SESSION['facultyID'];

// Fetch existing faculty full record
$fetchSql = "SELECT name, email, password, birthdate, department FROM faculty WHERE facultyID = ? ORDER BY year DESC LIMIT 1";
$fetchStmt = $conn->prepare($fetchSql);
$fetchStmt->bind_param("s", $facultyID);
$fetchStmt->execute();
$fetchResult = $fetchStmt->get_result();

if ($fetchResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Faculty details not found.']);
    exit();
}

$facultyData = $fetchResult->fetch_assoc();
$name = $facultyData['name'];
$email = $facultyData['email'];
$password = $facultyData['password'];
$birthdate = $facultyData['birthdate'];
$department = $facultyData['department'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $totalScore = isset($_POST['totalScore']) ? intval($_POST['totalScore']) : 0;
    $grade = $_POST['grade'] ?? '';
    $allowance = $_POST['allowance'] ?? '';
    $driveLink = $_POST['driveLink'] ?? '';
    $year = isset($_POST['year']) ? intval($_POST['year']) : 0;

    // Clean allowance
    $allowance = intval(preg_replace('/[^\d]/', '', $allowance));

    // Validate inputs
    if (empty($grade) || $allowance < 0 || $totalScore < 0 || empty($driveLink) || $year < 1900 || $year > 2100) {
        $_SESSION['data_inserted'] = false;
        echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
        exit();
    }

    // Check if year already exists
    $checkSql = "SELECT id FROM faculty WHERE facultyID = ? AND year = ?";
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param("si", $facultyID, $year);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        $_SESSION['data_inserted'] = false; // block download
        echo json_encode(['success' => false, 'message' => 'Data for this year already exists.']);
        exit();
    }

    // Correct param types: s = string, i = integer
    $insertSql = "INSERT INTO faculty (facultyID, name, email, password, birthdate, department, grade, allowance, total_score, drive_link, year)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("sssssssissi", $facultyID, $name, $email, $password, $birthdate, $department, $grade, $allowance, $totalScore, $driveLink, $year);

    if ($insertStmt->execute()) {
        $_SESSION['data_inserted'] = true; // allow PDF download
        echo json_encode(['success' => true, 'message' => 'Data inserted successfully.']);
    } else {
        $_SESSION['data_inserted'] = false;
        echo json_encode(['success' => false, 'message' => 'Database insert failed.']);
    }

    $insertStmt->close();
    $checkStmt->close();
    $fetchStmt->close();
    $conn->close();
} else {
    $_SESSION['data_inserted'] = false;
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}
?>
