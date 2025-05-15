<?php
function handleFileUpload($file)
{
    $target_dir = "uploads/";
    $original_file_name = basename($file["name"]);
    $imageFileType = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));

    // Check if the file path is not empty
    if (empty($file["tmp_name"])) {
        error_log("File Upload Error: File path is empty.");
        return false;
    }

    // Check if the file is an image
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        error_log("File Upload Error: File is not an image.");
        return false;
    }

    // Check file size (limit to 500KB)
    if ($file["size"] > 500000) {
        error_log("File Upload Error: File size exceeds the limit of 500KB. File size: " . $file["size"]);
        return false;
    }

    // Allow certain file formats
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowed_types)) {
        error_log("File Upload Error: Unsupported file type: " . $imageFileType);
        return false;
    }

    // Generate a unique file name
    $new_file_name = pathinfo($original_file_name, PATHINFO_FILENAME) . '_' . time() . '.' . $imageFileType;
    $target_file = $target_dir . $new_file_name;

    // Check if the uploads directory exists
    if (!is_dir($target_dir)) {
        error_log("File Upload Error: Uploads directory does not exist.");
        return false;
    }

    // Check if the uploads directory is writable
    if (!is_writable($target_dir)) {
        error_log("File Upload Error: Uploads directory is not writable.");
        return false;
    }

    // Move the file to the target directory
    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        return $target_file;
    } else {
        error_log("File Upload Error: Failed to move uploaded file.");
        return false;
    }
}