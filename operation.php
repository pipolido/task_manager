<?php
include('header.php');
include('condb.php');




// Handle operation updates
if (isset($_POST['update_operation'])) {
    $id = $_POST['id'];
    $operation = $_POST['operation'];

    // Update operation in the database
    $stmt = $pdo->prepare("UPDATE operations SET operation = ? WHERE id = ?");
    $stmt->execute([$operation, $id]);

    // Log the update operation
    $logOperation = 'Operation updated: ' . $operation;
    $stmt = $pdo->prepare("INSERT INTO operations (user_id, operation, operation_time) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $logOperation, date('Y-m-d H:i:s')]);

    echo "<div class='alert alert-success'>Operation updated successfully!</div>";
}

// Handle operation deletion
if (isset($_POST['delete_operation'])) {
    $id = $_POST['id'];

    // Delete operation from the database
    $stmt = $pdo->prepare("DELETE FROM operations WHERE id = ?");
    $stmt->execute([$id]);

    // Log the deletion operation
    $logOperation = 'Operation deleted: ID ' . $id;
    $stmt = $pdo->prepare("INSERT INTO operations (user_id, operation, operation_time) VALUES (?, ?, ?)");
    $stmt->execute([$_SESSION['user_id'], $logOperation, date('Y-m-d H:i:s')]);

    echo "<div class='alert alert-success'>Operation deleted successfully!</div>";
}

// Fetch all operations from the database
$stmt = $pdo->query("
    SELECT o.id, o.operation AS description, o.operation_time, u.name AS user_name
    FROM operations o
    JOIN users u ON o.user_id = u.id
    ORDER BY o.operation_time DESC
");
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="content">
    <h2>Operations Log</h2><br><br>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Description</th>
                <th>Date/Time</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($records as $record): ?>
                <tr>
                    <td><?= $record['id'] ?></td>
                    <td><?= htmlspecialchars($record['user_name']) ?></td>
                    <td><?= htmlspecialchars($record['description']) ?></td>
                    <td><?= htmlspecialchars($record['operation_time']) ?></td>
                    <td>
                        <!-- View Button -->
                        <button class="btn btn-info" data-toggle="modal" data-target="#viewModal<?= $record['id'] ?>">View</button>
                        <!-- Update Modal Trigger -->
                        <button class="btn btn-success" data-toggle="modal" data-target="#updateModal<?= $record['id'] ?>">Update</button>
                        <!-- Delete Form -->
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $record['id'] ?>">
                            <button type="submit" name="delete_operation" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>

                <!-- View Modal -->
                <div class="modal fade" id="viewModal<?= $record['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="viewModalLabel<?= $record['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="viewModalLabel<?= $record['id'] ?>">View Record</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><strong>User:</strong> <?= htmlspecialchars($record['user_name']) ?></p>
                                <p><strong>Description:</strong> <?= htmlspecialchars($record['description']) ?></p>
                                <p><strong>Date/Time:</strong> <?= htmlspecialchars($record['operation_time']) ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Modal -->
                <div class="modal fade" id="updateModal<?= $record['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel<?= $record['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <form method="post">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="updateModalLabel<?= $record['id'] ?>">Update Record</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $record['id'] ?>">
                                    <div class="form-group">
                                        <label for="operation">Description</label>
                                        <input type="text" name="operation" class="form-control" value="<?= htmlspecialchars($record['description']) ?>" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" name="update_operation" class="btn btn-primary">Update Record</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include('footer.php'); ?>