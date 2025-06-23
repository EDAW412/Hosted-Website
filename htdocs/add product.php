<?php
session_start();
$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $requiredFields = ['name', 'description', 'price', 'quantity', 'category'];
    foreach ($requiredFields as $field) {
        if (empty(trim($_POST[$field] ?? ''))) {
            $errors[] = ucfirst($field) . " is required.";
        }
    }
    if (!isset($_SESSION["user_id"])) {
        $errors[] = "User not logged in.";
    }
    if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Image upload is required.";
    }

    if (empty($errors)) {
        $name = $_POST["name"];
        $description = $_POST["description"];
        $price = $_POST["price"];
        $quantity = $_POST["quantity"];
        $category = $_POST["category"];
        $user_id = $_SESSION["user_id"];

        $originalName = $_FILES['image']['name'];
        $imageTmpName = $_FILES['image']['tmp_name'];
        $uniquePrefix = uniqid();
        $imageName = $uniquePrefix . '_' . basename($originalName);
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $targetPath = $uploadDir . $imageName;

        if (move_uploaded_file($imageTmpName, $targetPath)) {
            $stmt = $conn->prepare("INSERT INTO products (name, description, price, quantity, category, image, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdisss", $name, $description, $price, $quantity, $category, $imageName, $user_id);

            if ($stmt->execute()) {
                $successMessage = "Product added successfully! Redirecting...";
            } else {
                $errors[] = "Database error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $errors[] = "Image upload failed.";
        }
    } else {
        $showAlert = true;
    }
}

$conn->close();

$nameVal = htmlspecialchars($_POST['name'] ?? '');
$descVal = htmlspecialchars($_POST['description'] ?? '');
$priceVal = htmlspecialchars($_POST['price'] ?? '');
$quantityVal = htmlspecialchars($_POST['quantity'] ?? '');
$categoryVal = htmlspecialchars($_POST['category'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="images/Laezel_cat.png" type="image/png">

<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Add Product</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
.header {
    width: 100vw;              
    position: relative;        
    left: 50%;                
    right: 50%;
    margin-left: -50vw;       
    margin-right: -50vw;      
    background-color:  #ff6600;
    color: black;
    padding: 1rem 0;
    text-align: center;
    border-radius: 0 0 8px 8px;
    box-sizing: border-box;  
    z-index: 1000;           
}

</style>
<script>
    window.onload = function() {
        <?php if (!empty($errors)): ?>
            alert("<?php echo implode("\\n", array_map('addslashes', $errors)); ?>");
        <?php endif; ?>

        <?php if ($successMessage): ?>
            alert("<?php echo addslashes($successMessage); ?>");
            setTimeout(() => {
                window.location.href = "landing store page.php";
            }, 1000);
        <?php endif; ?>
    };
</script>
</head>
<body>

<?php include 'navbar.php'; ?>

    <div class="header">
        <h2>Add New Product</h2>
    </div>
    <div class="container mt-4">

    <form action="add product.php" method="post" enctype="multipart/form-data" class="mt-3" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Product Name</label>
            <input
                type="text"
                id="name"
                name="name"
                class="form-control"
                value="<?php echo $nameVal; ?>"
                required
            />
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Product Description</label>
            <textarea
                id="description"
                name="description"
                class="form-control"
                rows="3"
                required
            ><?php echo $descVal; ?></textarea>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price (e.g. 19.99)</label>
            <input
                type="number"
                id="price"
                name="price"
                class="form-control"
                step="0.01"
                min="0"
                value="<?php echo $priceVal; ?>"
                required
            />
        </div>

        <div class="mb-3">
            <label for="quantity" class="form-label">Quantity</label>
            <input
                type="number"
                id="quantity"
                name="quantity"
                class="form-control"
                min="1"
                value="<?php echo $quantityVal; ?>"
                required
            />
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select id="category" name="category" class="form-select" required>
                <option value="" disabled <?php echo $categoryVal === '' ? 'selected' : ''; ?>>Select category</option>
                <option value="Cars" <?php echo $categoryVal === 'Cars' ? 'selected' : ''; ?>>Cars</option>
                <option value="Toys" <?php echo $categoryVal === 'Toys' ? 'selected' : ''; ?>>Toys</option>
                <option value="Jewellery" <?php echo $categoryVal === 'Jewellery' ? 'selected' : ''; ?>>Jewellery</option>
                <option value="Pets" <?php echo $categoryVal === 'Pets' ? 'selected' : ''; ?>>Pets</option>
                <option value="Fashion" <?php echo $categoryVal === 'Fashion' ? 'selected' : ''; ?>>Fashion</option>
                <option value="Electronics" <?php echo $categoryVal === 'Electronics' ? 'selected' : ''; ?>>Electronics</option>
                <option value="Furniture" <?php echo $categoryVal === 'Furniture' ? 'selected' : ''; ?>>Furniture</option>
                <option value="Books" <?php echo $categoryVal === 'Books' ? 'selected' : ''; ?>>Books</option>
                <option value="Other" <?php echo $categoryVal === 'Other' ? 'selected' : ''; ?>>Other</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">Product Image</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*" required />
        </div>

        <button type="submit" class="btn btn-primary">Add Product</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
