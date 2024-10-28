<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'condb.php'; // Ensure this includes a valid PDO connection

// Check if the PDO connection was successful
if (!$pdo) {
    die("PDO connection failed: " . $pdo->errorInfo());
}

// Get task ID from query string
$task_id = $_GET['id'];

// Ensure the task ID is valid
if (!is_numeric($task_id)) {
    echo "Invalid task ID.";
    exit();
}

try {
    // Prepare the DELETE statement
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE user_id = :user_id AND id = :task_id");
    
    // Bind the parameters
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':task_id', $task_id, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        echo "Task deleted successfully!";
        
        header('Location: task.php');  // Redirect back to dashboard
        exit(); // Always exit after header redirection
    } else {
        echo "Failed to delete task!";
    }
} catch (PDOException $e) {
    echo "Error: " . htmlspecialchars($e->getMessage());
}

// No need to close the connection manually, PDO does this automatically when the script ends
?>
