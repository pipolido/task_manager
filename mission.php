<?php
include('header.php');
include('condb.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // Redirect to login page if the user is not logged in
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

if (isset($_POST['create_mission'])) {
    $nom = trim($_POST['nom']);
    $description = !empty($_POST['description']) ? trim($_POST['description']) : null;

    if (!empty($nom)) { // Check if the mission name is not empty
        try {
            // Insert new mission
            $stmt = $pdo->prepare("INSERT INTO missions (name, description, user_id) VALUES (?, ?, ?)");
            $stmt->execute([$nom, $description, $user_id]);

            // Redirect to the same page to prevent form resubmission
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Mission name is required.</div>";
    }
}

// Fetch all missions of the logged-in user
$stmt = $pdo->prepare("SELECT * FROM missions WHERE user_id = ?");
$stmt->execute([$user_id]);
$missions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    <h2>Manage Missions</h2>

    <form method="post">
        <div class="form-group">
            <label for="nom">Mission Name</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="description">Mission Description</label>
            <textarea name="description" class="form-control"></textarea>
        </div>
        <button type="submit" name="create_mission" class="btn btn-primary">Create Mission</button>
    </form>

    <h3>Your Missions</h3>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mission Name</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($missions)): ?>
                <tr>
                    <td colspan="2">No missions found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($missions as $mission): ?>
                    <tr>
                        <td><?= htmlspecialchars($mission['name']) ?></td>
                        <td><?= htmlspecialchars($mission['description']) ?></td>
                   
                    <td>
                        <a href="edit_mission.php?id=<?= $mission['id'] ?>" class="btn btn-warning">Update</a> <!-- Update button -->
                        <a href="delete_mission.php?id=<?= $mission['id'] ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this mission?');">Delete</a> <!-- Delete button -->
                    </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include('footer.php'); ?>
