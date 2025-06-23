<?php
session_start();

if (!isset($_SESSION['staff_id'])) {
    header('Location: staff login.php');
    exit();
}

$staff_username = $_SESSION['staff_username'];
$staff_role = $_SESSION['staff_role'];

function prettyRole($role) {
    return ucwords(str_replace('_', ' ', $role));
}
?>
<style>
  a.btn {
    background-color: #ff6600;
    border-color: #ff6600;
    color: white;
  }

  a.btn:hover,
  a.btn:focus {
    background-color: #cc5200; /* Slightly darker on hover */
    border-color: #cc5200;
    color: white;
  }

  a.btn-danger {
    background-color: #dc3545; /* Bootstrap default red */
    border-color: #dc3545;
    color: white;
  }

  a.btn-danger:hover,
  a.btn-danger:focus {
    background-color: #bb2d3b;
    border-color: #bb2d3b;
    color: white;
  }
</style>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="card p-4 shadow-sm">
        <h2 class="mb-4">Welcome, <?php echo htmlspecialchars($staff_username); ?>!</h2>
        <p>Your role: <strong><?php echo htmlspecialchars(prettyRole($staff_role)); ?></strong></p>

        <hr>

        <h4>Dashboard Actions</h4>

        <?php if (isset($_SESSION['staff_role']) && $_SESSION['staff_role'] === 'admin'): ?>
            <div class="mb-3">
                <a href="admin manage products.php" class="btn btn-primary d-block w-100">Manage Customer Products</a>
            </div>
        <?php endif; ?>

        <?php if (in_array(strtolower($staff_role), ['admin', 'manager'])): ?>
            <div class="mb-3">
                <a href="manage users.php" class="btn btn-primary d-block w-100">Manage staff</a>
            </div>
       
    

    <div class="mb-3">
        <a href="manage website users.php" class="btn btn-primary d-block w-100">Manage Users</a>
    </div>
<?php endif; ?>


        <div class="mb-3">
    <a href="view reports.php" class="btn btn-secondary d-block w-100">View Reports</a>
</div>
<div class="mb-3">
    <a href="view user feedback.php" class="btn btn-primary d-block w-100">View User Feedback and Issues</a>
</div>



        <div>
            <a href="staff logout.php" class="btn btn-danger d-block w-100">Logout</a>
        </div>
    </div>
</div>

</body>
</html>
