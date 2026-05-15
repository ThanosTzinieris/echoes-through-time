<?php

$error = '';
$sent = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name    = trim($_POST["name"] ?? '');
    $email   = trim($_POST["email"] ?? '');
    $subject = trim($_POST["subject"] ?? '');
    $message = trim($_POST["message"] ?? '');
    $honeypot = $_POST["website"] ?? '';
    $return_to = $_POST["return_to"] ?? 'index.php';

    // Honeypot check (anti-spam)
    if (!empty($honeypot)) {
        exit("Spam detected.");
    }

    // Required fields
    if ($name === '' || $message === '') {
        $error = "Name and message are required.";
    }

    // Email format check (only if filled)
    elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid e-mail format.";
    } else {

        $to = "REDACTED";   // Address redacted for GitHub

        $email_subject = "ETT Contact: " . ($subject ?: "No subject");

        $body = "Name: $name\n";
        $body .= "Email: " . ($email ?: "Not provided") . "\n";
        $body .= "Page: $return_to\n\n";
        $body .= "Message:\n$message\n";

        $headers = "From: REDACTED\r\n";   // Address redacted for GitHub

        if (!empty($email)) {
            $headers .= "Reply-To: $email\r\n";
        }

        if (mail($to, $email_subject, $body, $headers)) {
            $sent = true;
        } else {
            $error = "The Echo was lost... Please try again.";
        }

    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Contact</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="css/popup.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
    <link rel="icon" type="image/x-icon" href="/favicon/favicon.ico">
    <link rel="apple-touch-icon" href="/favicon/apple-touch-icon.png">
    <link rel="manifest" href="/favicon/site.webmanifest">
</head>

<body>

<div class="auth-container">

    <?php if (!$sent): ?>
    <div class="auth-left">

        <a href="dashboard.php" class="sigil"></a>

        <?php if (!empty($error)): ?>
            <div id="error-data"
                data-message="<?= htmlspecialchars($error) ?>"
                data-type="error">
            </div>
        <?php endif; ?>

        <p class="auth-title">
            Leave a trace in the Echoes...
        </p>

        <p class="auth-subtext">
            Report a flaw, share your thoughts, or seek guidance.
        </p>

        <form method="post" class="auth-form">

            <label>
                <input type="text" name="name" class="auth-input" placeholder="Name (or Traveler Name)">
            </label>

            <label>
                <input type="email" name="email" class="auth-input" placeholder="Email (optional)">
            </label>

            <label>
                <input type="text" name="subject" class="auth-input" placeholder="Subject (optional)">
            </label>

            <label>
                <textarea name="message" class="auth-input auth-input-large" rows="5" placeholder="Inscribe your message..."></textarea>
            </label>

            <!-- Hidden: honeypot anti-spam -->
            <input type="text" name="website" style="display:none">

            <!-- Hidden: referrer page -->
            <input type="hidden" name="return_to" value="<?= htmlspecialchars($_GET['from'] ?? 'index.php') ?>">

            <div class="auth-button">
                <button type="submit" class="button-s">Send</button>
            </div>

        </form>

    </div>
    <?php endif; ?>


    <?php if ($sent): ?>

    <div class="auth-left auth-success">

        <div class="auth-success-box">

            <p class="auth-title">
                Your Echo has been heard...
            </p>

            <div class="auth-button">
                <form action="<?= htmlspecialchars($return_to) ?>" method="GET">
                    <button type="submit" class="submit-button">
                        <img src="images/submit.png" alt="Next">
                    </button>
                </form>
            </div>

        </div>

    </div>

    <?php endif; ?>

    <div class="auth-right">
        <img src="images/sage.png" alt="Sage" class="auth-image">
    </div>

</div>

<script src="js/buttons.js"></script>
<script src="js/popup.js"></script>

</body>
</html>