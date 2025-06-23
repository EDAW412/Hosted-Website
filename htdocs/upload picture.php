<?php
session_start();
$user_id = $_SESSION['user_id'];

if(isset($_FILES['profile_image'])){
    $target_dir = "uploads/profiles/";
    $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
    if($check !== false && in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])){
        if(move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)){
            $conn = new mysqli("localhost", "root", "", "laezel_marketplace");
            $stmt = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            $stmt->bind_param("si", $target_file, $user_id);
            $stmt->execute();
            $stmt->close();
            $conn->close();
            echo "Profile picture updated successfully.";
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Invalid file type.";
    }
}
?>
