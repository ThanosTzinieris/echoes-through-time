<?php
require_once __DIR__ . '/../db.php';

$already_logged_in = isset($_SESSION["user_id"]);

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $u = trim($_POST["user"] ?? '');
    $p = trim($_POST["pass"] ?? '');

    if ($u === '' || $p === '') {
        $error = "Missing credentials";
    } else {

        $stmt = mysqli_prepare(
            $conn,
            "SELECT * FROM users WHERE username = ?"
        );

        mysqli_stmt_bind_param($stmt, "s", $u);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $row    = mysqli_fetch_assoc($result);

        if (!$row) {
            $error = "Username not found";
        }
        elseif (!password_verify($p, $row["password"])) {
            $error = "Incorrect password";
        }
        else {
            session_regenerate_id(true);

            $_SESSION["user"] = $u;
            $_SESSION["user_id"] = $row["id"];

            $stmt = mysqli_prepare(
                $conn,
                "INSERT IGNORE INTO user_progress (user_id, current_level)
                VALUES (?, 0)"
            );
            mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);
            mysqli_stmt_execute($stmt);

            require_once __DIR__ . "/../progress.php";

            $level = get_current_level();

            if ($level < 0) {
                echo "<script>window.location.replace('../ett/antechamber.php');</script>";
            } else {
                echo "<script>window.location.replace('../ett/ett" . ($level + 1) . ".php');</script>";
            }
            exit;

        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Login</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/auth.css">
        <link rel="stylesheet" href="../css/popup.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
        <link rel="icon" type="image/x-icon" href="/favicon/favicon.ico">
        <link rel="apple-touch-icon" href="/favicon/apple-touch-icon.png">
        <link rel="manifest" href="/favicon/site.webmanifest">
    </head>

    <body>

    <div class="auth-container">
            <div class="auth-left">

                <a href="../dashboard.php" class="sigil"></a>

                <?php if (!empty($error)): ?>
                    <div id="error-data" 
                        data-message="<?= htmlspecialchars($error) ?>"
                        data-type="error">
                    </div>
                <?php endif; ?>

                <?php if (!$already_logged_in): ?>
                    <p class="auth-title">Speak your name, and I shall restore your path...</p>

                    <form method="post" class="auth-form">
                        <label>
                            <input type="text" name="user" class="auth-input" placeholder="Username">
                        </label>

                        <label>
                            <input type="password" name="pass" class="auth-input" placeholder="Password">
                        </label>

                        <div class="auth-button">
                            <button type="submit" class="button-s">Login</button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="auth-title">You are already walking your path...</div>

                    <div class="auth-actions">
                        <form action="../auth/logout.php" method="post">
                            <button type="submit" class="button-s">Log Out</button>
                        </form>

                        <form action="../index.php" method="get">
                            <button type="submit" class="button-l">Continue your Journey</button>
                        </form>
                    </div>
                <?php endif; ?>

                <div class="auth-links">
                    <a href="register.php">Register</a>
                    <span class="auth-separator">|</span>
                    <a href="forgot_password.php">Forgot password?</a>
                    <span class="auth-separator">|</span>
                    <a href="../contact.php" class="auth-links">Contact</a>
                </div>

            </div>

            <div class="auth-right">
                <img src="../images/sage.png" alt="Sage" class="auth-image">
            </div>

        </div>

    <script src="../js/buttons.js"></script>
    <script src="../js/popup.js"></script>
    
    </body>
</html>