<?php

//here we load helpers
require_once __DIR__ . '/Config/Config.php';
require_once __DIR__ . '/Controllers/InputController.php';
require_once __DIR__ . '/Services/AuthService.php';
require_once __DIR__ . '/Services/MessageService.php';
require_once __DIR__ . '/Helpers/ImageHelper.php';
require_once __DIR__ . '/Helpers/LogerHelper.php';
require_once __DIR__ . '/Helpers/SessionHelper.php';

// Handle incoming webhook data
$update = json_decode(file_get_contents("php://input"), true);

// Define FSM states and transitions _ define functions here.
$fsm = [
    'create_user' => [
        1 => ['next' => 2,      'action'        => 'askMobile'],
        2 => ['next' => null,   'action'        => 'askUsers'],
    ],
    'login' => [
        1 => ['next' => 2,      'action'        => 'askUsername'],
        2 => ['next' => 3,      'action'        => 'askPassword'],
        3 => ['next' => null,   'action'        => 'processLogin'],
    ],
    'search_user' => [
        1 => ['next' => null,   'action'        => 'askUsersOrMnobile'],
    ],
];

// Process the incoming request
try {

    // If there's a message from the user
    if (isset($update['message'])) {

        $chat_id                = $update['message']['chat']['id'];
        $input_message_text     = $update['message']['text'];

        $sessionHelper          = new SessionHelper($chat_id);
        $authService            = new AuthService($chat_id);
        $messageService         = new MessageService($chat_id);
        $imageHelper            = new ImageHelper();
        $inputController        = new InputController($messageService,$sessionHelper,$authService,$imageHelper);

        $session_action         = $sessionHelper->getSession();

        //here we delete everything and start from first
        if($input_message_text == "/start"){

            $sessionHelper->clearSession();
            $messageService->showMainMenu();
            exit();
        }

        //here we check if we have sessions
        if ($session_action != null) {
            $action = $session_action['action'];
            $step   = $session_action['step'];

            //here we check if action is not login we check if user is logged in or not
            if($action != "login"){

                //Logged in, allow main menu actions
                if($authService->getLogin() == null) {
                    $messageService->sendMessage("نشست شما منقضی شده است. لطفا ابتدا وارد حساب کاربری خود شوید.");
                    $messageService->showMainMenu();
                    exit();
                }
            }

            if (isset($fsm[$action][$step])) {
                $currentStep = $fsm[$action][$step];

                $nextStep = $currentStep['next'];
                $actionFunction = $currentStep['action'];

                $updatedSession = $inputController->$actionFunction($session_action, $input_message_text);

                if ($nextStep !== null) {
                    $updatedSession['action']   = $action;
                    $updatedSession['step']     = $nextStep;

                    $sessionHelper->saveSession($updatedSession);
                } else {
                    $sessionHelper->clearSession();
                }
               
            } else {
                $messageService->sendMessage("به نظر میرسه مشکلی پیش اومده! لطفا مجدد تلاش کنید.");
            }
        }else{

            //here we display main menu
            $messageService->showMainMenu();
        }
    }

    if (isset($update['callback_query'])) {

        //here we get call back query detail
        $callback_query         = $update['callback_query'];
        $chat_id                = $callback_query['message']['chat']['id'];
        $message_id             = $callback_query['message']['message_id'];
        $callback_data          = $callback_query['data'];


        //here we make object of classes
        $sessionHelper          = new SessionHelper($chat_id);
        $authService            = new AuthService($chat_id);
        $messageService         = new MessageService($chat_id);
        $imageHelper            = new ImageHelper();
        $inputController        = new InputController($messageService,$sessionHelper,$authService,$imageHelper);

        //here we have user data as an array
        $user_data_array        = $authService->getLogin();

        /**
         * Check if login button is pressed or not
         */
        if($callback_data == "login"){
    
                
            //here we create session
            $session_array = ['action' => 'login', 'step' => 2, 'data' => []];

            //here we update session array
            $sessionHelper->saveSession($session_array);

            //here we send message
            $messageService->sendMessage("لطفا نام کاربری خود را وارد نمایید.");
            exit();
        }

        /**
         * Check user is logged in or not
         */
        if ($user_data_array == null) {

            $messageService->sendMessage("نشست شما منقضی شده است. لطفا ابتدا وارد حساب کاربری خود شوید.");
            $messageService->showMainMenu();
            exit();
        }

        /**
         * React based on callback button
         */
        if (strpos($callback_data, 'user_mgmt') === 0) {

            // User Management actions
            $parts  = explode(':', $callback_data);
            $page   = isset($parts[1]) ? intval($parts[1]) : 0;
            
            //here we get users list
            $inputController->get_users_list($user_data_array['token'],$page,'',$message_id);
        }elseif(strpos($callback_data, 'logout') === 0){
         
            //here we delete sessions file
            $sessionHelper->clearSession();
            $authService->clearLogin();
            $messageService->sendMessage("با موفقیت از حساب کاربری خود خارج شدید!");
            $messageService->showMainMenu();
        }
    }
} catch (Exception $e) {
}