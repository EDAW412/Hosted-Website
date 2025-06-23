<?php
session_start();

if (!isset($_SESSION['staff_role']) || $_SESSION['staff_role'] !== 'admin') {
    header('Location: staff login.php');
    exit();
}

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header('Location: admin manage products.php?message=Product+deleted+successfully');
        exit();
    } else {
        $stmt->close();
        $conn->close();
        header('Location: admin manage products.php?error=Failed+to+delete+product');
        exit();
    }
}

function truncateText($text, $maxLength = 25) {
    if (strlen($text) > $maxLength) {
        return htmlspecialchars(substr($text, 0, $maxLength - 3)) . '...';
    }
    return htmlspecialchars($text);
}

$sql = "SELECT p.id, p.name AS product_name, p.description, p.price, p.image, p.quantity, u.name, u.email 
        FROM products p 
        JOIN users u ON p.user_id = u.id
        ORDER BY p.id ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Customer Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        img.product-image {
            max-width: 100px;
            max-height: 70px;
            object-fit: cover;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1>Manage Customer Products</h1>

    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <table class="table table-striped bg-white shadow-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Email</th>
                <th>Product Name</th>
                <th>Price</th>
                <th>Image</th>
                <th>Description</th>
                <th>Quantity</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo truncateText($row['product_name']); ?></td>
                <td><?php echo 'R ' . number_format($row['price'], 2); ?></td>
                <td>
                    <?php if ($row['image']): ?>
                        <img src="uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" class="product-image" />
                    <?php else: ?>
                        No image
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo intval($row['quantity']); ?></td>
                <td>
                    <a href="admin manage products.php?delete=<?php echo $row['id']; ?>" 
                       class="btn btn-danger btn-sm" 
                       onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <a href="staff landing page.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>
</body>
</html>

<?php
$conn->close();
?>