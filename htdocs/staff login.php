<?php
session_start();

$error = ''; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare('SELECT staff_id, password_hash, role FROM staff_users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($staff_id, $stored_password, $role);
        $stmt->fetch();

        if (password_verify($password, $stored_password)) {
            $_SESSION['staff_id'] = $staff_id;
            $_SESSION['staff_username'] = $username;
            $_SESSION['staff_role'] = $role;

            header('Location: staff landing page.php');
            exit();
        } else {
            $error = 'Invalid password';
        }
    } else {
        $error = 'No such user';
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Staff Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-orange: #ff6600;
      --secondary-orange: #ffa366;
    }

    body {
      background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .login-card {
      background-color: #fff;
      padding: 2rem;
      border-radius: 1rem;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      margin: 4rem auto;
      animation: fadeIn 0.8s ease forwards;
      opacity: 0;
    }

    h2 {
      font-weight: 700;
      font-size: 1.6rem;
      color: #333;
    }

    .form-label {
      font-weight: 600;
      color: #333;
    }

    .btn-primary {
      background-color: var(--primary-orange);
      border: none;
      transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #e65c00;
    }

    .alert {
      font-size: 0.95rem;
    }

    a {
      color: var(--primary-orange);
      text-decoration: none;
      transition: color 0.3s ease;
    }

    a:hover {
      color: #e65c00;
    }

    @keyframes fadeIn {
      to {
        opacity: 1;
      }
    }
  </style>
</head>

<body>
  <div class="container">
    <div class="login-card">
      <h2 class="mb-4 text-center">Staff Login</h2>

      <?php if (!empty($error)): ?>
      <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="post">
        <div class="mb-3">
          <label for="username" class="form-label">Username:</label>
          <input type="text" class="form-control" name="username" id="username" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Password:</label>
          <input type="password" class="form-control" name="password" id="password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-3">Login</button>
      </form>

      <div class="mt-3 text-center">
        <a href="index.html">Return to Home</a>
      </div>
    </div>
  </div>
</body>

</html>
