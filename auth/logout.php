<?php
require_once __DIR__ . "/../db.php";

$_SESSION = [];

session_destroy();

header("Location: ../dashboard.php");
exit;