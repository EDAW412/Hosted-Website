<?php
session_start();
if (!isset($_SESSION['staff_role']) || !in_array($_SESSION['staff_role'], ['admin', 'manager'])) {
    header('Location: staff login.php');
    exit();
}

$currentUserRole = $_SESSION['staff_role'];
$currentUserId = $_SESSION['staff_id'];

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$error = '';
$success = '';
$staff_id = intval($_GET['id'] ?? 0);

if ($staff_id <= 0) {
    header('Location: manage users.php');
    exit();
}

$stmt = $conn->prepare("SELECT username, role FROM staff_users WHERE staff_id = ?");
$stmt->bind_param('i', $staff_id);
$stmt->execute();
$stmt->bind_result($username, $role);
if (!$stmt->fetch()) {
    $stmt->close();
    header('Location: manage users.php');
    exit();
}
$stmt->close();

if ($currentUserRole === 'admin' && in_array($role, ['admin', 'manager']) && $staff_id != $currentUserId) {
    $roleChangeAllowed = false;
} else {
    $roleChangeAllowed = true;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = trim($_POST['username']);
    $new_role = $_POST['role'];
    $new_password = $_POST['password'];

    if (empty($new_username) || empty($new_role)) {
        $error = "Username and role cannot be empty.";
    } else {
        if ($new_username !== $username) {
            $stmt = $conn->prepare("SELECT staff_id FROM staff_users WHERE username = ? AND staff_id != ?");
            $stmt->bind_param('si', $new_username, $staff_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error = "Username already taken.";
            }
            $stmt->close();
        }

        if (!$error) {
            if ($currentUserRole === 'admin' && in_array($role, ['admin', 'manager']) && $staff_id != $currentUserId) {
                $new_role = $role; // Force original role, disallow change
            }

            if (!empty($new_password)) {
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE staff_users SET username = ?, role = ?, password_hash = ? WHERE staff_id = ?");
                $stmt->bind_param('sssi', $new_username, $new_role, $password_hash, $staff_id);
            } else {
                $stmt = $conn->prepare("UPDATE staff_users SET username = ?, role = ? WHERE staff_id = ?");
                $stmt->bind_param('ssi', $new_username, $new_role, $staff_id);
            }
            if ($stmt->execute()) {
                $success = "Staff member updated successfully.";
                $username = $new_username;
                $role = $new_role;
                if ($staff_id == $currentUserId) {
                    if ($currentUserRole === 'admin' && in_array($role, ['admin', 'manager']) && $staff_id != $currentUserId) {
                        $roleChangeAllowed = false;
                    } else {
                        $roleChangeAllowed = true;
                    }
                }
            } else {
                $error = "Failed to update staff member.";
            }
            $stmt->close();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Edit Staff Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1>Edit Staff Member</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($username); ?>" required />
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role:</label>
            <?php if ($roleChangeAllowed): ?>
                <select name="role" id="role" class="form-select" required>
                    <option value="customer_support" <?php if ($role == 'customer_support') echo 'selected'; ?>>Customer Support</option>
                    <option value="manager" <?php if ($role == 'manager') echo 'selected'; ?>>Manager</option>
                    <option value="admin" <?php if ($role == 'admin') echo 'selected'; ?>>Admin</option>
                </select>
            <?php else: ?>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $role))); ?>" disabled />
                <input type="hidden" name="role" value="<?php echo htmlspecialchars($role); ?>" />
                <div class="form-text text-danger">You cannot change the role of this user.</div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">New Password (leave blank to keep current):</label>
            <input type="password" id="password" name="password" class="form-control" />
        </div>

        <button type="submit" class="btn btn-primary">Save Changes</button>
        <a href="manage users.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
</body>
</html>
