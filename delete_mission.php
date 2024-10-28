<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'condb.php';
if(!$pdo){
    die("PDO connection failed: " . $pdo->errorInfo());
}

// Get mission ID from query string
$mission_id = $_GET['id'];
if (!is_numeric($mission_id)) {
    echo "Invalid mission ID.";
    exit();
}
// Delete mission for the user
try{
$stmt = $pdo->prepare("DELETE FROM missions WHERE user_id = :user_id AND id = :mission_id ");
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindParam(':mission_id', $mission_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    echo "Mission deleted successfully!";
    
    header('Location: mission.php'); 
     exit();
} else {
    echo "Failed to delete mission!";
}
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}

?>
