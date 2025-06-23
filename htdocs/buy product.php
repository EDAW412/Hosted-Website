<?php
session_start();
include 'navbar.php'

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$productId = intval($_POST['product_id']);

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$sql = "SELECT * FROM products WHERE id = ? AND quantity > 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productId);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo "<script>alert('Sorry, this product is out of stock.'); window.location.href='store.php';</script>";
    exit();
}

$update = $conn->prepare("UPDATE products SET quantity = quantity - 1 WHERE id = ?");
$update->bind_param("i", $productId);
$update->execute();

$insert = $conn->prepare("INSERT INTO purchases (user_id, product_id, purchase_date) VALUES (?, ?, NOW())");
$insert->bind_param("ii", $userId, $productId);
$insert->execute();

$conn->close();

header("Location: thank_you.php");
exit();
?>
