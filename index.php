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

                if(!check_user_is_logged_in($chat_id)) {

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

    if (isset($update['callback_query'])) {
        $callback_query = $update['callback_query'];
        $chat_id = $callback_query['message']['chat']['id'];
        $callback_data = $callback_query['data'];
        $session_file = SESSION_FILE."/$chat_id"."_user_login_data.json";
        $state  = json_decode(file_get_contents($session_file), true);
        $sessionManager = new SessionManager($chat_id);
        $loginManager   = new LoginManager($chat_id);

        // Logged in, allow main menu actions
        if($callback_data == "login"){
            $sessionManager = new SessionManager($chat_id);
            $session = $sessionManager->getSession();
    
            // Start a new session based on the callback
            if ($session == null) {
                if ($callback_data === 'create_user') {
                    $session = ['action' => 'create_user', 'step' => 1, 'data' => []];
                } elseif ($callback_data === 'login') {
                    $session = ['action' => 'login', 'step' => 2, 'data' => []];
                }
            }
            $sessionManager->saveSession($session);
            sendMessage($chat_id, "لطفا نام کاربری خود را وارد نمایید.");
            exit();
        }

        //if command is not login first you check if user is logged in or not
        if (!check_user_is_logged_in($chat_id)) {
            sendMessage($chat_id, "نشست شما منقضی شده است. لطفا ابتدا وارد حساب کاربری خود شوید.");
            showMainMenu($chat_id);
            exit();
        }

        //here we have our magic!
        if (strpos($callback_data, 'user_mgmt') === 0) {
    
            // User Management actions
            $parts  = explode(':', $callback_data);
            $page   = isset($parts[1]) ? intval($parts[1]) : 0;
            
            //here we get users list
            get_users_list($chat_id,$state['token'],$page,'');
        } elseif (strpos($callback_data, 'user:') === 0) {
            // Submenu for specific user
            $userId = explode(':', $callback_data)[1];
            $keyboard = [
                [['text' => '🔄 تمدید یک ماهه'              , 'callback_data' => "subscription:$userId"]],
                [['text' => '📋 دریافت اطلاعات کاربر'        , 'callback_data' => "get_user_data:$userId"]],
            ];
            sendKeyboard($chat_id, "مدیریت کاربر:", $keyboard);
        } elseif (strpos($callback_data, 'subscription:') === 0) {
            // Handle subscription
            $userId = explode(':', $callback_data)[1];
            $login_response = file_get_contents(MAIN_SERVER_URL . "chargeusersubscription/" . API_TOKEN . '/' . urlencode($state['token']) . '/' .$userId);
            $subscription_result = json_decode($login_response, true);
            
            if($subscription_result['result'] == 1){

                //here we need to update user login data        
                $state_login['token']         = $subscription_result['token'];
                $state_login['wallet']        = $subscription_result['wallet'];
                $state_login['users_count']   = $subscription_result['users_count'];
                $state_login['login_at']      = time();
                $state_login['logged_in']     = true;
                $loginManager->saveLogin($state);

                sendMessage($chat_id, "مبلغ ".$subscription_result['cost']." تومان بابت ساخت تمدید کاربر از حساب شما کسر شد."."\n"." مانده اعتبار کیف پول شما:"."\n".
                $subscription_result['wallet']." تومان می باشد."."\n"."تشکر از همراهی شما");
                showMainMenu($chat_id);
            }else{
                sendMessage($chat_id, $subscription_result['message']);
            }
        } elseif (strpos($callback_data, 'get_user_data:') === 0) {
            // Handle subscription
            $userId = explode(':', $callback_data)[1];
            $get_user_data_response = file_get_contents(MAIN_SERVER_URL . "getuserdata/" . API_TOKEN . '/' . urlencode($state['token']) . '/' .$userId);
            $user_data_result = json_decode($get_user_data_response, true);
            
            if($user_data_result['result'] == 1){

                sendMessage($chat_id, $user_data_result['get_user_data']);
                showMainMenu($chat_id);
            }else{
                sendMessage($chat_id, $subscription_result['message']);
            }
        } elseif (strpos($callback_data, 'wallet') === 0) {
            //here we display wallet credit
            $login_response = file_get_contents(MAIN_SERVER_URL . "getresellerwallet/" . API_TOKEN . '/' . urlencode($state['token']));
            $wallet_result = json_decode($login_response, true);
    
            if($wallet_result['result'] == 1){

                //here we need to update user login data        
                $state_user['token']         = $wallet_result['token'];
                $state_user['wallet']        = $wallet_result['wallet'];
                $state_user['users_count']   = $wallet_result['users_count'];
                $state_user['login_at']      = time();
                $state_user['logged_in']     = true;
                $loginManager->saveLogin($state_user);

                sendMessage($chat_id, "اعتبار کیف پول شما: ".$wallet_result['wallet']." تومان می باشد.");
                showMainMenu($chat_id);
            }else{
                sendMessage($chat_id, "در ارتباط با سرور مشکلی پیش امده است!");
            }
        } elseif (strpos($callback_data, 'create_user') === 0) {
    
            //here we can check if user has credit or not
            if($state['wallet'] == 0){
                sendMessage($chat_id, "شما اعتبار کافی برای ساخت کاربر جدید را ندارید!");
                exit();
            }


            // Start a new session based on the callback
            if ($callback_data === 'create_user') {
                $session = ['action' => 'create_user',  'step' => 1, 'data' => []];
            } elseif ($callback_data === 'login') {
                $session = ['action' => 'login',        'step' => 2, 'data' => []];
            }
            
            $sessionManager->saveSession($session);
            sendMessage($chat_id, "کاربر جدید: در صورت تمایل برای مدیریت راحت تر کاربر شماره موبایل کاربر جدید را وارد نمایید. در صورت نداشتن شماره کاربر یک شماره موبایل 11 رقمی وارد نمایید.");
        } elseif (strpos($callback_data, 'search_user') === 0) {
    
            // Start a new session based on the callback
            if ($callback_data === 'search_user') {
                $session = ['action' => 'search_user',  'step' => 1, 'data' => []];
            }
            
            $sessionManager->saveSession($session);
            sendMessage($chat_id, "جهت جستجوی کاربر قسمتی از نام کاربری یا شماره تماس ثبت شده را وارد نمایید.");
        }elseif(strpos($callback_data, 'logout') === 0){
         

            //here we delete sessions file
            $sessionManager ->  clearSession();
            $loginManager   ->  clearLogin();

            sendMessage($chat_id, "با موفقیت از حساب کاربری خود خارج شدید!");
            showMainMenu($chat_id);
        }
    }
} catch (Exception $e) {
    sendMessage($chat_id ?? 0, "An error occurred. Please try again later.");
}