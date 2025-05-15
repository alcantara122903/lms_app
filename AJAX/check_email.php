<?php
require_once '../classes/database.php';

header('Content-Type: application/json'); // Ensure JSON response

if (isset($_POST['email'])) {
    $email = $_POST['email'];
    $con = new database();

    try {
        $db = $con->opencon();
        $query = $db->prepare("SELECT user_email FROM Users WHERE user_email = ?");
        $query->execute([$email]);
        $existingUser = $query->fetch();

        if ($existingUser) {
            echo json_encode(['exists' => true]); // Email exists
        } else {
            echo json_encode(['exists' => false]); // Email does not exist
        }
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        echo json_encode(['error' => 'Database query failed']);
    }
} else {
    error_log("Invalid Request: Email not provided");
    echo json_encode(['error' => 'Invalid request']);
}
?>
