<?php
function ask_llm($system_prompt, $user_prompt)
{

    $api_key = getenv('OPENAI_API_KEY');    // Portfolio-safe environment configuration

    $url = getenv('OPENAI_API_URL');

    $data = [
        "model" => "gpt-4.1-mini",
        "messages" => [
            [
                "role" => "system",
                "content" => $system_prompt
            ],
            [
                "role" => "user",
                "content" => $user_prompt
            ]
        ],
        "max_tokens" => 200,
        "temperature" => 0.7
    ];

    $json_data = json_encode($data);

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "Authorization: Bearer " . $api_key
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);

    $response = curl_exec($ch);

    curl_close($ch);

    $decoded = json_decode($response, true);

    return $decoded['choices'][0]['message']['content'] ?? "No response from NPC.";
}