<?php
require_once __DIR__ . "/../db.php";
require_once __DIR__ . "/../progress.php";

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $u  = trim($_POST["user"]);
    $p1 = trim($_POST["pass"]);
    $p2 = trim($_POST["pass2"]);
    $q  = trim($_POST["question"]);
    $a  = trim($_POST["answer"]);

    if (empty($u) || empty($p1) || empty($p2) || empty($q) || empty($a)) {
        $error = "All fields are required.";
    }
    elseif ($p1 !== $p2) {
        $error = "Passwords do not match.";
    }
    else {

        $password_hash = password_hash($p1, PASSWORD_DEFAULT);
        $answer_hash   = password_hash($a, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare(
            $conn,
            "INSERT INTO users
             (username, password, security_question, security_answer, avatar)
             VALUES (?, ?, ?, ?, NULL)"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $u,
            $password_hash,
            $q,
            $answer_hash
        );

        try {
            mysqli_stmt_execute($stmt);

            $user_id = mysqli_insert_id($conn);

            $_SESSION["user"]    = $u;
            $_SESSION["user_id"] = $user_id;

            $stmt = mysqli_prepare(
                $conn,
                "INSERT INTO user_progress (user_id, current_level)
                VALUES (?, 0)"
            );
            mysqli_stmt_bind_param($stmt, "i", $user_id);
            mysqli_stmt_execute($stmt);

            $level = get_current_level();
            header("Location: ../ett/antechamber.php");
            exit;
        }
        catch (mysqli_sql_exception $e) {

            if ($e->getCode() == 1062) {
                $error = "Username already exists. Please choose another one.";
            } else {
                $error = "Registration temporarily unavailable.";
            }

        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Register</title>
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

        <p class="auth-title">Speak your name, and bind your path to memory...</p>

        <?php if (!empty($error)): ?>
            <div id="error-data"
                data-message="<?= htmlspecialchars($error) ?>"
                data-type="error"></div>
        <?php endif; ?>

        <form method="post" class="auth-form">

            <div class="auth-grid">

                <div class="auth-col">
                    <input name="user" class="auth-input" placeholder="Username">
                    <input name="pass" type="password" class="auth-input" placeholder="Password">
                    <input name="pass2" type="password" class="auth-input" placeholder="Repeat Password">
                </div>

                <div class="auth-col">
                    <input name="question" class="auth-input" placeholder="Security question">
                    <textarea name="answer" class="auth-input auth-input-large" placeholder="Answer"></textarea>
                </div>

            </div>

            <div class="auth-button">
                <button type="submit" class="button-s">Register</button>
            </div>

        </form>

        <div class="auth-links">
            <a href="login.php">Return to Log In</a>
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