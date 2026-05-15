<?php
require_once __DIR__ . "/db.php";

$flash = $_SESSION["flash"] ?? '';
unset($_SESSION["flash"]);

if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/popup.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title>Echoes Through Time</title>
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
    <link rel="icon" type="image/x-icon" href="/favicon/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/favicon/site.webmanifest">
</head>

<body>

<?php if (!empty($flash)): ?>
    <div id="error-data"
         data-message="<?= htmlspecialchars($flash) ?>"
         data-type="success"></div>
<?php endif; ?>

<a href="dashboard.php" class="sigil"></a>

<div class="menu-container">

    <div class="panels">

        <div class="panel-box">
            <h2>You seem familiar.</h2>
            <p>Speak your name, and I shall restore your path...</p>

            <a href="auth/login.php" class="button-s">Login</a>
            <p class="subtext">Returning player</p>
        </div>

        <div class="panel-box">
            <h2>...or walk unseen.</h2>
            <p>But know that your footsteps will fade.</p>

            <a href="ett/antechamber.php" class="button-l">Enter as Guest</a>
            <p class="subtext">Progress will not be saved</p>
        </div>

    </div>

</div>

<a href="contact.php" class="contact-link">Send an Echo</a>

<script src="js/buttons.js"></script>
<script src="js/popup.js"></script>

</body>
</html>