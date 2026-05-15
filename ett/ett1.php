<?php
    require_once __DIR__ . '/../db.php';
    require_once __DIR__ . '/../progress.php';
    require_once __DIR__ . '/ett1_config.php';  // LEVEL#
    require_once __DIR__ . '/../level_guard.php';
    guard_level($level - 1);

    $flash = $_SESSION["flash"] ?? '';
    unset($_SESSION["flash"]);

    $error = "";
    $solved = false;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $answer = strtolower(trim($_POST["answer"]));

        if ($answer === $echo){
            $solved = true;

            $current_level = get_current_level();

            // Only increase if user hasn't already progressed
            if ($current_level < $level) {

                // Logged-in user
                if (isset($_SESSION["user_id"])) {

                    $stmt = mysqli_prepare(
                        $conn,
                        "INSERT INTO user_progress (user_id, current_level)
                        VALUES (?, ?)
                        ON DUPLICATE KEY UPDATE
                        current_level = VALUES(current_level)"
                    );

                    mysqli_stmt_bind_param(
                        $stmt,
                        "ii",
                        $_SESSION["user_id"],
                        $level
                    );

                    mysqli_stmt_execute($stmt);
                }

                // Guest user
                else {
                    $_SESSION["progress"]["current_level"] = $level;
                }
            }
        } else {
            $error = "That is not the name I was buried with...";
        }
    }
?>

<!DOCTYPE html>

<html>
    <head>
        <title>Echo I: The Tomb Without a Name</title>
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="../css/ett.css">
        <link rel="stylesheet" href="../css/<?= "ett" . $level ?>.css">
        <link rel="stylesheet" href="../css/popup.css">
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
        <link rel="icon" type="image/x-icon" href="/favicon/favicon.ico">
        <link rel="apple-touch-icon" href="/favicon/apple-touch-icon.png">
        <link rel="manifest" href="/favicon/site.webmanifest">
    </head>

    <body>

        <div id="level-data" data-level="ett<?php echo $level; ?>"></div>


        <div class="avatar <?php echo $alignment; ?>">
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


        <div class="riddle-box <?php echo $alignment; ?>">

            <a href="antechamber.php" class="sigil"></a>
            <h2>Echo I: The Tomb Without a Name</h2>
            <p>"My father raised smooth towers on sand,<br>
            but mine would cast a shadow on all.<br>
            Though Ma'at took form at my command,<br>
            I carved no word on wall or scroll."</p>

            <div class="chat-container">

                <div id="chat-log"></div>

                <form id="question-form" class="input-row">
                    <input type="text" id="player-question" placeholder="Ask your question...">
                    <button type="submit" class="ask-button">
                        <img src="../images/send.png" alt="Ask">
                    </button>
                </form>

            </div>

            <form action="" method="POST">

                <div class="submit-row">

                    <div class="submit-container">
                        <input type="text" name="answer" class="submit-input">
                    </div>

                    <button type="submit" class="submit-button">
                        <img src="../images/submit.png" alt="Submit">
                    </button>

                </div>

            </form>

        </div>

        <?php
        $message = $error ?: $flash;

        $type = !empty($error)
            ? "error"
            : ($_SESSION["flash_type"] ?? "error");

        unset($_SESSION["flash_type"]);
        ?>

        <?php if (!empty($message)): ?>
            <div id="error-data"
                data-message="<?php echo htmlspecialchars($message); ?>"
                data-type="<?php echo $type; ?>"
            </div>
        <?php endif; ?>

        <?php
            $solved_text = "Khufu did not argue for truth. He monumentalized it.<br>
            Curious, isn't it?<br>
            That the grandest pyramid ever built felt no need to justify itself in writing.";

            $next_level = "ett" . ($level + 1) . ".php";

            include 'solved.php';
        ?>

        <script src="../js/ett.js"></script>
        <script src="../js/buttons.js"></script>
        <script src="../js/popup.js"></script>

    </body>
</html>