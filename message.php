<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$message = strtolower(trim($input['message'] ?? ''));

function analyze_message($message) {
    preg_match_all('/https?:\/\/[\w\.-]+\.[a-z]{2,6}\S*/', $message, $urls);

    $fake_keywords = [
        'free', 'win', 'click', 'urgent', 'offer', 'verify', 'password', 'bank',
        'prize', 'lottery', 'congratulations', 'risk-free', 'act now', 'limited time',
        'deal', 'subscribe', 'unsubscribe', 'scam', 'hack', 'account suspended',
        'confirm your identity', 'security alert'
    ];

    $suspicious = false;
    foreach ($fake_keywords as $keyword) {
        if (strpos($message, $keyword) !== false) {
            $suspicious = true;
            break;
        }
    }

    if (strlen($message) < 20 && preg_match('/(buy|cheap|discount|offer)/', $message)) {
        $suspicious = true;
    }

    if (!empty($urls[0])) {
        $response = "🔎 Analyzing URL(s)...\n";
        foreach ($urls[0] as $url) {
            $suspicious_url = preg_match('/(xn--|\.ru|\.cn|\.xyz|\.top|\.club|\.work)/', $url);
            $result = ($suspicious || $suspicious_url) ? "⚠️ Likely fake or suspicious." : "✅ Looks safe.";
            $response .= "$url - $result\n";
        }
        return $response;
    }

    if ($suspicious) {
        return "⚠️ This message seems suspicious or potentially spam. Please be cautious.";
    }

    return null;
}

function guide_user($message) {
    $patterns = [
        'cyber stats|stats|crime' => "📊 'Cyber Stats' shows crime rates and predictions by state, city, year, and crime type.",
        'mapit|map' => "🗺️ 'MapIt' lets you click on a location on the map and auto-fill your dashboard selection.",
        'top city|top' => "🏙️ 'Top City' displays cities with the highest and lowest crime rates.",
        'fake account|account|profile' => "🕵️ 'Fake Account Detector' checks if a social profile is likely genuine or fake.",
        'cyber bot|bot|chat' => "🤖 I am Cyber Bot. I help users explore the site and check URLs or messages for fake content."
    ];

    foreach ($patterns as $pattern => $response) {
        if (preg_match("/\b($pattern)\b/", $message)) {
            return $response;
        }
    }
    return null;
}

$analysis = analyze_message($message);
if ($analysis !== null) {
    echo json_encode(['reply' => $analysis]);
    exit;
}

$guidance = guide_user($message);
if ($guidance !== null) {
    echo json_encode(['reply' => $guidance]);
    exit;
}

$fallbacks = [
    "🤖 I'm here to help. Ask me about the modules or paste a URL/message to analyze.",
    "🔍 Try asking about 'Cyber Stats', 'MapIt', or analyze a suspicious link.",
    "🤔 I'm learning! Try rephrasing or ask about a feature you want."
];

echo json_encode(['reply' => $fallbacks[array_rand($fallbacks)]]);
exit;
?>
