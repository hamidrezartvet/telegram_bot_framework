<?php

//here we load helpers
require_once __DIR__ . '/helper/authenticate_helper.php';
require_once __DIR__ . '/helper/message_helper.php';
require_once __DIR__ . '/helper/session_helper.php';
require_once __DIR__ . '/helper/login_helper.php';
require_once __DIR__ . '/helper/loger_helper.php';
require_once __DIR__ . '/helper/function_helper.php';
require_once __DIR__ . '/constants/constants.php';

// Process the incoming request
try {

    // If there's a message from the user
    if (isset($update['message']) ) {
        $chat_id        = $update['message']['chat']['id'];
        $text           = $update['message']['text'];
   
        //here we get session class to check if any action is started to continue or not
        $sessionManager = new SessionManager($chat_id);
        $loginManager   = new LoginManager($chat_id);
        $session        = $sessionManager->getSession();

        //if user pressed start , we stop and reset all process
        if($text == "/start"){

            //here we delete sessions and reset all going process
            $sessionManager->clearSession();
            showMainMenu($chat_id);
            exit();
        }

        //here we check if we have sessions
        if ($session != null) {

            //if we have an ongoing work we get detail
            $action = $session['action'];
            $step   = $session['step'];

            //here we check if action is not login we check if user is logged in or not
            if($action != "login"){

                if(!check_user_is_logged_in($loginManager)) {

                    sendMessage($chat_id, "your session is expired! please login.");
                    showMainMenu($chat_id);
                    exit();
                }
            }

            //here we have core of this framework. FSM make defining functions very easy.
            if (isset($fsm[$action][$step])) {
                $currentStep = $fsm[$action][$step];
                $nextStep = $currentStep['next'];
                $actionFunction = $currentStep['action'];

                if (function_exists($actionFunction)) {
                    $updatedSession = $actionFunction($chat_id, $session, $text);

                    if ($nextStep !== null) {
                        $updatedSession['action']   = $action;
                        $updatedSession['step']     = $nextStep;

                        $sessionManager->saveSession($updatedSession);
                    } else {
                        $sessionManager->clearSession();
                    }
                } else {
                    sendMessage($chat_id, "ops! something went wrong!");
                }
            } else {
                sendMessage($chat_id, "ops! something went wrong!");
            }
        }else{

            //if we do not have any sessions we request user to choose an option from menu
            sendMessage($chat_id, "please choose an option to start!");
            showMainMenu($chat_id);
        }
    }

    // If there's a callback_query from the user
    if (isset($update['callback_query'])) {
        $callback_query = $update['callback_query'];
        $callback_data  = $callback_query['data'];
        $chat_id        = $callback_query['message']['chat']['id'];

        //here we load session classes
        $sessionManager = new SessionManager($chat_id);
        $loginManager   = new LoginManager($chat_id);

        //here we load user data
        $user_data      = $loginManager->getLogin();

        //if command is not login first you check if user is logged in or not
        if (!check_user_is_logged_in($chat_id)) {
            sendMessage($chat_id, "your session is expired! please login.");
            showMainMenu($chat_id);
            exit();
        }

        // Here we react based on button clicked and callback query text
        if(strpos($callback_data, 'login') === 0){

            // Start a new session based on the callback
            if ($session == null) {
                $session = ['action' => 'login', 'step' => 2, 'data' => []];
            }
            $sessionManager->saveSession($session);
            sendMessage($chat_id, "please enter your username");
            exit();
        }else if(strpos($callback_data, 'test') === 0){

            //here we have a test function
            sendMessage($chatId, "this is a test message for testing test button!");
        }else if(strpos($callback_data, 'logout') === 0){
         
            //here we delete sessions file
            $sessionManager ->  clearSession();
            $loginManager   ->  clearLogin();
            sendMessage($chat_id, "you logged out successfully!");
            showMainMenu($chat_id);
        }
    }
} catch (Exception $e) {
    sendMessage($chat_id ?? 0, "An error occurred. Please try again later.");
}