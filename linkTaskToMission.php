<?php
include('header.php');
include('condb.php');


$missions = $pdo->prepare("SELECT * FROM missions WHERE user_id = ?");
$missions->execute([$_SESSION['user_id']]);
$missions = $missions->fetchAll(PDO::FETCH_ASSOC);

// Fetch only the tasks that belong to the logged-in user
$tasks = $pdo->prepare("SELECT * FROM tasks WHERE user_id = ?");
$tasks->execute([$_SESSION['user_id']]);
$tasks = $tasks->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['submit'])) {
    $task_id = $_POST['task_id'];
    $mission_id = $_POST['mission_id'];
    
    // Update the task with the mission
    $stmt = $pdo->prepare("UPDATE tasks SET mission_id = ? WHERE id = ?");
    $stmt->execute([$mission_id, $task_id]);

    echo "Task linked to mission successfully!";
}

?>

<div class="content">
    <h2>Link Task to Mission</h2>
    <form method="post">
        <div class="form-group">
            <label for="task_id">Select Task</label>
            <select name="task_id" class="form-control">
                <?php foreach ($tasks as $task): ?>
                    <option value="<?= $task['id'] ?>"><?= $task['nom'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="mission_id">Select Mission</label>
            <select name="mission_id" class="form-control">
                <?php foreach ($missions as $mission): ?>
                    <option value="<?= $mission['id'] ?>"><?= $mission['nom'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Link Task</button>
    </form>
</div>

<?php include('footer.php'); ?>
