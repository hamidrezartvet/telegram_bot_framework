<?php


// Telegram bot token
define('BOT_TOKEN', '');

// Define telegram API address
define('API_URL', 'https://api.telegram.org/bot' . BOT_TOKEN . '/');

// API token : it's better to define a token for communicating to your server
define('API_TOKEN', '');

// Define your main server's API URL
define('MAIN_SERVER_URL', '');


// Define session file address. we keep user interactions in sessions
define('SESSION_FILE', '/var/www/telegram/sessions');

// Number of users per page
$usersPerPage = 5; 

// Handle incoming webhook data
$update = json_decode(file_get_contents("php://input"), true);

// Define FSM states and transitions
$fsm = [
    'create_user' => [
        1 => ['next' => 2,      'action'        => 'askMobile'],
        2 => ['next' => null,   'action'        => 'askUsers'],
    ],
    'login' => [
        1 => ['next' => 2,      'action'        => 'askUsername'],
        2 => ['next' => 3,      'action'        => 'askPassword'],
        3 => ['next' => null,   'action'        => 'processLogin'],
    ]
];

if (!file_exists(SESSION_FILE)) {
    mkdir(SESSION_FILE, 0777, true);
}