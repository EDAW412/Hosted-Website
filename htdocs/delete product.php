<?php
session_start();
include 'navbar.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $userId = $_SESSION['user_id'];
    $productId = intval($_POST['id']);

    $conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $productId, $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $product = $result->fetch_assoc();

        $deleteStmt = $conn->prepare("DELETE FROM products WHERE id = ? AND user_id = ?");
        $deleteStmt->bind_param("ii", $productId, $userId);
        $deleteStmt->execute();

        $imagePath = 'Uploads/' . $product['image'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        $deleteStmt->close();
        $_SESSION['message'] = "Product deleted successfully.";
    } else {
        $_SESSION['message'] = "Product not found or you don't have permission to delete it.";
    }

    $stmt->close();
    $conn->close();
} else {
    $_SESSION['message'] = "Invalid request.";
}

header('Location: my listings.php');
exit();
?>