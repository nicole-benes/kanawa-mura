<?php

// Require the autoloader created by Composer.
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


$appId = 'Iv23li5efDjrqP1fzr2u'; // Your GitHub App ID
$privateKeyPath = 'scripts/github.key'; // Path to your private key file

// Build the JWT payload
$payload = [
    "iat" => time() - 60,           // Github recommends giving a 60 second leeway
    "exp" => time() + (10 * 60),    // Expiration time (max 10 minutes)
    "iss" => $appId                 // App Identifier
];

// Read the private key
$privateKey = file_get_contents($privateKeyPath);

$jwt = $jwt = \Firebase\JWT\JWT::encode($payload, $privateKey, 'RS256');

// Use guzzle to get an access token using our credentials
$client = new \GuzzleHttp\Client(['verify' => false]);

// Get the installiation IDs for this app
$response = $client->request('GET', 'https://api.github.com/users/nicole-benes/installation', [
   'headers' => [
        'Authorization' => 'Bearer ' . $jwt,
    ],
]);

// Decode the response
$response = json_decode( $response->getBody() );

// Get an access token for the installation
$tokenRequest = $client->request( 'POST', $response->access_tokens_url, [ 
    'headers' => [
        'Authorization' => 'Bearer ' . $jwt,
    ],
]);

// Decode the token response
$tokenRequest = json_decode( $tokenRequest->getBody() );

$accessToken = $tokenRequest->token;

/*
$res = $client->request('POST', 'https://api.github.com/app/installations/1243499/access_tokens', [
    'headers' => [
*/

//echo '<pre>' . print_r( json_decode( $token->getBody() ), 1 ) . '</pre>';

echo $accessToken . '\n';

/*
$url = 'https://api.github.com/app/installations/INSTALLATION_ID/access_tokens';

$token = json_decode($guzzle->post($url, [
    'form_params' => [
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'resource' => 'https://graph.microsoft.com/',
        'grant_type' => 'client_credentials',
    ],
])->getBody()->getContents());
*/



/*
echo "File run!";
var_dump($argv);

$thing = $argv[ 1 ];

echo strlen( $thing ) . "\n";



function base64UrlEncode($text)
{
    return str_replace(
        ['+', '/', '='],
        ['-', '_', ''],
        base64_encode($text)
    );
}
    */