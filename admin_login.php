<?php
$pageTitleKey = "management_login";
include 'header.php';

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Auto-setup table if missing (Hackathon safety)
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password_hash VARCHAR(255) NOT NULL,
        role VARCHAR(20) DEFAULT 'admin'
    )");

    // Auto-create default admin if table is empty
    $check_admin = mysqli_query($conn, "SELECT id FROM admins LIMIT 1");
    if (mysqli_num_rows($check_admin) == 0) {
        $def_user = 'admin';
        $def_pass = password_hash('admin123', PASSWORD_DEFAULT);
        mysqli_query($conn, "INSERT INTO admins (username, password_hash) VALUES ('$def_user', '$def_pass')");
    }

    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = $_POST['password'];

    $res = mysqli_query($conn, "SELECT * FROM admins WHERE username='$user'");
    if ($row = mysqli_fetch_assoc($res)) {
        if (password_verify($pass, $row['password_hash'])) {
            $_SESSION['admin_logged_in'] = true;
            header("Location: admin_dashboard.php");
            exit();
        }
    }
    $error = $lang == 'en' ? "Invalid credentials!" : "गलत प्रयोगकर्ता नाम वा पासवर्ड!";
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card border-0 shadow-lg p-4" style="border-top: 5px solid var(--gov-blue) !important;">
                <h4 class="text-center text-primary mb-4"><?php echo __('management_login'); ?></h4>
                <?php if ($error)
                    echo "<div class='alert alert-danger'>$error</div>"; ?>
                <form action="admin_login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold"><?php echo __('username'); ?></label>
                        <input type="text" name="username" class="form-control border-primary" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold"><?php echo __('password'); ?></label>
                        <input type="password" name="password" class="form-control border-primary" required>
                    </div>
                    <button type="submit"
                        class="btn btn-primary w-100 shadow mt-2"><?php echo __('login_btn'); ?></button>
                </form>
                <div class="mt-4 text-center small text-muted">
                    <?php echo __('official_portal'); ?>
                </div>
            </div>
        </div>
    </div>
</div><?php include 'footer.php'; ?>