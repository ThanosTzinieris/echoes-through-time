<?php
require_once __DIR__ . "/db.php";
require_once __DIR__ . "/progress.php";

if (isset($_SESSION["user_id"])) {
    $level = get_current_level();
    header("Location: ett/ett" . ($level + 1) . ".php");
    exit;
}

header("Location: dashboard.php");
exit;