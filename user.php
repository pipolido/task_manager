<?php
include('header.php');
include('condb.php');

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
?>

<div class="content">
    <h2>User Profile</h2>
    <table class="table">
        <tr>
            <th>Name:</th>
            <td><?= $user['name']; ?></td>
        </tr>
        <tr>
            <th>Email:</th>
            <td><?= $user['email']; ?></td>
        </tr>
        <tr>
            <th>Status:</th>
            <td><?= ucfirst($user['role']); ?></td>
        </tr>
        <tr>
            <th>Role:</th>
            <td><?= ucfirst($user['status']); ?></td>
        </tr>
    </table>
</div>

<?php include('footer.php'); ?>
