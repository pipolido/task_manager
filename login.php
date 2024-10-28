<?php
include('header.php');
include('condb.php');


    

session_regenerate_id(true);

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $submitted_csrf_token = $_POST['csrf_token']; // Get the CSRF token from the form

    if (!isset($_SESSION['csrf_token']) || $submitted_csrf_token !== $_SESSION['csrf_token']) {
        echo "<p style='color:red;'>Invalid CSRF token!</p>";
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "<p style='color:red;'>Invalid email format!</p>";
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Store user ID and role in session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];

        // Regenerate CSRF token after successful login
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

        // Insert CSRF token into the database with expiration (30 minutes)
        try {
            $expires_at = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            $stmt = $pdo->prepare("INSERT INTO csrf_tokens (user_id, csrf_token, created_at, expires_at) VALUES (?, ?, NOW(), ?)");
            $stmt->execute([$user['id'], $_SESSION['csrf_token'], $expires_at]);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        // Redirect to index.php after login
        header('Location: index.php');
        exit; // Ensure no further execution
    } else {
        echo "<p style='color:red;'>Invalid login credentials!</p>";
    }
}

// CSRF token validation logic for subsequent requests
if (isset($_POST['csrf_token'])) {
    $csrf_token = $_POST['csrf_token'];
    $user_id = $_SESSION['user_id'];

    // Validate the CSRF token from the database
    try {
        $stmt = $pdo->prepare("SELECT * FROM csrf_tokens WHERE user_id = ? AND csrf_token = ? AND expires_at > NOW()");
        $stmt->execute([$user_id, $csrf_token]);
        $csrf_record = $stmt->fetch();
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }

    if (!$csrf_record) {
        echo "<p style='color:red;'>Invalid or expired CSRF token!</p>";
        exit;
    }
}
?>
<style>
    .login-page {
        background-color: #343a40; 
        color: #fff; 
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .login-box {
        width: 360px;
    }
    .login-card-body {
        background-color: #1e1e1e;
        border-radius: 10px;
        padding: 20px;
    }
    .form-control {
        background-color: #2c2c2c; 
        border: 1px solid #555;
        color: #fff;
    }
    .form-control::placeholder {
        color: #bbb; 
    }
    .input-group-text {
        background-color: #2c2c2c;
        border: 1px solid #555;
    }
    .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        border-color: #004085;
    }
</style>

<!-- HTML Form with CSRF Token -->
<form action="" method="post">
    <!-- CSRF token field -->
    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
    <div class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="#"><b>Task</b>Manager</a>
        </div>
        <div class="card">
            <div class="card-body login-card-body">
                <p class="login-box-msg">Sign in to start your session</p>

                <form action="" method="post">
                    <div class="input-group mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-envelope"></span>
                            </div>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Password" required>
                        <div class="input-group-append">
                            <div class="input-group-text">
                                <span class="fas fa-lock"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-4">
                            <button type="submit" name="submit" class="btn btn-primary btn-block">Sign In</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</form>

<?php include('footer.php'); ?>
