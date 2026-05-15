<?php
session_start();
require_once __DIR__ . "/../db.php";

$error = '';

/* Reset recovery flow on fresh visit */
if($_SERVER["REQUEST_METHOD"] === "GET"){
	unset($_SESSION["recovery_user"]);
	unset($_SESSION["recovery_done"]);
}

/* Handle POST */
if($_SERVER["REQUEST_METHOD"] === "POST"){

	$u = trim($_POST["user"] ?? "");

	if(empty($u)){
		$error = "Please enter your username.";
	} else {

		$stmt = mysqli_prepare(
			$conn,
			"SELECT security_question, security_answer FROM users WHERE username=?"
		);
		mysqli_stmt_bind_param($stmt, "s", $u);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$row = mysqli_fetch_assoc($result);

		if(!$row){
			$error = "Username not found.";
		} else {

			$question = $row["security_question"];

			/* STEP 1: verify security answer */
			if(!isset($_SESSION["recovery_user"]) && isset($_POST["answer"])){

				$user_answer = trim($_POST["answer"]);

				if(password_verify($user_answer, $row["security_answer"])){
					$_SESSION["recovery_user"] = $u;
				} else {
					$error = "Incorrect answer.";
				}
			}

			/* STEP 2: reset password */
			if(isset($_SESSION["recovery_user"]) && isset($_POST["newpass"], $_POST["newpass2"])){

				$np1 = trim($_POST["newpass"]);
				$np2 = trim($_POST["newpass2"]);

				if(empty($np1) || empty($np2)){
					$error = "Please fill in both password fields.";
				} elseif($np1 !== $np2){
					$error = "Passwords do not match.";
				} else {

					$new_hash = password_hash($np1, PASSWORD_DEFAULT);

					$update = mysqli_prepare(
						$conn,
						"UPDATE users SET password=? WHERE username=?"
					);
					mysqli_stmt_bind_param($update, "ss", $new_hash, $_SESSION["recovery_user"]);
					mysqli_stmt_execute($update);

					/* fetch user id */
					$get_user = mysqli_prepare(
						$conn,
						"SELECT id FROM users WHERE username=?"
					);

					mysqli_stmt_bind_param(
						$get_user,
						"s",
						$_SESSION["recovery_user"]
					);

					mysqli_stmt_execute($get_user);

					$user_result = mysqli_stmt_get_result($get_user);
					$user_row = mysqli_fetch_assoc($user_result);

					/* auto-login + flash */
					$_SESSION["user"] = $_SESSION["recovery_user"];
					$_SESSION["user_id"] = $user_row["id"];
					$_SESSION["flash"] = "Password changed!";
					$_SESSION["flash_type"] = "success";

					unset($_SESSION["recovery_user"]);

					header("Location: ../index.php");
					exit;

				}
			}
		}
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Recover your path</title>
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

        <p class="auth-title">Have you forgotten who you are?</p>

		<?php if (!empty($error)): ?>
			<div id="error-data"
				data-message="<?= htmlspecialchars($error) ?>"
				data-type="error"></div>
		<?php endif; ?>

        <form method="post" class="auth-form">

            <input name="user" class="auth-input" placeholder="Username"
                   value="<?= htmlspecialchars($u ?? '') ?>">

            <?php if(isset($question) && !isset($_SESSION["recovery_user"])): ?>
                <p class="auth-subtext"><?= htmlspecialchars($question) ?></p>
                <input name="answer" class="auth-input" placeholder="Your answer">
            <?php endif; ?>

            <?php if(isset($_SESSION["recovery_user"])): ?>
                <input name="newpass" type="password" class="auth-input" placeholder="New password">
                <input name="newpass2" type="password" class="auth-input" placeholder="Repeat new password">
            <?php endif; ?>

            <div class="auth-button">
                <button type="submit" class="button-s">Continue</button>
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