<?php
session_start();

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$productId = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

if ($productId <= 0) {
    die("Invalid product ID.");
}

$productQuery = "SELECT p.*, u.name AS seller_name, u.surname AS seller_surname, u.email AS seller_email
                 FROM products p
                 JOIN users u ON p.user_id = u.id
                 WHERE p.id = ?";
$stmt = $conn->prepare($productQuery);
$stmt->bind_param("i", $productId);
$stmt->execute();
$productResult = $stmt->get_result();

if ($productResult->num_rows === 0) {
    die("Product not found.");
}

$product = $productResult->fetch_assoc();

$reviewQuery = $conn->prepare("SELECT r.rating, r.comment, u.name, r.created_at FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
$reviewQuery->bind_param("i", $productId);
$reviewQuery->execute();
$reviewResult = $reviewQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="images/Laezel_cat.png" type="image/png">
    <meta charset="UTF-8" />
    <title>Product Information - <?php echo htmlspecialchars($product['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
    <?php include 'navbar.php'; ?>

<style>

.btn-orange {
    background-color: #ff6600;
    border-color: #ff6600;
    color: #fff;
}

.btn-orange:hover,
.btn-orange:focus {
    background-color: #e65c00;
    border-color: #e65c00;
    color: #fff;
}

</style>

<div class="container mt-5">
    <div class="card shadow-sm">
        <div class="row g-0">
            <div class="col-md-5">
                <img src="uploads/<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid rounded-start" alt="Product Image" />
            </div>
            <div class="col-md-7">
                <div class="card-body">
                    <h3 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p class="card-text small"><?php echo htmlspecialchars($product['description']); ?></p>
                    <p class="card-text small"><strong>Price:</strong> R<?php echo number_format($product['price'], 2); ?></p>
                    <p class="card-text small"><strong>In Stock:</strong> <?php echo (int)$product['quantity']; ?></p>

                    <hr />

                    <h6 class="small">Listed By:</h6>
                    <p class="small mb-1"><?php echo htmlspecialchars($product['seller_name'] . ' ' . $product['seller_surname']); ?></p>
                    <p class="small">
                        <a href="mailto:<?php echo htmlspecialchars($product['seller_email']); ?>">
                            <?php echo htmlspecialchars($product['seller_email']); ?>
                        </a>
                    </p>

                    <form action="payment.php" method="post" class="mt-3">
                        <input type="hidden" name="product_id" value="<?php echo $productId; ?>" />
                        <div class="mb-2">
                            <label for="quantity" class="form-label small">Quantity to Purchase:</label>
                            <input type="number" name="quantity" id="quantity" class="form-control form-control-sm" min="1" max="<?php echo (int)$product['quantity']; ?>" required />
                        </div>
                        <button type="submit" class="btn btn-success btn-sm">Confirm Purchase</button>
                        <a href="landing store page.php" class="btn btn-secondary btn-sm ms-2">Cancel</a>
                    </form>

                    <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="mt-3">
                        <button class="btn btn-danger btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#reportCollapse" aria-expanded="false" aria-controls="reportCollapse">
                            Report Product
                        </button>
                        <div class="collapse mt-2" id="reportCollapse">
                            <form method="POST" action="submit report.php" class="small">
                                <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                                <div class="mb-2">
                                    <label for="reason" class="form-label">Reason for Report:</label>
                                    <select name="reason" id="reason" class="form-select form-select-sm" required>
                                        <option value="">Select a reason</option>
                                        <option value="Inappropriate Content">Inappropriate Content</option>
                                        <option value="Fraudulent or Scam">Fraudulent or Scam</option>
                                        <option value="Copyright Violation">Copyright Violation</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label for="comment" class="form-label">Additional Comments (optional):</label>
                                    <textarea name="comment" id="comment" class="form-control form-control-sm" rows="2"></textarea>
                                </div>
                                <button type="submit" class="btn btn-danger btn-sm">Submit Report</button>
                            </form>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['user_id'])): ?>
                    <div class="mt-4">
                        <h6 class="small">Leave a Review (Optional)</h6>
                        <form method="POST" action="submit review.php" class="small">
                            <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                            <div class="mb-1">
                                <label for="rating" class="form-label">Rating (1-5):</label>
                                <select name="rating" id="rating" class="form-select form-select-sm" style="width: auto;">
                                    <option value="">No Rating</option>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?> Star<?php echo $i > 1 ? 's' : ''; ?></option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="mb-1">
                                <label for="comment" class="form-label">Comment (Optional):</label>
                                <textarea name="comment" id="comment" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                            <button type="submit" class="btn btn-orange btn-sm">Submit Review</button>
                        </form>
                    </div>
                    <?php else: ?>
                        <p class="mt-3 text-muted small">Log in to leave a review.</p>
                    <?php endif; ?>

                    <div class="mt-3">
                        <h6 class="small">
                            <a class="text-decoration-none" data-bs-toggle="collapse" href="#reviewsCollapse" role="button" aria-expanded="false" aria-controls="reviewsCollapse">
                                Reviews (<?php echo $reviewResult->num_rows; ?>) <span class="ms-1">&#x25BC;</span>
                            </a>
                        </h6>

                        <div class="collapse" id="reviewsCollapse">
                            <?php
                            if ($reviewResult->num_rows > 0):
                                while ($review = $reviewResult->fetch_assoc()):
                            ?>
                            <div class="mb-2 p-2 border rounded bg-light">
                                <strong><?php echo htmlspecialchars($review['name']); ?></strong> -
                                <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                                    <span style="color: gold;">&#9733;</span>
                                <?php endfor; ?>
                                <?php if ($review['comment']): ?>
                                    <p class="mb-0 small"><?php echo htmlspecialchars($review['comment']); ?></p>
                                <?php endif; ?>
                                <small class="text-muted"><?php echo htmlspecialchars(date('F j, Y', strtotime($review['created_at']))); ?></small>
                            </div>
                            <?php endwhile; else: ?>
                            <p class="small text-muted">No reviews yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$reviewQuery->close();
?>
