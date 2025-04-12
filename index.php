<?php
require 'config.php';

$input = json_decode(file_get_contents('php://input'), true);

// سجل البيانات لو تحب
file_put_contents('log.txt', print_r($input, true), FILE_APPEND);

// رد تفاعلي
if (isset($input['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply'])) {
    $selected = $input['entry'][0]['changes'][0]['value']['messages'][0]['interactive']['list_reply']['id'];
    $from = $input['entry'][0]['changes'][0]['value']['messages'][0]['from'];

    switch ($selected) {
        case 'account_info':
            $msg = "📄 تفاصيل حسابك: ...";
            break;
        case 'support':
            $msg = "🛠 الدعم الفني جاهز لخدمتك! تواصل معنا.";
            break;
        case 'contact':
            $msg = "📞 تواصل معنا عبر: email@example.com";
            break;
        default:
            $msg = "❓ خيار غير معروف.";
    }

    send_message($from, $msg);
}

function send_message($to, $message) {
    $url = "https://graph.facebook.com/v19.0/" . PHONE_NUMBER_ID . "/messages";
    $data = [
        "messaging_product" => "whatsapp",
        "to" => $to,
        "type" => "text",
        "text" => ["body" => $message]
    ];

    $headers = [
        "Authorization: Bearer " . ACCESS_TOKEN,
        "Content-Type: application/json"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}

echo "✅ Webhook جاهز";
