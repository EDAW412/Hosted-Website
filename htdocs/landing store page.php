<?php
session_start();
$name = $_SESSION['user_name'] ?? '';
$surname = $_SESSION['user_surname'] ?? '';

$userInitials = strtoupper(
    ($name ? $name[0] : '') .
    ($surname ? $surname[0] : '')
);

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
$selectedCategory = $_GET['category'] ?? 'All';

$query = "
    SELECT p.*, COALESCE(AVG(r.rating), 0) AS avg_rating, COUNT(r.id) AS review_count
    FROM products p
    LEFT JOIN reviews r ON p.id = r.product_id
    WHERE p.quantity > 0
";

$params = [];
$types = '';

if ($searchTerm !== '') {
    $query .= " AND p.name LIKE CONCAT('%', ?, '%')";
    $params[] = $searchTerm;
    $types .= 's';
}

if ($selectedCategory !== 'All') {
    $query .= " AND p.category = ?";
    $params[] = $selectedCategory;
    $types .= 's';
}

$query .= " GROUP BY p.id ORDER BY p.created_at DESC";

$stmt = $conn->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" href="images/Laezel_cat.png" type="image/png">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Velnor Marketplace</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<style>
  body {
      background-color: #f4f4f4;
  }
  .navbar {
      margin-bottom: 2rem;
  }
  .navbar-brand {
  font-family: 'Dancing Script', cursive;
    font-size: 1.75rem;  /* bigger store name */
    font-weight: 600;
    gap: 10px;           /* space between image and text */
    display: flex;
    align-items: left;
    padding-left: 0;     /* remove any left padding */
}
.navbar-brand img {
    margin-right: 0;
    height: 30px;
    width: auto;
}
.logo-img {
    height: 30px;
    width: 30px;    /* Ensure width and height are equal for a perfect circle */
    border-radius: 50%;
    object-fit: cover;  /* Makes sure the image covers the entire circle */
}



  .product-card {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 1rem;
      background-color: white;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
      height: 100%;
       white-space: nowrap;      /* Keep the text on one line */
  overflow: hidden;         /* Hide the overflow */
  text-overflow: ellipsis;  /* Add ellipsis (...) at the end */
  
  }
  .product-card img {
      max-width: 100%;
      height: 150px;
      object-fit: cover;
      border-radius: 4px;
  }
  form[role="search"] input.form-control {
      font-size: 0.8rem;
      height: 30px;
      padding: 0.25rem 0.5rem;
  }
  form[role="search"] button {
      font-size: 0.8rem;
      height: 30px;
      padding: 0.25rem 0.75rem;
  }
  .btn, .nav-link {
      font-size: 0.8rem;
      padding: 0.25rem 0.75rem;
  }
  .dropdown-toggle {
      font-size: 0.8rem;
      height: 30px;
      padding: 0.25rem 0.75rem;
  }
  .navbar-collapse {
      display: flex;
      align-items: center;
      justify-content: space-between;
  }
    nav .mx-auto {
        flex-grow: 1;
        max-width: 600px;  
    }

    nav form[role="search"] input.form-control {
        width: 100% !important;
        min-width: 300px;
    }

  .ms-auto {
      display: flex;
      align-items: center;
      gap: 0.5rem;
  }

  .btn-primary {
      background-color: #ff6600; /* Orange */
      border-color: #ff6600;
  }
  .btn-primary:hover,
  .btn-primary:focus {
      background-color: #e65c00; /* Darker orange on hover */
      border-color: #e65c00;
  }
  .btn-outline-success {
      color: #ff6600;
      border-color: #ff6600;
  }
  .btn-outline-success:hover,
  .btn-outline-success:focus {
      background-color: #ff6600;
      color: white;
      border-color: #ff6600;
  }
  .btn-secondary {
      background-color: #ff6600;
      border-color: #ff6600;
  }
  .btn-secondary:hover,
  .btn-secondary:focus {
      background-color: #e65c00;
      border-color: #e65c00;
  }

  @media (max-width: 768px) {
      .navbar-collapse {
          flex-direction: column;
          align-items: stretch;
      }
      .mx-auto {
          max-width: 100%;
          margin-bottom: 1rem;
      }
      .ms-auto {
          flex-direction: column;
          align-items: stretch;
          width: 100%;
      }
      .ms-auto .btn, .ms-auto .nav-link, .ms-auto .dropdown {
          width: 100%;
          text-align: center;
          margin-bottom: 0.5rem;
      }
      .ms-auto .dropdown-menu {
          width: 100%;
      }
      form[role="search"] input.form-control {
    width: 400px !important;  /* force wider width */
    max-width: 100%;          /* prevent overflow on small screens */
}

}

      

