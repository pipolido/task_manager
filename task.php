<?php
include('header.php');
include('condb.php');

if (isset($_POST['create_task'])) {
    $name = $_POST['nom'];
    $description = $_POST['description'];
    $priority = $_POST['priorite'];
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO tasks (name, description, priority, user_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $description, $priority, $user_id]);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit; // Ensure that the script stops executing after redirect
}

// Update the query to join tasks with missions
$tasks = $pdo->query("
    SELECT t.*, m.name AS mission_name 
    FROM tasks t 
    LEFT JOIN missions m ON t.mission_id = m.id 
    WHERE t.user_id = ".$_SESSION['user_id']
)->fetchAll(PDO::FETCH_ASSOC);

?>
<style>
/* Table Styles */
.table {
    width: 100%;
    border-collapse: collapse;
}

.table-bordered {
    border: 1px solid #ddd;
}

.table th, .table td {
    padding: 15px;
    text-align: left;
    border: 1px solid #ddd;
}

/* Priority Color Coding */
.low {
    background-color: green; 
}

.medium {
    background-color: orange;
}

.high {
    background-color: red; 
}

/* Button Styles */
.btn {
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    text-decoration: none;
    color: white;
    cursor: pointer;
    margin-right: 5px; /* Spacing between buttons */
}

.btn-warning {
    background-color: #ffc107; /* Bootstrap warning color */
}

.btn-danger {
    background-color: #dc3545; /* Bootstrap danger color */
}

.btn:hover {
    opacity: 0.8; /* Slightly transparent on hover */
}
</style>
<div class="content">
    <h2>Manage Tasks</h2>

    <form method="post">
        <div class="form-group">
            <label for="nom">Task Name</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Task Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label for="priorite">Priority</label>
            <select name="priorite" class="form-control">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
            </select>
        </div>
        <button type="submit" name="create_task" class="btn btn-primary">Create Task</button>
    </form>

    <h3>Your Tasks</h3>
    <table class="table table-bordered">
    <thead>
        <tr>
            <th>Task Name</th>
            <th>Description</th>
            <th>Priority</th>
            <th>Status</th>
            <th>Mission</th>
            <th>Actions</th> <!-- Added Actions header -->
        </tr>
    </thead>
    <tbody>
        <?php if (empty($tasks)): ?>
            <tr>
                <td colspan="6">No tasks found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($tasks as $task): ?>
                <tr>
                    <td><?= htmlspecialchars($task['name']) ?></td>
                    <td><?= htmlspecialchars($task['description']) ?></td>
                    <td class="<?= strtolower($task['priority']) ?>"><?= htmlspecialchars($task['priority']) ?></td> <!-- Apply priority class -->
                    <td><?= htmlspecialchars($task['status']) ?></td>
                    <td><?= htmlspecialchars($task['mission_name']) ?></td> <!-- Display mission name -->
                    <td>
                        <a href="edit_task.php?id=<?= $task['id'] ?>" class="btn btn-warning">Update</a> <!-- Update button -->
                        <a href="delete_task.php?id=<?= $task['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this task?');">Delete</a> <!-- Delete button -->
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

</div>

<?php include('footer.php'); ?>
                