<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id'];
    $productId = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $reason = isset($_POST['reason']) ? trim($_POST['reason']) : '';
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    if ($productId <= 0 || empty($reason)) {
        header('Location: product_info.php?product_id=' . $productId);
        exit;
    }

    $conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("INSERT INTO product_reports (user_id, product_id, reason, comment, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiss", $userId, $productId, $reason, $comment);
    $stmt->execute();
    $stmt->close();
    $conn->close();

} else {
    header('Location: landing store page.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Report Submitted</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        .btn-orange {
            background-color: #ff6600;
            border-color: #ff6600;
            color: #fff;
        }
        .btn-orange:hover {
            background-color: #e65c00; 
            border-color: #e65c00;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container mt-5 text-center">
        <div class="card shadow-sm p-4">
            <h2>Thank You for Your Report</h2>
            <p>An admin will be in contact with you soon if an issue is found.</p>
            <a href="purchase page.php?product_id=<?php echo htmlspecialchars($productId); ?>" class="btn btn-orange mt-3">Back to Product</a>
            <a href="landing store page.php" class="btn btn-secondary mt-3 ms-2">Back to Store</a>
        </div>
    </div>
</body>
</html>