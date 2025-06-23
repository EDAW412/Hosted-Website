<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="icon" href="images/Laezel_cat.png" type="image/png">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Successful | Laezel Marketplace</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .success-card {
            background-color: white;
            padding: 3rem;
            border-radius: 1rem;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            text-align: center;
            width: 100%;
            max-width: 500px;
        }
        .success-card h2 {
            color: #ff6600;
            margin-bottom: 1.5rem;
        }
        .success-card p {
            color: #34495e;
            font-size: 1.1rem;
            margin-bottom: 2rem;
        }
        .btn-orange {
            background-color: #ff6600;
            border-color: #ff6600;
            color: #fff;
            font-weight: 500;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            transition: background-color 0.3s ease;
        }
        .btn-orange:hover,
        .btn-orange:focus {
            background-color: #e65c00;
            border-color: #e65c00;
            color: #fff;
        }
        @media (max-width: 768px) {
            .success-card {
                padding: 2rem;
            }
            .success-card h2 {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <div class="success-card">
        <h2>✅ Registration Successful!</h2>
        <p>You can now log in to your account.</p>
        <a href="login.html" class="btn btn-orange">Go to Login</a>
    </div>
</body>
</html>