<?php

require_once __DIR__ . "/../includes/auth.php";
require_role('admin'); // Only admins can delete
require_once __DIR__ . "/../db.php";


if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    
    $user_id_to_delete = (int)$_GET['id'];
    
    
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id_to_delete) {
        
        echo "<script>
                alert('❌ Security Warning: You cannot delete your own account while logged in.');
                window.location.href = '../users.php';
              </script>";
        exit;
    }

    
    $stmt = $mysqli->prepare("DELETE FROM users WHERE id = ?");
    
    if ($stmt) {
        $stmt->bind_param("i", $user_id_to_delete);
        
        if ($stmt->execute()) {
           
            $stmt->close();
            header("Location: ../users.php?msg=User deleted successfully");
            exit;
        } else {
            
            echo "Error deleting record: " . $mysqli->error;
        }
    } else {
        echo "Database preparation error.";
    }

} else {
   
    header("Location: ../users.php");
    exit;
}
?>
