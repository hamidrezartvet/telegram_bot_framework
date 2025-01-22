<?php

/**
 * Send a message with inline keyboard
 */
function sendKeyboard($chat_id, $text, $keyboard) {
    $encoded_keyboard = json_encode(['inline_keyboard' => $keyboard]);
    $url = API_URL . "sendMessage?chat_id=$chat_id&text=" . urlencode($text) .
     "&reply_markup=" . $encoded_keyboard;
    file_get_contents($url);
}

/**
 * Function to send a message to a user via Telegram
 */
function sendMessage($chat_id, $text) {
    $url = API_URL . "sendMessage?chat_id=$chat_id&text=" . urlencode($text);
    file_get_contents($url);
}

/**
 * Function to show menu to a user via Telegram
 */
function showMainMenu($chat_id) {

    //here we get wallet and users count value
    $session_file = SESSION_FILE."/$chat_id"."_user_login_data.json";
    $wallet = 0;
    $users  = 0;
    if(file_exists($session_file)){
        $welcome_text = "please choose an itme to start!";
        $state  = json_decode(file_get_contents($session_file), true);
        $wallet = $state['wallet'];
            
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'test button' , 'callback_data' => 'test'],

                ],
                [
                    ['text' => 'خروج'        , 'callback_data' => 'logout']
                ]
            ]
        ];
    }else{

        //here we define welcome message
        $welcome_text = "welcome! for using bot you need to login.";
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'login'       , 'callback_data' => 'login']
                ]
            ]
        ];
    }

    $encoded_keyboard = json_encode($keyboard);

    $url = API_URL . "sendMessage?chat_id=$chat_id&text=" . urlencode($welcome_text) . "&reply_markup=" . $encoded_keyboard;
    file_get_contents($url);
}