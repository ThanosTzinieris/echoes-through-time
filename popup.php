<?php
http_response_code(500);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Path Lost</title>

    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
</head>

<body>

<div class="auth-container">

    <div class="auth-left">

        <p class="auth-title">
            I could not access the streams of consciousness
        </p>


        <?php
        // Default message
        $message = "Something went wrong.<br>Please try again later.";

        // Based on error type
        if (isset($error_type)) {
            if ($error_type === "db") {
                $message = "The echoes are silent... the archive is sealed.";
            }
        }
        ?>


        <p class="auth-error">
            <?= $message ?>
        </p>      

        <div class="auth-links">
            <a href="index.php">Return to the beginning</a>
        </div>

    </div>

    <div class="auth-right"></div>

</div>

<script src="js/buttons.js"></script>

</body>
</html>