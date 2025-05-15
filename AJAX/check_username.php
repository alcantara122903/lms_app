<?php
require_once '../classes/database.php';

header('Content-Type: application/json'); // Ensure JSON response

if (isset($_POST['username'])) {
    $username = $_POST['username'];
    $con = new database();

    try {
        $db = $con->opencon();
        $query = $db->prepare("SELECT user_FN FROM Users WHERE user_FN = ?");
        $query->execute([$username]);
        $existingUser = $query->fetch();

        if ($existingUser) {
            echo json_encode(['exists' => true]); 
        } else {
            echo json_encode(['exists' => false]); 
        }
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        echo json_encode(['error' => 'Database query failed']);
    }
} else {
    error_log("Invalid Request: Username not provided");
    echo json_encode(['error' => 'Invalid request']);
}
?>
