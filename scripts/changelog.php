<?php

// Require the autoloader created by Composer.
require __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\Client;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Get the milestone from the command line arguments
$milestone = 14;

// Set up the GitHub App credentials
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

// Generate the JWT token
$jwt = \Firebase\JWT\JWT::encode($payload, $privateKey, 'RS256');

// Initialize Guzzle client
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

// Get the access token
$accessToken = $tokenRequest->token;


$milestoneRequest = $client->request( 'GET', 'https://api.github.com/repos/nicole-benes/kanawa-mura/milestones/' . $milestone, [ 
    'headers' => [
        'Authorization' => 'Bearer ' . $accessToken,
    ],
]);

// Decode the response
$milestoneResult = json_decode( $milestoneRequest->getBody() );

// Get the milestone due date
$dueDate = $milestoneResult->due_on;

// Create a new DateTime object
$date = new DateTime( $dueDate, new DateTimeZone( 'UTC' ) );

// Generate the sprint taxononomy term
$sprintTerm = $date->format( 'F Y' );

// Pull out the repo from the milestone url
$repository = preg_match( '#^https://api\.github\.com/repos/[^/]+/([^/]+)#', $milestoneResult->url, $matches );

// Assign the actual match to our repo variable
$repositorySlug = $matches[1];

// Reset the repository variable
$repository = null;

switch( $repositorySlug ) {
    case 'raleighnc':
        $repository = 'raleighnc.gov';
        break;
    case 'udo':
        $repository = 'udo.raleighnc.gov';
        break;
    case 'corecon':
        $repository = 'corecon.raleighnc.gov';
        break;
    default:
        $repository = 'Unknown Site';
}

// Get issues in the triggering milestone
$issueRequest = $client->request( 'GET', 'https://api.github.com/repos/nicole-benes/kanawa-mura/issues?milestone=' . $milestone . '&state=open&label=bug', [ 
    'headers' => [
        'Authorization' => 'Bearer ' . $accessToken,
    ],
]);

// Decode the response
$issues = json_decode( $issueRequest->getBody() );

// Loop through the issues
foreach( $issues as $issue ) {

    // Assume the ticket number is null
    $ticket = null;

    // Check if the ticket is in the title
    if( strpos( $issue->title, 'COR-' ) !== false ) {

        // Get the ticket number from the title
        preg_match( '/COR-(\d+)/i', $issue->title, $matches );

        // Assign the ticket number to the variable
        $ticket = $matches[1];

    // Ticket number was not in the title
    } else {

        // Check if the issue has labels to look for the ticket number
        if( count( $issue->labels ) > 0 ) {

            // Loop through the labels
            foreach( $issue->labels as $key => $label ) {

                // Check if the label is a ticket
                if( strpos( $label->name, 'COR-' ) !== false ) {

                    // Get the ticket number from the label
                    preg_match( '/COR-(\d+)/i', $label->name, $matches );

                    // Assign the ticket number to the variable
                    $ticket = $matches[1];
                }
            }
        }
    }

    // Check if the ticket number is not null and is numeric
    if( is_numeric( $ticket ) ) {

        // Get the ticket URL
        $ticketUrl = 'https://atendesign.atlassian.net/browse/COR-' . $ticket;

        // Remove the ticket number from the title
        $ticketTitle = trim( preg_replace( '/COR-\d+: /i', '', $issue->title ) );

    }

    // Assume the category term is general update
    $categoryTerm = 'General Update';

    // Loop through the labels
    foreach( $issue->labels as $label ) {

        // Ensure the label is a not ticket number
        if( strpos( $label->name, 'COR-' ) === false ) {

            // Set the category term to the label name
            $categoryTerm = ucwords( $label->name );
        }
    }

    // Ensure we have collected all the data for our Drupal request
    if( $repository !== null && $categoryTerm != null && $sprintTerm != null && $ticketUrl != null && $ticketTitle != null ) {

        // Call the function to create the Drupal REST API request
        create_internal_update( $repository, $categoryTerm, $sprintTerm, $ticketUrl, $ticketTitle );
    } 
}

function create_internal_update( $repository, $categoryTerm, $sprintTerm, $ticketUrl, $ticketTitle ) {

    echo $repository . "\n";
    echo $categoryTerm . "\n";
    echo $sprintTerm . "\n";
    echo $ticketUrl . "\n";
    echo $ticketTitle . "\n";
}

/*
echo '<pre>';
print_r($issues);
echo '</pre>';
*/
?>