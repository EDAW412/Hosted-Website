<?php
$error = isset($_GET['error']) ? htmlspecialchars($_GET['error']) : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="images/Laezel_cat.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Laezel Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .signup-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .signup-card {
            background-color: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 700px;
        }
        .form-label {
            font-weight: 600;
        }
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
</head>
<body>
    <div class="signup-container">
        <div class="signup-card">
            <h2 class="mb-4 text-center">Create Your Account</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            <form action="register.php" method="POST">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="col-md-6">
                        <label for="surname" class="form-label">Surname</label>
                        <input type="text" class="form-control" id="surname" name="surname" required>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-6">
                        <label for="id_number" class="form-label">ID Number</label>
                        <input type="text" class="form-control" id="id_number" name="id_number" required>
                    </div>
                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="col-md-6">
                        <label for="address" class="form-label">Address</label>
                        <input type="text" class="form-control" id="address" name="address" required>
                    </div>
                    <div class="col-md-6">
                        <label for="province" class="form-label">Province</label>
                        <select class="form-select" id="province" name="province" required>
                            <option value="" disabled selected>Select your province</option>
                            <option value="Gauteng">Gauteng</option>
                            <option value="Western Cape">Western Cape</option>
                            <option value="KwaZulu-Natal">KwaZulu-Natal</option>
                            <option value="Eastern Cape">Eastern Cape</option>
                            <option value="Free State">Free State</option>
                            <option value="Limpopo">Limpopo</option>
                            <option value="Mpumalanga">Mpumalanga</option>
                            <option value="Northern Cape">Northern Cape</option>
                            <option value="North West">North West</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required pattern="(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{8,}" title="Password must be at least 8 characters, contain a capital letter and a special character.">
                    </div>
                </div>
                <div class="mt-4 text-center">
                    <button type="submit" class="btn btn-orange px-5">Sign Up</button>
                </div>
                <a href="index.html" class="btn btn-secondary mt-3">Back to Home</a>
            </form>
        </div>
    </div>
</body>
</html>