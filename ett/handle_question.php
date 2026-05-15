<?php
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../npc/npc_loader.php';
require_once __DIR__ . '/../llm/llm_client.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

$question = trim($_POST["question"] ?? "");

// Determine identity (logged-in user or guest)
$user_id = $_SESSION['user_id'] ?? null;
$guest_token = $_SESSION['guest_token'] ?? null;

// Determine current level
$level_id = null;

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

// Guest fallback
if (!$level_id) {
    $level_id = $_POST['level_id'] ?? null;

    if (!preg_match('/^ett\d+$/', $level_id)) {
        $level_id = null;
    }
}

$echo = null;

if ($level_id) {
    $level_file = __DIR__ . "/{$level_id}_config.php";

    if (file_exists($level_file)) {
        require_once $level_file;
    }

    $system_prompt = null;

    if ($echo !== null) {
        $system_prompt = get_npc_system_prompt($level_id, $echo);
    }
}

// Save player message if we have level + message
if ($level_id && $question !== '') {
    $stmt = $conn->prepare("
        INSERT INTO npc_messages (user_id, guest_token, level_id, sender, message)
        VALUES (?, ?, ?, 'player', ?)
    ");
    $stmt->bind_param("ssss", $user_id, $guest_token, $level_id, $question);
    $stmt->execute();
}

// NPC logic
$response = "The sands remain silent.";

$last_exchange = "";

// Fetch last NPC + player messages
if ($level_id) {
    $stmt = $conn->prepare("
        SELECT sender, message
        FROM npc_messages
        WHERE level_id = ?
        AND (user_id = ? OR guest_token = ?)
        ORDER BY created_at DESC
        LIMIT 2
    ");
    $stmt->bind_param("sss", $level_id, $user_id, $guest_token);
    $stmt->execute();

    $stmt->bind_result($sender, $message);

    $history = [];

    while ($stmt->fetch()) {
        $history[] = "{$sender}: {$message}";
    }

    if (!empty($history)) {
        $last_exchange = implode("\n", array_reverse($history));
    }
}

if ($system_prompt && $question !== '') {
    $response = ask_llm(
        $system_prompt,
        "Previous exchange:\n{$last_exchange}\n\nPlayer: {$question}"
    );
}

// Save NPC response if we have a valid level
if ($level_id && $response !== '') {
    $stmt = $conn->prepare("
        INSERT INTO npc_messages (user_id, guest_token, level_id, sender, message)
        VALUES (?, ?, ?, 'npc', ?)
    ");
    $stmt->bind_param("ssss", $user_id, $guest_token, $level_id, $response);
    $stmt->execute();
}

echo json_encode([
    "player_question" => $question,
    "npc_response" => $response
]);