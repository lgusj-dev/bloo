<?php
include('db_connect.php');

// Fetch all users
$stmt = $conn->prepare("SELECT id, password FROM users");
$stmt->execute();
$result = $stmt->get_result();

$updated = 0;
while ($row = $result->fetch_assoc()) {
    // Check if password is not already hashed (hashed passwords are typically long strings)
    if (strlen($row['password']) < 40) {  // Unhashed passwords are typically shorter
        $hashed_password = password_hash($row['password'], PASSWORD_DEFAULT);
        
        // Update the password
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update_stmt->bind_param("si", $hashed_password, $row['id']);
        if ($update_stmt->execute()) {
            $updated++;
        }
        $update_stmt->close();
    }
}

echo "Updated passwords for $updated users.";
$stmt->close();
$conn->close();
?>