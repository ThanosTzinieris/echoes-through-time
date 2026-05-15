<?php
require_once __DIR__ . "/db.php";

function get_current_level()
{
    // Logged-in user
    if (isset($_SESSION["user_id"])) {
        global $conn;

        $stmt = mysqli_prepare(
            $conn,
            "SELECT current_level FROM user_progress WHERE user_id = ?"
        );
        mysqli_stmt_bind_param($stmt, "i", $_SESSION["user_id"]);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $level);

        if (mysqli_stmt_fetch($stmt)) {
            return (int)$level; // ensure integer
        }

        return -1; // Before the Antechamber
    }

    // Guest with session progress
    if (isset($_SESSION["progress"]["current_level"])) {
        return (int)$_SESSION["progress"]["current_level"];
    }

    // Absolute default
    return -1; // Before the Antechamber
}