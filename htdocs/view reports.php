<?php
session_start();

if (!isset($_SESSION['staff_id'])) {
    header('Location: staff login.php');
    exit();
}

$staff_username = $_SESSION['staff_username'];
$staff_role = $_SESSION['staff_role'];

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
SELECT r.id AS report_id, r.reason, r.comment, r.created_at, p.name AS product_name, u.name AS reporting_user, u.email AS reporting_email, r.product_id
FROM product_reports r
JOIN products p ON r.product_id = p.id
JOIN users u ON r.user_id = u.id
ORDER BY r.id DESC
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Product Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1>Product Reports</h1>

    <?php if ($result && $result->num_rows > 0): ?>
        <table class="table table-striped shadow-sm mt-4 bg-white">
<thead>
  <tr>
    <th>ID</th>
    <th>Reason</th>
    <th>Comment</th>
    <th>Product</th>
    <th>Reporting User</th>
    <th>Email</th>
    <th>Reported At</th>
    <th>Actions</th>
  </tr>
</thead>
<tbody>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
  <td><?php echo $row['report_id']; ?></td>
  <td><?php echo htmlspecialchars($row['reason']); ?></td>
  <td><?php echo htmlspecialchars($row['comment']); ?></td>
  <td><?php echo htmlspecialchars($row['product_name']); ?></td>
  <td><?php echo htmlspecialchars($row['reporting_user']); ?></td>
  <td><?php echo htmlspecialchars($row['reporting_email']); ?></td>
  <td><?php echo htmlspecialchars($row['created_at']); ?></td>
  <td>
    <a href="view item reports.php?id=<?php echo urlencode($row['product_id']); ?>" class="btn btn-primary btn-sm">View Product</a>
  </td>
</tr>
<?php endwhile; ?>
</tbody>
        </table>
    <?php else: ?>
        <p>No reports found.</p>
    <?php endif; ?>

    <a href="staff landing page.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
