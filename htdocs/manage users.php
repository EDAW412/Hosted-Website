<?php
session_start();

function prettyRole($role) {
    return ucwords(str_replace('_', ' ', $role));
}

if (!isset($_SESSION['staff_role']) || !in_array($_SESSION['staff_role'], ['admin', 'manager'])) {
    header('Location: staff login.php');
    exit();
}

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    if ($delete_id == $_SESSION['staff_id']) {
        $error = "You cannot delete your own account.";
    } else {
        $stmt = $conn->prepare("SELECT role FROM staff_users WHERE staff_id = ?");
        $stmt->bind_param('i', $delete_id);
        $stmt->execute();
        $stmt->bind_result($delete_role);
        if ($stmt->fetch()) {
            $stmt->close();

            if ($_SESSION['staff_role'] !== 'admin') {
                $error = "Only admins can delete users.";
            } else if (in_array($delete_role, ['admin', 'manager'])) {
                $error = "You cannot delete admin or manager accounts.";
            } else {
                $stmt = $conn->prepare("DELETE FROM staff_users WHERE staff_id = ?");
                $stmt->bind_param('i', $delete_id);
                $stmt->execute();
                $stmt->close();
                header('Location: manage users.php');
                exit();
            }
        } else {
            $stmt->close();
            $error = "User not found.";
        }
    }
}


$result = $conn->query("SELECT staff_id, username, role, created_at FROM staff_users ORDER BY staff_id ASC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Manage Staff Members</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1>Manage Staff Members</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <a href="add staff.php" class="btn btn-success mb-3">Add New Staff Member</a>
    <a href="staff landing page.php" class="btn btn-secondary mb-3 ms-2">Back to Dashboard</a>

    <table class="table table-striped bg-white shadow-sm">
        <thead>
            <tr>
                <th>ID</th>
                <th>Username</th>
                <th>Role</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
<?php while ($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['staff_id']; ?></td>
        <td><?php echo htmlspecialchars($row['username']); ?></td>
        <td><?php echo htmlspecialchars(prettyRole($row['role'])); ?></td>
        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
        <td>
            <a href="edit staff.php?id=<?php echo $row['staff_id']; ?>" class="btn btn-primary btn-sm">Edit</a>
            <?php if ($row['staff_id'] != $_SESSION['staff_id']): ?>
                <a href="manage users.php?delete=<?php echo $row['staff_id']; ?>" 
                   class="btn btn-danger btn-sm" 
                   onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
            <?php else: ?>
                <button class="btn btn-secondary btn-sm" disabled>Delete</button>
            <?php endif; ?>
        </td>
    </tr>
<?php endwhile; ?>
</tbody>
    </table>
</div>
</body>
</html>
