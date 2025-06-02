<?php
session_start();
include_once 'db_connect.php';

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// Handle file upload
if(isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
    $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
    $filename = $_FILES["profile_picture"]["name"];
    $filetype = $_FILES["profile_picture"]["type"];
    $filesize = $_FILES["profile_picture"]["size"];

    // Verify file extension
    $ext = pathinfo($filename, PATHINFO_EXTENSION);
    if(!array_key_exists($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Error: Please select a valid file format (JPG, JPEG, PNG, GIF).']);
        exit();
    }

    // Verify file size - 5MB maximum
    $maxsize = 5 * 1024 * 1024;
    if($filesize > $maxsize) {
        echo json_encode(['success' => false, 'message' => 'Error: File size is larger than the allowed limit (5MB).']);
        exit();
    }

    // Verify MIME type of the file
    if(in_array($filetype, $allowed)) {
        // Create unique filename
        $new_filename = uniqid('profile_') . '.' . $ext;
        $upload_path = "uploads/" . $new_filename;
        
        // Move the file to the uploads directory
        if(move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $upload_path)) {
            // Get current profile picture
            $sql = "SELECT profile_picture FROM users WHERE id = $user_id";
            $result = $conn->query($sql);
            $user = $result->fetch_assoc();
            
            // Delete old profile picture if it's not the default
            if($user['profile_picture'] != 'uploads/default-avatar.png' && file_exists($user['profile_picture'])) {
                unlink($user['profile_picture']);
            }
            
            // Update user profile picture in database
            $update_sql = "UPDATE users SET profile_picture = '$upload_path' WHERE id = $user_id";
            
            if($conn->query($update_sql) === TRUE) {
                echo json_encode(['success' => true, 'message' => 'Profile picture updated successfully', 'file_path' => $upload_path]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error updating database: ' . $conn->error]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error: There was a problem uploading your file. Please try again.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Error: There was a problem with the file type. Please try again.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Error: No file uploaded or file upload error.']);
}
?>
