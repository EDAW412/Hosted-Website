<?php
session_start();

$servername = "sql110.infinityfree.com";
$username = "if0_39218282";
$password = "WaddlePaddle412";
$database = "if0_39218282_laezel_marketplace";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name'] ?? '');
    $surname = trim($_POST['surname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $id_number = trim($_POST['id_number'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $province = trim($_POST['province'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($name) || empty($surname) || empty($email) || empty($id_number) || empty($phone) || empty($address) || empty($province) || empty($password)) {
        header("Location: signup.php?error=All fields are required");
        exit;
    }

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    
    $stmt->close();

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, surname, email, id_number, phone, address, province, password) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $name, $surname, $email, $id_number, $phone, $address, $province, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $conn->insert_id;
        $_SESSION['user_name'] = $name;
        $stmt->close();
        $conn->close();
        header("Location: registration_success.php");
        exit;
    } else {
        $stmt->close();
        header("Location: signup.php?error=Registration failed. Please try again");
        exit;
    }
}

$conn->close();
?>