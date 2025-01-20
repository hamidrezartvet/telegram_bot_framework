<?php

/**
 * Send a message with inline keyboard
 */
function sendKeyboard($chat_id, $text, $keyboard) {
    $encoded_keyboard = json_encode(['inline_keyboard' => $keyboard]);
    $url = API_URL . "sendMessage?chat_id=$chat_id&text=" . urlencode($text) . "&reply_markup=" . $encoded_keyboard;
    file_get_contents($url);
}

/**
 * Function to send a message to a user via Telegram
 */
function sendMessage($chat_id, $text) {
    $url = API_URL . "sendMessage?chat_id=$chat_id&text=" . urlencode($text);
    file_get_contents($url);
}


function showMainMenu($chat_id) {

    //here we get wallet and users count value
    $session_file = SESSION_FILE."/$chat_id"."_user_login_data.json";
    $wallet = 0;
    $users  = 0;
    if(file_exists($session_file)){
        $welcome_text = "لطفا یکی از گزینه های زیر را انتخاب کنید.";
        $state  = json_decode(file_get_contents($session_file), true);
        $wallet = $state['wallet'];
        $users  = $state['users_count'];
            
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'جستجو کاربر'                    , 'callback_data' => 'search_user'],
                    ['text' => 'کاربر جدید'                     , 'callback_data' => 'create_user'],
                ],
                [
                    ['text' => 'لیست کاربران'.'('.$users.')'    , 'callback_data' => 'user_mgmt'],
                ],
                [
                    ['text' => 'کیف پول'.'('.$wallet.' تومان)'  , 'callback_data' => 'wallet'],

                ],
                [
                    ['text' => 'خروج'                           , 'callback_data' => 'logout']
                ]
            ]
        ];
    }else{

        //here we define welcome message
        $welcome_text = "خوش آمدید! برای استفاده از ربات ابتدا باید وارد شوید. روی دکمه زیر کلیک کنید.";
        $keyboard = [
            'inline_keyboard' => [
                [
                    ['text' => 'ورود'                           , 'callback_data' => 'login']
                ]
            ]
        ];
    }

    $encoded_keyboard = json_encode($keyboard);

    $url = API_URL . "sendMessage?chat_id=$chat_id&text=" . urlencode($welcome_text) . "&reply_markup=" . $encoded_keyboard;
    file_get_contents($url);
}