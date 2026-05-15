<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../progress.php';

$current_level = get_current_level();
$target_level = ($current_level < 1) ? 1 : $current_level + 1;

$flash = $_SESSION["flash"] ?? '';
unset($_SESSION["flash"]);

if ($current_level < 0) {

    if (isset($_SESSION["user_id"])) {

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE user_progress SET current_level = 0 WHERE user_id = ?"
        );

        mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);
        mysqli_stmt_execute($stmt);

    } else {
        $_SESSION["progress"]["current_level"] = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>The Antechamber</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/antechamber.css">
        <link rel="stylesheet" href="../css/popup.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
        <link rel="icon" type="image/x-icon" href="/favicon/favicon.ico">
        <link rel="apple-touch-icon" href="/favicon/apple-touch-icon.png">
        <link rel="manifest" href="/favicon/site.webmanifest">
    </head>

    <body class="antechamber-page">

        <?php if (!empty($flash)): ?>
            <div id="error-data"
                data-message="<?= htmlspecialchars($flash) ?>"
                data-type="error">
            </div>
        <?php endif; ?>

        <div class="avatar">
            <div class="avatar-icon"></div>

            <div class="avatar-dropdown">
                <?php if (isset($_SESSION["user"])): ?>
                    <a href="../auth/logout.php">
                        <img src="../images/logout.png" alt="Log Out">
                    </a>
                <?php else: ?>
                    <a href="../auth/login.php">
                        <img src="../images/login.png" alt="Log In">
                    </a>
                <?php endif; ?>

                <a href="../contact.php">
                    <img src="../images/contact.png" alt="Contact">
                </a>
            </div>
        </div>

        <div class="antechamber-container">

            <div class="top-section">
                <a href="../ett/antechamber.php" class="sigil"></a>

                <?php if (isset($_SESSION["user"])): ?>
                    <p class="greeting">Welcome, <?php echo htmlspecialchars($_SESSION["user"]); ?>.</p>
                <?php else: ?>
                    <p class="greeting">You walk unseen...</p>
                <?php endif; ?>
            </div>

            <div class="bottom-section">
                <div class="immersion-text">
                    <p>History remembers more than it reveals. Its truths lie buried.
                    <br>In names, in deeds, in what was wrought by human hands.</p>

                    <p>At each step, the guardians of truth will set a trial of intellect and discernment before you.
                    To an inquiring mind, they answer only in what is true, and what is false.</p>

                    <p>But the final answer must be yours alone.</p>

                    <form action="ett<?php echo $target_level; ?>.php" method="GET">
                        <button type="submit" class="button-s">
                            <?php echo ($current_level <= 0) ? "Begin" : "Continue"; ?>
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <script src="../js/buttons.js"></script>
        <script src="../js/popup.js"></script>

    </body>
</html>