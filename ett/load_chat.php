<?php
require_once __DIR__ . '/../db.php';

// Determine identity
$user_id = $_SESSION['user_id'] ?? null;
$guest_token = $_SESSION['guest_token'] ?? null;

$level_id = null;

// If logged in, get level from user_progress
if ($user_id) {
    $stmt = $conn->prepare("SELECT current_level FROM user_progress WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $level_num = $result->fetch_row()[0] ?? null;

    if ($level_num !== null) {
        $level_id = "ett" . ($level_num + 1);
    }
}

// If guest, get level from POST
if (!$level_id) {

    $level_id = $_POST['level_id'] ?? null;

    if (!preg_match('/^ett\d+$/', $level_id)) {
        $level_id = null;
    }
}

$messages = [];

if ($level_id) {
    if ($user_id) {

        // Logged-in user: use ONLY user_id
        $stmt = $conn->prepare("
            SELECT sender, message
            FROM npc_messages
            WHERE user_id = ?
            AND level_id = ?
            ORDER BY created_at ASC
        ");

        $stmt->bind_param("is", $user_id, $level_id);

    } else {

        // Guest user: use ONLY guest_token
        $stmt = $conn->prepare("
            SELECT sender, message
            FROM npc_messages
            WHERE guest_token = ?
            AND level_id = ?
            ORDER BY created_at ASC
        ");

        $stmt->bind_param("ss", $guest_token, $level_id);
    }

    $stmt->execute();

    $stmt->bind_result($sender, $message);

    while ($stmt->fetch()) {
        $messages[] = [
            "sender" => $sender,
            "message" => $message
        ];
    }
}

echo json_encode($messages);
exit;