<?php

function askUsername($chatId, $session, $text = null) {
    sendMessage($chatId, "welcome! please enter your username.");
}

function askPassword($chatId, $session, $text = null) {
    $text = convertToEnglishNumbers($text);
    $session['data']['username'] = $text;
    sendMessage($chatId, "thanks! now enter password please.");
    return $session;
}

function processLogin($chat_id, $session, $text = null) {

    //here we convert all the digits format to english
    $text = convertToEnglishNumbers($text);
    $session['data']['password'] = $text;

    //here we check if user exist or not
    $login_response = file_get_contents(MAIN_SERVER_URL . "/" . API_TOKEN . '/' . urlencode($session['data']['username']) . "/" . urlencode($text));
    $result = json_decode($login_response, true);
    if ($result['result'] == 1) {

        //here we need to update user login data
        $loginManager   = new LoginManager($chat_id);      
        $state['token']         = $result['token'];
        $state['username']      = $result['username'];
        $state['password']      = $result['password'];
        $state['login_at']      = time();
        $state['logged_in']     = true;
        $loginManager->saveLogin($state);

        sendMessage($chat_id, "hoooray! you logged in successfully!");
        showMainMenu($chat_id);
        exit();
    } else {
        sendMessage($chat_id, "ops! something went wrong!");
        $session['step'] = 1; // Reset login process
    }
    return $session;
}

function convertToEnglishNumbers($string) {
    $farsiArabicNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    
    return str_replace($farsiArabicNumbers, $englishNumbers, $string);
}