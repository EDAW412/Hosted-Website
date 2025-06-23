<?php
session_start();

if (!isset($_SESSION['staff_role']) || !in_array(strtolower($_SESSION['staff_role']), ['admin', 'manager'])) {
    header('Location: staff login.php');
    exit();
}

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $delete_user_id = intval($_POST['delete_user_id']);

    if ($delete_user_id !== intval($_SESSION['staff_id'])) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param('i', $delete_user_id);
        $stmt->execute();
        $stmt->close();

        header('Location: manage website users.php?message=User+deleted+successfully');
        exit();
    } else {
        $error = "You cannot delete your own profile.";
    }
}

$result = $conn->query("SELECT id, name, email FROM users ORDER BY id ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1>Manage Users</h1>

    <?php if (isset($_GET['message'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <table class="table table-striped bg-white shadow-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($user = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                    <?php if ($user['id'] !== intval($_SESSION['staff_id'])): ?>
                    <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>" />
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    <?php else: ?>
                        <em>Cannot delete self</em>
                    <?php endif; ?>
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
