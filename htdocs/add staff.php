<?php
session_start();
if (!isset($_SESSION['staff_role']) || !in_array($_SESSION['staff_role'], ['admin', 'manager'])) {
    header('Location: staff login.php');
    exit();
}

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    if (empty($username) || empty($password) || empty($role)) {
        $error = "Please fill all fields.";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT staff_id FROM staff_users WHERE username = ?");
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username already exists.";
        } else {
            // Hash password (recommended)
            $password_hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO staff_users (username, password_hash, role, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->bind_param('sss', $username, $password_hash, $role);
            if ($stmt->execute()) {
                $success = "New staff member added successfully.";
            } else {
                $error = "Error adding staff member.";
            }
        }
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Add Staff Member</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1>Add Staff Member</h1>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post" action="">
        <div class="mb-3">
            <label for="username" class="form-label">Username:</label>
            <input type="text" id="username" name="username" class="form-control" required />
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password:</label>
            <input type="password" id="password" name="password" class="form-control" required />
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role:</label>
            <select name="role" id="role" class="form-select" required>
                <option value="">Select role</option>
                <option value="customer_support">Customer Support</option>
                <option value="manager">Manager</option>
                <option value="admin">Admin</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Add Staff Member</button>
        <a href="manage users.php" class="btn btn-secondary ms-2">Cancel</a>
    </form>
</div>
</body>
</html>
