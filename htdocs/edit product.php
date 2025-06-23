<?php

session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (!isset($_GET['id'])) {
    header('Location: my listings.php');
    exit();
}

$productId = intval($_GET['id']);

$stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $productId, $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    $stmt->close();
    $conn->close();
    header('Location: my listings.php');
    exit();
}

$product = $result->fetch_assoc();
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $category = $_POST['category'] ?? '';


    if (!empty($_FILES['image']['name'])) {
        $targetDir = "uploads/";
        $imageFileType = strtolower(pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION));
        $newFileName = uniqid() . '.' . $imageFileType;
        $targetFile = $targetDir . $newFileName;

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFile)) {
            $oldImage = $product['image'];
            if (file_exists($targetDir . $oldImage)) {
                unlink($targetDir . $oldImage);
            }
            $imageToSave = $newFileName;
        } else {
            $imageToSave = $product['image']; 
        }
    } else {
        $imageToSave = $product['image']; 
    }

    $updateStmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, quantity = ?, category = ?, image = ? WHERE id = ? AND user_id = ?");
    $updateStmt->bind_param("ssdissii", $name, $description, $price, $quantity, $category, $imageToSave, $productId, $userId);
    $updateStmt->execute();

    $updateStmt->close();
    $conn->close();

    header('Location: my listings.php');
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>

<body>
<div class="container mt-5">
    <h2>Edit Product</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="text" name="name" class="form-control mb-2" placeholder="Product Name" required value="<?php echo htmlspecialchars($product['name']); ?>">
        <textarea name="description" class="form-control mb-2" placeholder="Description" required><?php echo htmlspecialchars($product['description']); ?></textarea>
        <input type="number" step="0.01" name="price" class="form-control mb-2" placeholder="Price" required value="<?php echo htmlspecialchars($product['price']); ?>">
        <input type="number" name="quantity" class="form-control mb-2" placeholder="Quantity" required value="<?php echo htmlspecialchars($product['quantity']); ?>">

        <select name="category" class="form-control mb-2" required>
            <option value="">Select Category</option>
            <?php
            $categories = ['Cars', 'Toys', 'Jewellery', 'Pets', 'Fashion', 'Electronics', 'Furniture', 'Books', 'Other'];
            foreach ($categories as $cat) {
                $selected = $cat === $product['category'] ? 'selected' : '';
                echo "<option value=\"$cat\" $selected>$cat</option>";
            }
            ?>
        </select>

        <p>Current Image:</p>
        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" style="max-width:150px; display:block; margin-bottom:10px;">

        <input type="file" name="image" class="form-control mb-3">
        <small class="text-muted">Upload a new image to replace the current one (optional).</small>

        <button type="submit" class="btn btn-primary">Update Product</button>
        <a href="my listings.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
