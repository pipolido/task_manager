<?php 
include('header.php');
include('condb.php'); 

$missions = [];
$mission_query = "SELECT * FROM missions where user_id = ?";
$stmt_missions = $pdo->prepare($mission_query);
$stmt_missions->execute([$_SESSION['user_id']]);
$missions = $stmt_missions->fetchAll(PDO::FETCH_ASSOC);

$tasks = [];
$task_query = "SELECT * FROM tasks where user_id = ?";
$stmt_tasks = $pdo->prepare($task_query);
$stmt_tasks->execute([$_SESSION['user_id']]);
$tasks = $stmt_tasks->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mission_id = $_POST['mission'];
    $selected_tasks = isset($_POST['missions']) ? $_POST['missions'] : [];

    if (!empty($selected_tasks)) {
        foreach ($selected_tasks as $task_id) {
            // Update each selected task with the chosen mission ID
            $update_query = "UPDATE tasks SET mission_id = :mission_id WHERE id = :task_id";
            $stmt_update = $pdo->prepare($update_query);
            $stmt_update->bindParam(':mission_id', $mission_id, PDO::PARAM_INT);
            $stmt_update->bindParam(':task_id', $task_id, PDO::PARAM_INT);
            $stmt_update->execute();
        }

        echo '<div class="alert alert-success">Tasks linked to mission successfully!</div>';
    } else {
        echo '<div class="alert alert-warning">Please select at least one task.</div>';
    }
}
?>
<style>
    /* Custom CSS to reduce left space */
    .content-wrapper {
        padding-left: 0;
        justify-content: center;
        align-items: center; /* Remove left padding */
    }
    .container-fluid {
        padding-left: 15px; /* Adjust this value as needed */
        padding-right: 15px; 
        justify-content: center;
        align-items: center;/* Adjust this value as needed */
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1>Link Tasks to Mission</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Select Mission and Tasks</h3>
                </div>
                <div class="card-body">
                    <form method="post">
                        <div class="form-group">
                            <label for="mission">Select Mission:</label>
                            <select name="mission" id="mission" class="form-control">
                                <?php 
                                if (!empty($missions)) {
                                    foreach ($missions as $mission) {
                                        echo '<option value="' . $mission['id'] . '">' . htmlspecialchars($mission['name']) . '</option>';
                                    }
                                } else {
                                    echo '<option>No missions found</option>';
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Select Tasks:</label><br>
                            <?php 
                            if (!empty($tasks)) {
                                foreach ($tasks as $task) {
                                    echo '<div class="form-check">';
                                    echo '<input class="form-check-input" type="checkbox" name="missions[]" value="' . $task['id'] . '" id="task_' . $task['id'] . '">';
                                    echo '<label class="form-check-label" for="task_' . $task['id'] . '">' . htmlspecialchars($task['name']) . '</label>';
                                    echo '</div>';
                                }
                            } else {
                                echo 'No tasks available';
                            }
                            ?>
                        </div>

                        <button type="submit" class="btn btn-primary">Link Tasks</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include('footer.php'); ?>
