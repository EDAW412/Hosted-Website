<?php
session_start();

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;
$userId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if ($productId <= 0 || $quantity <= 0) {
    die("Invalid request.");
}

if ($userId === 0) {
    die("You must be logged in to make a purchase.");
}

$productQuery = "SELECT * FROM products WHERE id = $productId";
$productResult = $conn->query($productQuery);

if (!$productResult || $productResult->num_rows === 0) {
    die("Product not found.");
}

$product = $productResult->fetch_assoc();

if ($quantity > $product['quantity']) {
    die("Not enough stock available.");
}

$newQuantity = $product['quantity'] - $quantity;
$updateQuery = "UPDATE products SET quantity = $newQuantity WHERE id = $productId";
$conn->query($updateQuery);

$purchaseInsert = $conn->prepare("INSERT INTO purchases (user_id, product_id, quantity, purchase_date) VALUES (?, ?, ?, NOW())");
$purchaseInsert->bind_param("iii", $userId, $productId, $quantity);
$purchaseInsert->execute();

header("Location: purchase_success.php?product_id=$productId&qty=$quantity");
exit;
