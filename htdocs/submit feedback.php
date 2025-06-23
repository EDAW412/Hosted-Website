<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$conn = new mysqli("sql110.infinityfree.com", "if0_39218282", "WaddlePaddle412", "if0_39218282_laezel_marketplace");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["feedback"])) {
    $user_id = $_SESSION['user_id'];
    $feedback = trim($_POST['feedback']);

    if (empty($feedback)) {
        $message = "Please enter your feedback.";
    } else {
        $stmt = $conn->prepare("INSERT INTO feedback (user_id, feedback_text) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $feedback);

        if ($stmt->execute()) {
            echo "<p>Thank you for your feedback! Redirecting you back to the store...</p>";
            echo "<script>
                    setTimeout(function() {
                        window.location.href = 'landing store page.php';
                    }, 3000); // Redirect after 3 seconds
                  </script>";
            exit;
        } else {
            $message = "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <link rel="icon" href="images/Laezel_cat.png" type="image/png">

    <title>Submit Feedback</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        form {
            margin-top: 20px;
        }
        textarea {
            width: 100%;
            height: 100px;
        }
        .error {
            color: red;
        }
    </style>
</head>
<body>

<h2>Submit Your Feedback</h2>

<?php
if (isset($message)) {
    echo "<p class='error'>$message</p>";
}
?>

<form method="POST" action="submit feedback.php">
    <label for="feedback">Your Feedback:</label><br>
    <textarea name="feedback" id="feedback" required></textarea><br><br>
    <input type="submit" value="Submit Feedback">
</form>

</body>
</html>
