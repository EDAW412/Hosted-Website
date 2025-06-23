<?php
session_start();

if (!isset($_SESSION['staff_id'])) {
    header('Location: staff login.php');
    exit();
}

if (!isset($_GET['id'])) {
    echo "Product ID is missing.";
    exit();
}

$product_id = intval($_GET['id']);

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $stmt_delete = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt_delete->bind_param('i', $product_id);
    if ($stmt_delete->execute()) {
        $stmt_delete->close();
        $conn->close();
        header('Location: admin manage products.php?message=Product+deleted+successfully');
        exit();
    } else {
        echo "<div class='alert alert-danger'>Failed to delete product.</div>";
    }
}

$stmt = $conn->prepare("
    SELECT p.*, u.name AS owner_name, u.email AS owner_email 
    FROM products p
    JOIN users u ON p.user_id = u.id
    WHERE p.id = ?
");
$stmt->bind_param('i', $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Product not found.";
    exit();
}

$product = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Product Details</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .product-image { max-width: 200px; max-height: 200px; object-fit: cover; }
  </style>
</head>
<body class="bg-light">

<div class="container mt-5">
  <h1>Product Details</h1>

  <div class="card shadow-sm p-4 mt-4">
    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
    <p><strong>Description:</strong> <?php echo htmlspecialchars($product['description']); ?></p>
    <p><strong>Price:</strong> $<?php echo number_format($product['price'], 2); ?></p>
    <p><strong>Quantity:</strong> <?php echo intval($product['quantity']); ?></p>

    <?php if ($product['image']): ?>
      <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" class="product-image" />
    <?php else: ?>
      <p><em>No image available.</em></p>
    <?php endif; ?>

    <hr>
    <h5>Owner Info</h5>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($product['owner_name']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($product['owner_email']); ?></p>

    <form method="post" onsubmit="return confirm('Are you sure you want to delete this product?');" class="mt-4">
      <input type="hidden" name="delete_product" value="1" />
      <button type="submit" class="btn btn-danger">Delete Product</button>
    </form>
  </div>

  <a href="view reports.php" class="btn btn-secondary mt-3">Back to Reports</a>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
