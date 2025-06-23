<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$userId = $_SESSION['user_id'];

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT p.purchase_date, p.quantity, pr.price, pr.name, pr.image, pr.id AS product_id
        FROM purchases p
        JOIN products pr ON p.product_id = pr.id
        WHERE p.user_id = ?
        ORDER BY p.purchase_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase History | Laezel Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" href="images/Laezel_cat.png" type="image/png">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .btn-orange {
            background-color: #ff6600;
            border-color: #ff6600;
            color: #fff;
            font-weight: 500;
        }
        .btn-orange:hover,
        .btn-orange:focus {
            background-color: #e65c00;
            border-color: #e65c00;
            color: #fff;
        }
        .table img {
            max-height: 50px;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-4">
        <h2>Your Purchase History</h2>
        <?php if ($result && $result->num_rows > 0): ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Purchase Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" class="img-fluid"></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td>R<?php echo number_format($row['price'], 2); ?></td>
                        <td><?php echo htmlspecialchars(date('F j, Y, g:i a', strtotime($row['purchase_date']))); ?></td>
                        <td>
                            <a href="purchase page.php?product_id=<?php echo $row['product_id']; ?>" class="btn btn-orange btn-sm">View Product</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no purchases yet.</p>
        <?php endif; ?>
        <a href="landing store page.php" class="btn btn-primary mt-3">Back to Marketplace</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>