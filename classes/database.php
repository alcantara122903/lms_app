<?php
class database {
    function opencon(): PDO {
        try {
            $pdo = new PDO(
                dsn: 'mysql:host=localhost;dbname=dbs_db',
                username: 'root',
                password: '',
                options: [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Enable exceptions for errors
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC // Set default fetch mode
                ]
            );
            // Test query to verify connection
            $pdo->query("SELECT 1");
            return $pdo;
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public function signupUser($firstname, $lastname, $birthday, $email, $sex, $phone, $username, $password, $profile_picture_path) {
        try {
            $sql = "INSERT INTO Users (user_FN, user_LN, user_birthday, user_email, user_sex, user_phone, user_password, user_type) 
                    VALUES (:firstname, :lastname, :birthday, :email, :sex, :phone, :password, 1)";
            $stmt = $this->opencon()->prepare($sql);
            $stmt->execute([
                ':firstname' => $firstname,
                ':lastname' => $lastname,
                ':birthday' => $birthday,
                ':email' => $email,
                ':sex' => $sex,
                ':phone' => $phone,
                ':password' => $password
            ]);
            $userID = $this->opencon()->lastInsertId();

            // Insert profile picture
            $sqlPic = "INSERT INTO Users_Pictures (user_id, user_pic_url) VALUES (:user_id, :profile_picture_path)";
            $stmtPic = $this->opencon()->prepare($sqlPic);
            $stmtPic->execute([
                ':user_id' => $userID,
                ':profile_picture_path' => $profile_picture_path
            ]);

            return $userID; // Return the user ID on success
        } catch (PDOException $e) {
            error_log("Signup Error: " . $e->getMessage()); // Log the exact error
            return false; // Return false only on failure
        }
    }

    public function insertAddress($userID, $street, $barangay, $city, $province) {
        try {
            // Insert into Address table
            $sqlAddress = "INSERT INTO Address (ba_street, ba_barangay, ba_city, ba_province) 
                           VALUES (:street, :barangay, :city, :province)";
            $stmtAddress = $this->opencon()->prepare($sqlAddress);
            $stmtAddress->execute([
                ':street' => $street,
                ':barangay' => $barangay,
                ':city' => $city,
                ':province' => $province
            ]);
            $addressID = $this->opencon()->lastInsertId();

            // Link address to user in Users_Address table
            $sqlUserAddress = "INSERT INTO Users_Address (user_id, address_id) VALUES (:user_id, :address_id)";
            $stmtUserAddress = $this->opencon()->prepare($sqlUserAddress);
            $stmtUserAddress->execute([
                ':user_id' => $userID,
                ':address_id' => $addressID
            ]);

            return true;
        } catch (PDOException $e) {
            error_log("Address Insertion Error: " . $e->getMessage());
            return false;
        }
    }
}
?>