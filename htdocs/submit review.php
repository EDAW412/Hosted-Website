<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to leave a review.";
    exit;
}

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$userId = $_SESSION['user_id'];
$productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$rating = isset($_POST['rating']) ? intval($_POST['rating']) : null;
$comment = trim($_POST['comment'] ?? '');

if ($productId <= 0) {
    echo "Invalid product ID.";
    exit;
}
if ($rating < 1 || $rating > 5) {
    echo "Invalid rating. Please submit a rating between 1 and 5.";
    exit;}

$checkQuery = $conn->prepare("SELECT id FROM reviews WHERE user_id = ? AND product_id = ?");
$checkQuery->bind_param("ii", $userId, $productId);
$checkQuery->execute();
$checkResult = $checkQuery->get_result();

if ($checkResult->num_rows > 0) {
    echo "You have already submitted a review for this product.";
} else {
    $insertQuery = $conn->prepare("INSERT INTO reviews (product_id, user_id, rating, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    $insertQuery->bind_param("iiis", $productId, $userId, $rating, $comment);

    if ($insertQuery->execute()) {
        echo "Thank you for your review!";
    } else {
        echo "Error submitting review: " . $conn->error;
    }

    $insertQuery->close();
}

$checkQuery->close();
$conn->close();
?>
