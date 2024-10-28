<?php
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'condb.php';
if (!$pdo) {
    die("PDO connection failed: " . $pdo->errorInfo());
}

// Get task ID from query string
$task_id = $_GET['id'];

// Fetch task details for the user
$stmt = $pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id AND id = :task_id");
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->bindParam(':task_id', $task_id, PDO::PARAM_INT);
$stmt->execute();
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    echo "<div class='alert alert-danger'>task not found or access denied!</div>";
    exit();
}

$update_success = false;
$update_error = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $priority = $_POST['priorite'];

    // Update task in the database
    $update_stmt = $pdo->prepare("UPDATE tasks SET name = ?, description = ?, priority = ? WHERE id = ? AND user_id = ?");
    $update_stmt->bindParam(1, $name, PDO::PARAM_STR);
    $update_stmt->bindParam(2, $description, PDO::PARAM_STR);
    $update_stmt->bindParam(3, $priority, PDO::PARAM_STR);
    $update_stmt->bindParam(4, $task_id, PDO::PARAM_INT);
    $update_stmt->bindParam(5, $_SESSION['user_id'], PDO::PARAM_INT);

    if ($update_stmt->execute()) {
        $update_success = true;
        
    } else {
        $update_error = true;
    }

    $update_stmt = null;
}
$stmt = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit task</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Edit task</h2>
    <form method="POST" action="" class="mt-4">
        <div class="mb-3">
            <label for="taskName" class="form-label">task Name:</label>
            <input type="text" name="name" id="taskName" value="<?php echo htmlspecialchars($task['name']); ?>" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="taskDescription" class="form-label">Description:</label>
            <textarea name="description" id="taskDescription" class="form-control" required><?php echo htmlspecialchars($task['description']); ?></textarea>
        </div>
        <div class="form-group">
            <label for="priorite">Priority</label>
            <select name="priorite" class="form-control">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Update task</button>
        <!-- <a href="dashboard.php" class="btn btn-success">Dashboard</a> -->
    </form>
</div>

<!-- Bootstrap Modal -->
<div class="modal fade" id="updateModal" tabindex="-1" aria-labelledby="updateModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="updateModalLabel">task Update</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php if ($update_success): ?>
            <div class="alert alert-success">task updated successfully!</div>
        <?php elseif ($update_error): ?>
            <div class="alert alert-danger">Failed to update task!</div>
        <?php endif; ?>
      </div>
      <div class="modal-footer">
        <a href="task.php"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button></a>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Show the modal if update success or error
    <?php if ($update_success || $update_error): ?>
        var updateModal = new bootstrap.Modal(document.getElementById('updateModal'));
        updateModal.show();
    <?php endif; ?>
</script>
</body>
</html>
