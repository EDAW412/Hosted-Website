<?php
session_start();

if (!isset($_SESSION['staff_role']) || !in_array(strtolower($_SESSION['staff_role']), ['admin', 'manager'])) {
    header('Location: staff login.php');
    exit();
}

$staff_role = strtolower($_SESSION['staff_role']);

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($staff_role === 'admin' && isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM feedback WHERE id = ?");
    $stmt->bind_param('i', $delete_id);
    $stmt->execute();
    $stmt->close();

    header('Location: view user feedback.php?message=Feedback+deleted+successfully');
    exit();
}

$sql = "SELECT f.id, f.user_id, f.feedback_text, f.created_at, u.name AS user_name, u.email AS user_email
        FROM feedback f
        LEFT JOIN users u ON f.user_id = u.id
        ORDER BY f.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>User Feedback and Issues</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1>User Feedback and Issues</h1>

    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows === 0): ?>
        <p>No feedback found.</p>
    <?php else: ?>
        <table class="table table-striped bg-white shadow-sm">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Feedback</th>
                    <th>Submitted At</th>
                    <?php if ($staff_role === 'admin'): ?>
                        <th>Actions</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($feedback = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $feedback['id']; ?></td>
                    <td><?php echo htmlspecialchars($feedback['user_name'] ?? 'Guest'); ?></td>
                    <td><?php echo htmlspecialchars($feedback['user_email'] ?? ''); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($feedback['feedback_text'])); ?></td>
                    <td><?php echo htmlspecialchars($feedback['created_at']); ?></td>
                    <?php if ($staff_role === 'admin'): ?>
                        <td>
                            <a href="view user feedback.php?delete=<?php echo $feedback['id']; ?>" 
                               class="btn btn-danger btn-sm" 
                               onclick="return confirm('Are you sure you want to delete this feedback?');">
                               Delete
                            </a>
                        </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <a href="staff landing page.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

</body>
</html>

<?php
$conn->close();
?>
