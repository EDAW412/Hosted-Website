<?php
session_start();

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function showErrorPage($message) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Login Failed</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #fff0f0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .error-box {
                padding: 2rem;
                background-color: #ffe0e0;
                border: 1px solid #ffb3b3;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(255, 0, 0, 0.2);
                text-align: center;
            }
            .error-box button {
                margin-top: 1rem;
                padding: 0.5rem 1rem;
                font-size: 1rem;
                background-color: #ff4d4d;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            .error-box button:hover {
                background-color: #cc0000;
            }
        </style>
    </head>
    <body>
        <div class='error-box'>
            <h2>❌ Login Failed</h2>
            <p>$message</p>
            <form action='login.html'>
                <button type='submit'>Back to Login</button>
            </form>
        </div>
    </body>
    </html>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $input_password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, name, surname, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($input_password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_surname'] = $user['surname'];  

            echo "<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv='refresh' content='3;url=landing%20store%20page.php' />
                <title>Login Successful</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        background-color: #f0f8ff;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                    }
                    .message {
                        padding: 2rem;
                        background-color: #e0ffe0;
                        border: 1px solid #b2ffb2;
                        border-radius: 8px;
                        box-shadow: 0 0 10px rgba(0, 128, 0, 0.2);
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <div class='message'>
                    <h2>✅ Login successful!</h2>
                    <p>Welcome, " . htmlspecialchars($user['name']) . ".</p>
                    <p>Redirecting to the store in 3 seconds...</p>
                </div>
            </body>
            </html>";
            exit();
        } else {
            showErrorPage("Invalid password. Please try again.");
        }
    } else {
        showErrorPage("No user found with that email address.");
    }

    $stmt->close();
}

$conn->close();
?>