</style>

</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="landing store page.php" style="gap: 10px;">
    <img src="images/Laezel_cat.png" alt="Laezel Cat Logo" class="logo-img">

<span style="font-size: 1.5rem; font-weight: 700;">Velnor</span>
</a>


        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="mx-auto">
                <form method="GET" action="" class="d-flex" role="search" aria-label="Product search form">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search products..."
                        aria-label="Search products" value="<?php echo htmlspecialchars($searchTerm); ?>">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>

            <div class="d-flex align-items-center ms-auto">
                <div class="dropdown me-2">
                    <?php
                    $displayCategory = ($selectedCategory && $selectedCategory !== 'All') ? htmlspecialchars($selectedCategory) : 'Categories';
                    ?>
                    <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="categoriesDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true" aria-label="Select product category">
                        <?php echo $displayCategory; ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="categoriesDropdown">
                        <li>
                            <a class="dropdown-item" href="?<?php echo http_build_query(['search' => $searchTerm, 'category' => 'All']); ?>">
                                All Categories
                            </a>
                        </li>
                        <?php
                        $categorySql = "SELECT DISTINCT category FROM products ORDER BY category ASC";
                        $categoryResult = $conn->query($categorySql);
                        if ($categoryResult && $categoryResult->num_rows > 0) {
                            while ($cat = $categoryResult->fetch_assoc()) {
                                $catName = htmlspecialchars($cat['category']);
                                $linkParams = http_build_query(['search' => $searchTerm, 'category' => $cat['category']]);
                                $activeClass = ($selectedCategory === $cat['category']) ? ' active' : '';
                                echo "<li><a class=\"dropdown-item$activeClass\" href=\"?$linkParams\">$catName</a></li>";
                            }
                        }
                        ?>
                    </ul>
                </div>

                <a href="contact 2.html" class="btn btn-secondary me-2">Contact Us</a>
                <a class="nav-link" href="my listings.php">My Listings</a>
                <a class="nav-link me-2" href="add product.php">Add Product</a>

                <div class="dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center p-0" href="#" id="profileDropdown"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-haspopup="true" aria-label="User menu" style="cursor:pointer;">
                        <div style="
                            width: 32px;
                            height: 32px;
                            border-radius: 50%;
                            background-color: #ff6600;
                            color: white;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            font-weight: bold;
                            font-size: 14px;
                            user-select: none;">
                            <?php echo htmlspecialchars($userInitials); ?>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li><a class="dropdown-item" href="purchase history.php">Purchase History</a></li>
                        <li><a class="dropdown-item"href=" submit feedback.php">Send Us Feeback</a><li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>


<div class="container">
    <div class="row">
        <?php
        if ($result && $result->num_rows > 0) {
            while ($product = $result->fetch_assoc()) {
                ?>
                <div class="col-md-4 mb-4">
                    <div class="product-card">
                        <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image" />
                        <h5 class="mt-2"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="text-muted">R<?php echo number_format($product['price'], 2); ?></p>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <p>Amount in stock: <?php echo htmlspecialchars($product['quantity']); ?></p>
                        <?php if ($product['review_count'] > 0): ?>
                            <p>Average Rating: <?php echo number_format($product['avg_rating'], 1); ?> ⭐️ (<?php echo $product['review_count']; ?> reviews)</p>
                        <?php else: ?>
                            <p>No reviews yet</p>
                        <?php endif; ?>
                        <form method="POST" action="buy product.php">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <a href="purchase page.php?product_id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">View Product</a>
                        <form>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p>No products available right now.</p>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
