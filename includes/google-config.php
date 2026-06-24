<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load environment variables from .env file
require_once __DIR__ . '/env-loader.php';

// Google Client Configuration
// Use environment variables for sensitive credentials
define('GOOGLE_CLIENT_ID', getenv('GOOGLE_CLIENT_ID'));
define('GOOGLE_CLIENT_SECRET', getenv('GOOGLE_CLIENT_SECRET'));
define('GOOGLE_REDIRECT_URI', getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost/smartlostandfound/login.php');

/**
 * Generate Google Login URL
 */
function getGoogleLoginUrl() {
    $params = [
        'response_type' => 'code',
        'client_id'     => GOOGLE_CLIENT_ID,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'scope'         => 'openid email profile',
        'state'         => bin2hex(random_bytes(16)), // Secures against CSRF
    ];
    $_SESSION['oauth_state'] = $params['state'];
    return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
}

/**
 * Exchange Authorization Code for Access Token & Retrieve User Info
 */
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