<?php
session_start();

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$productId = intval($_POST['product_id'] ?? 0);
$quantity = intval($_POST['quantity'] ?? 0);

if ($productId <= 0 || $quantity <= 0) {
    die("Invalid request.");
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $productId);
$stmt->execute();
$productResult = $stmt->get_result();

if ($productResult->num_rows === 0) {
    die("Product not found.");
}

$product = $productResult->fetch_assoc();
$paymentSuccess = false;
$errors = [];

if (isset($_POST['pay_now'])) {
    $cardName = trim($_POST['card_name'] ?? '');
    $cardNumber = trim($_POST['card_number'] ?? '');
    $expiry = trim($_POST['expiry'] ?? '');
    $cvv = trim($_POST['cvv'] ?? '');

    if ($cardName === '' || $cardNumber === '' || $expiry === '' || $cvv === '') {
        $errors[] = "All fields are required.";
    } elseif (!preg_match('/^\d{16}$/', $cardNumber)) {
        $errors[] = "Card number must be 16 digits.";
    } elseif (!preg_match('/^\d{3}$/', $cvv)) {
        $errors[] = "CVV must be 3 digits.";
    }

    if (empty($errors)) {
        $newQty = $product['quantity'] - $quantity;
        if ($newQty < 0) {
            $errors[] = "Not enough stock.";
        } else {
            $updateStmt = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
            $updateStmt->bind_param("ii", $newQty, $productId);
            $updateStmt->execute();

            $userId = $_SESSION['user_id'] ?? 0;
            if ($userId <= 0) {
                die("User not logged in.");
            }

            $purchaseStmt = $conn->prepare("INSERT INTO purchases (user_id, product_id, quantity, purchase_date) VALUES (?, ?, ?, NOW())");
            $purchaseStmt->bind_param("iii", $userId, $productId, $quantity);
            $purchaseStmt->execute();

            $paymentSuccess = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Pay for <?php echo htmlspecialchars($product['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="card p-4 shadow-sm">
        <h3>Payment for "<?php echo htmlspecialchars($product['name']); ?>"</h3>
        <p><strong>Quantity:</strong> <?php echo $quantity; ?></p>
        <p><strong>Total:</strong> R<?php echo number_format($product['price'] * $quantity, 2); ?></p>

        <?php if ($paymentSuccess): ?>
            <div class="alert alert-success">Payment successful! Thank you for your purchase.</div>
            <a href="landing store page.php" class="btn btn-primary">Return to Homepage</a>
        <?php else: ?>
            <?php if ($errors): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $e) echo "<p>$e</p>"; ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                <input type="hidden" name="quantity" value="<?php echo $quantity; ?>">

                <div class="mb-3">
                    <label>Cardholder Name</label>
                    <input type="text" name="card_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Card Number</label>
                    <input type="text" name="card_number" class="form-control" maxlength="16" required>
                </div>
                <div class="mb-3">
                    <label>Expiry</label>
                    <input type="month" name="expiry" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>CVV</label>
                    <input type="text" name="cvv" class="form-control" maxlength="3" required>
                </div>

                <button type="submit" name="pay_now" class="btn btn-success">Pay Now</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
