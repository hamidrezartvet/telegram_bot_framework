<?php

require_once __DIR__ . '/helper/authenticate_helper.php';
require_once __DIR__ . '/helper/message_helper.php';
require_once __DIR__ . '/constants/constants.php';

class Logger {
    private static $logFile = __DIR__ . '/sessions/debug.log';

    public static function log($message) {
        $timestamp = date('[Y-m-d H:i:s]');
        file_put_contents(self::$logFile, "$timestamp $message\n", FILE_APPEND);
    }

    public static function error($message) {
        $timestamp = date('[Y-m-d H:i:s]');
        file_put_contents(self::$logFile, "$timestamp [ERROR] $message\n", FILE_APPEND);
    }
}

class SessionManager {
    private $sessionFile;

    public function __construct($chatId) {
        $this->sessionFile = __DIR__ . "/sessions/{$chatId}.json";
    }

    public function getSession() {
        if (file_exists($this->sessionFile)) {
            Logger::log("Loading session for chat ID: $this->sessionFile");
            return json_decode(file_get_contents($this->sessionFile), true);
        }
        Logger::log("No session found for chat ID: $this->sessionFile");
        return null;
    }

    public function saveSession($data) {
        Logger::log("Saving session for chat ID: $this->sessionFile with data: " . json_encode($data));
        file_put_contents($this->sessionFile, json_encode($data));
    }

    public function clearSession() {
        if (file_exists($this->sessionFile)) {
            Logger::log("Clearing session for chat ID: $this->sessionFile");
            unlink($this->sessionFile);
        }
    }
}

// Define FSM states and transitions
$fsm = [
    'create_user' => [
        1 => ['next' => 2, 'action' => 'askMobile'],
        2 => ['next' => 3, 'action' => 'askName'],
        3 => ['next' => null, 'action' => 'finalizeUserCreation'],
    ],
    'login' => [
        1 => ['next' => 2, 'action' => 'askUsername'],
        2 => ['next' => 3, 'action' => 'askPassword'],
        3 => ['next' => null, 'action' => 'processLogin'],
    ],
];



function askUsername($chatId, $session, $text = null) {
    Logger::log("askUsername called for chat ID: $chatId");
    sendMessage($chatId, "Please enter your username:");
}

function askPassword($chatId, $session, $text = null) {
    Logger::log("askPassword called for chat ID: $chatId with username: $text");
    $session['data']['username'] = $text;
    sendMessage($chatId, "Now, please enter your password:");
    return $session;
}

function processLogin($chatId, $session, $text = null) {
    Logger::log("processLogin called for chat ID: $chatId with password: $text");
    $session['data']['password'] = $text;

    $response = file_get_contents(MAIN_SERVER_URL . "check_login/" . API_TOKEN . "/" . $session['data']['username'] . "/" . $session['data']['password']);
    $result = json_decode($response, true);

    if ($result['success']) {
        Logger::log("Login successful for chat ID: $chatId");
        sendMessage($chatId, "Login successful!");
        showMainMenu($chatId);
    } else {
        Logger::error("Login failed for chat ID: $chatId");
        sendMessage($chatId, "Invalid credentials. Please try again.");
        $session['step'] = 1; // Reset login process
    }

    return $session;
}

// Process the incoming request
try {
    if (isset($update['callback_query'])) {
        $callback_query = $update['callback_query'];
        $chatId = $callback_query['message']['chat']['id'];
        $callbackData = $callback_query['data'];

        Logger::log("Callback query received for chat ID: $chatId with data: $callbackData");

        $sessionManager = new SessionManager($chatId);
        $session = $sessionManager->getSession();

        // Start a new session based on the callback
        if (!$session) {
            if ($callbackData === 'create_user') {
                $session = ['action' => 'create_user', 'step' => 1, 'data' => []];
            } elseif ($callbackData === 'login') {
                $session = ['action' => 'login', 'step' => 1, 'data' => []];
            }
        }

        $sessionManager->saveSession($session);
        sendMessage($chatId, "Let's get started!");
    }

    if (isset($update['message'])) {
        $chatId = $update['message']['chat']['id'];
        $text = $update['message']['text'];

        Logger::log("Message received for chat ID: $chatId with text: $text");

        $sessionManager = new SessionManager($chatId);
        $session = $sessionManager->getSession();

        if ($session) {
            $action = $session['action'];
            $step = $session['step'];

            if (isset($fsm[$action][$step])) {
                $currentStep = $fsm[$action][$step];
                $nextStep = $currentStep['next'];
                $actionFunction = $currentStep['action'];

                if (function_exists($actionFunction)) {
                    $updatedSession = $actionFunction($chatId, $session, $text);
                    if ($nextStep !== null) {
                        $updatedSession['step'] = $nextStep;
                        $sessionManager->saveSession($updatedSession);
                    } else {
                        $sessionManager->clearSession();
                    }
                } else {
                    Logger::error("Invalid action function: $actionFunction");
                    sendMessage($chatId, "Invalid action.");
                }
            } else {
                Logger::error("Invalid step for action: $action, step: $step");
                sendMessage($chatId, "Invalid step.");
            }
        } else {
            sendMessage($chatId, "No active session. Please start by clicking a button.");
        }
    }
} catch (Exception $e) {
    Logger::error("Unhandled exception: " . $e->getMessage());
    sendMessage($chatId ?? 0, "An error occurred. Please try again later.");
}

// Send main menu with buttons
function showMainMenu($chatId) {
    $keyboard = [
        [['text' => "Create User", 'callback_data' => 'create_user']],
        [['text' => "Login", 'callback_data' => 'login']],
    ];
    sendKeyboard($chatId, "Choose an action:", $keyboard);
}