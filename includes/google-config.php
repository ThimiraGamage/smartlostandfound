require_once 'env.php';
loadEnv(__DIR__ . '/.env');
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load environment variables from .env file
require_once __DIR__ . '/env-loader.php';

// Google Client Configuration
// Use environment variables for sensitive credentials
$GOOGLE_CLIENT_ID = $_ENV['GOOGLE_CLIENT_ID'];
$GOOGLE_CLIENT_SECRET = $_ENV['GOOGLE_CLIENT_SECRET'];
$GOOGLE_REDIRECT_URI = $_ENV['GOOGLE_REDIRECT_URI'];

/**
 * Generate Google Login URL
 */
function getGoogleLoginUrl() {

    global $GOOGLE_CLIENT_ID, $GOOGLE_REDIRECT_URI;

    $params = [
        'response_type' => 'code',
        'client_id'     => $GOOGLE_CLIENT_ID,
        'redirect_uri'  => $GOOGLE_REDIRECT_URI,
        'scope'         => 'openid email profile',
        'state'         => bin2hex(random_bytes(16)),
    ];

    $_SESSION['oauth_state'] = $params['state'];

    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}

function handleGoogleCallback($code) {
    $token_url = 'https://oauth2.googleapis.com/token';
    
    // POST Request Payload to Google Token Endpoint
    $post_data = [
        'code'          => $code,
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code'
    ];

    $ch = curl_init($token_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Keep simple for local XAMPP setup
    $response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    }
    curl_close($ch);

    $token_data = json_decode($response, true);
    if (!isset($token_data['access_token'])) {
        return false;
    }

    $access_token = $token_data['access_token'];

    // Retrieve User Profile Info using Access Token
    $info_url = 'https://www.googleapis.com/oauth2/v3/userinfo?access_token=' . urlencode($access_token);
    
    $ch = curl_init($info_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $info_response = curl_exec($ch);
    
    if (curl_errno($ch)) {
        curl_close($ch);
        return false;
    }
    curl_close($ch);

    return json_decode($info_response, true);
}
?>