<?php
require_once __DIR__ . "/progress.php";

function guard_level($required_level) {

    $current_level = get_current_level();

    if ($current_level < 0) {

        $_SESSION["flash"] = "The path to truth has no shortcuts.";

        header("Location: antechamber.php");
        exit;
    }

    if ($current_level < $required_level) {

        $_SESSION["flash"] = "You dare reach beyond your understanding? Return… and earn your place.";

        header("Location: ett" . ($current_level + 1) . ".php");
        exit;
    }
}