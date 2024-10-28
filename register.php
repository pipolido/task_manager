<?php
include('header.php');
include('condb.php');




if (isset($_POST['register'])) {
    $name = $_POST['nom'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, status) VALUES ( ?, ?, ?, 'active')");
    $stmt->execute([$name, $email, $password]);

    echo "User registered successfully!";
    
}
header("locatio:login.php");
?>

<div class="content">
    <h2>Register</h2>
    <form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token'];?>">
        <div class="form-group">
            <label for="nom">Name</label>
            <input type="text" name="nom" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="register" class="btn btn-primary">Register</button>
    </form>
</div>

<?php include('footer.php'); ?>
