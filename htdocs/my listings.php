<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

$sql = "SELECT * FROM products WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Listings - Velnor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="icon" href="images/Laezel_cat.png" type="image/png">

    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .header {
            background-color: #ff6600;
            color: #fff;
            padding: 1rem 0;
            text-align: center;
            margin-bottom: 1rem;
            border-radius: 0 0 8px 8px;
        }
        .listings-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1rem;
            padding: 0.5rem;
        }
        .product-card {
            background-color: #fff;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .product-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 16px rgba(0,0,0,0.1);
        }
        .product-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-bottom: 1px solid #eee;
        }
        .product-card h6 {
            margin: 0.5rem 0 0.25rem;
            font-size: 1rem;
            font-weight: 600;
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
        }
        .product-card p {
            font-size: 0.85rem;
            margin: 0 0.5rem 0.25rem;
            color: #555;
        }
        .product-card .price {
            font-weight: bold;
            color: #28a745;
        }
        .btn-group {
            margin: 0.5rem 0;
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }
        .btn {
            font-size: 0.8rem;
            padding: 4px 0;
            width: 70px;
        }
    </style>
</head>
<body>
    <?php include 'navbar.php'; ?>
    <div class="header">
        <h2>My Listings</h2>
    </div>
    <div class="container">
        <?php
        if (isset($_SESSION['message'])) {
            echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>" .
                 htmlspecialchars($_SESSION['message']) .
                 "<button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button></div>";
            unset($_SESSION['message']);
        }
        ?>
        <div class="listings-container">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($product = $result->fetch_assoc()) {
                    ?>
                    <div class="product-card">
                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                        <h6 class="text-center"><?php echo htmlspecialchars($product['name']); ?></h6>
                        <p class="price text-center">R<?php echo number_format($product['price'], 2); ?></p>
                        <p class="text-center"><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="text-center text-muted mb-1">Stock: <?php echo htmlspecialchars($product['quantity']); ?></p>
                        <div class="btn-group">
                            <a href="edit product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                            <form method="POST" action="delete product.php" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='text-center text-muted'>You have no product listings yet.</p>";
            }
            $stmt->close();
            $conn->close();
            ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>