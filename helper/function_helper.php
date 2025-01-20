<?php

function askUsername($chatId, $session, $text = null) {
    sendMessage($chatId, "خوش آمدید! لطفا نام کاربری خود را وارد کنید.");
}

function askPassword($chatId, $session, $text = null) {
    $text = convertToEnglishNumbers($text);
    $session['data']['username'] = $text;
    sendMessage($chatId, "مرسی! حالا رمز عبور را وارد کنید.");
    return $session;
}

function processLogin($chat_id, $session, $text = null) {
    $text = convertToEnglishNumbers($text);
    $session['data']['password'] = $text;

    //here we check if reseller exist or not
    $login_response = file_get_contents(MAIN_SERVER_URL . "checkresellerexist/" . API_TOKEN . '/' . urlencode($session['data']['username']) . "/" . urlencode($text));
    $result = json_decode($login_response, true);
    if ($result['result'] == 1) {

        //here we need to update user login data  
        $loginManager   = new LoginManager($chat_id);      
        $state['token']         = $result['token'];
        $state['wallet']        = $result['wallet'];
        $state['users_count']   = $result['users_count'];
        $state['login_at']      = time();
        $state['logged_in']     = true;
        $loginManager->saveLogin($state);

        sendMessage($chat_id, "تبریک! شما با موفقیت وارد شدید!");
        showMainMenu($chat_id);
    } else {
        sendMessage($chat_id, "در راستی آزمایی شما مشکلی پیش آمده است. لطفا مجدد تلاش کنید.");
        $session['step'] = 1; // Reset login process
    }
    return $session;
}

// Utility functions for FSM actions
function askMobile($chatId, $session, $text = null) {
    $text = convertToEnglishNumbers($text);

    //here we need to validate mobile number
    if (!isValidMobileNumber($text)) {
        $session['data']['mobile'] = $text;

        // Start a new session based on the callback
        $session = ['action' => 'create_user',  'step' => 1, 'data' => []];
      
        $sessionManager = new SessionManager($chatId);
        $sessionManager->saveSession($session);
        sendMessage($chatId, "شماره موبایل به درستی وارد نشده است! لطفا مجدد وارد نمایید.");
        exit();
    }

    $session['data']['mobile'] = $text;
    sendMessage($chatId, "با این حساب چند نفر به صورت همزمان می خواهند وصل شوند؟ (این حساب چندکاربره می باشد؟)");
    return $session;
}

function askUsers($chatId, $session, $text = null) {
    $text = convertToEnglishNumbers($text);

    //here we need validate input users number must not more than 5 and must not be 
    if(!isValidDigit($text)){

        // Start a new session based on the callback
        $session = ['action' => 'create_user',  'step' => 2, 'data' => ['mobile' => $session['data']['mobile']]];
      
        $sessionManager = new SessionManager($chatId);
        $sessionManager->saveSession($session);
        sendMessage($chatId, "حداکثر تعداد کاربر مجاز ۷ عدد می باشد. لطفا مجدد تعداد کاربران را وارد نمایید.");
        exit();
    }
    $session['data']['users'] = $text;
    $session_file = SESSION_FILE."/$chatId"."_user_login_data.json";
    $state  = json_decode(file_get_contents($session_file), true);

    //here we check if reseller exist or not
    $login_response = file_get_contents(MAIN_SERVER_URL . "createnewrandomuser/" . API_TOKEN . '/'.$state['token'] . "/" . urlencode($session['data']['mobile']) . "/" . urlencode($text));
    $result = json_decode($login_response, true);
    if ($result['result'] == 1) {

        //here we delete session
        $sessionManager = new SessionManager($chatId);
        $loginManager   = new LoginManager($chatId);
        $sessionManager->clearSession();

        //here we update array sooner
        $state['token']         = $state['token'];
        $state['wallet']        = $result['wallet'];
        $state['users_count']   = $result['users_count'];
        $state['login_at']      = time();
        $state['logged_in']     = true;
        $loginManager->saveLogin($state);

        sendMessage($chatId, "تبریک کاربر جدید ساخته شد.");
        sendMessage($chatId, "مبلغ ".$result['cost']." تومان بابت ساخت کاربر جدید از حساب شما کسر شد."."\n"." مانده اعتبار کیف پول شما:"."\n".
    $result['wallet']." تومان می باشد."."\n"."تشکر از همراهی شما");
        sendMessage($chatId, $result['new_user_data']);
        showMainMenu($chatId);
    }else{
        $sessionManager = new SessionManager($chatId);
        $sessionManager->clearSession();

        sendMessage($chatId,$result['message']);
        showMainMenu($chatId);
    }
}

function convertToEnglishNumbers($string) {
    $farsiArabicNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    
    return str_replace($farsiArabicNumbers, $englishNumbers, $string);
}


function askUsersOrMnobile($chatId, $session, $text = null) {
    $loginManager = new LoginManager($chatId);
    $loginList = $loginManager->getLogin();
    $text = convertToEnglishNumbers($text);

    get_users_list($chatId,$loginList['token'],0,$text);
}

function get_users_list($chat_id,$token,$page,$search){
    // Fetch users from the server

    //here we give specific string to search
    if($search == ''){
        $search = 'emptyStringIsPassed';
    }

    $usersPerPage = 5;
    $users_response = file_get_contents(MAIN_SERVER_URL . "getresellerusers/" . API_TOKEN . '/' . urlencode($token) . "/$page"."/$search");
    $users_result = json_decode($users_response, true);
    if ($users_result['result'] == 1) {
        $users = $users_result['users'];
        $totalPages = ceil($users_result['total_users'] / $usersPerPage);

        $keyboard = [];
        foreach ($users as $user) {
            if($user['status'] != 0){
                //here we need to calculate left days
                // Get the current timestamp
                $currentTimestamp = time();

                // Calculate the difference in seconds
                $differenceInSeconds = $user['expire_at'] - $currentTimestamp;

                // Calculate the number of days left and round up
                $daysLeft = ceil($differenceInSeconds / (60 * 60 * 24)); // 60 seconds * 60 minutes * 24 hours
            }else{
                $daysLeft = "inactive!";
            }
            $keyboard[] = [
                ['text' => $user['username']." ⏱: $daysLeft", 'callback_data' => "user:{$user['id']}"],
            ];
        }

        if ($page > 0) {
            $keyboard[] = [['text' => '⏪ Previous', 'callback_data' => "user_mgmt:" . ($page - 1)]];
        }
        if ($page < $totalPages - 1) {
            $keyboard[] = [['text' => '⏩ Next', 'callback_data' => "user_mgmt:" . ($page + 1)]];
        }

        if(count($keyboard) == 0){
            sendKeyboard($chat_id, "اطلاعاتی یافت نشد!", $keyboard);
        }else{
            sendKeyboard($chat_id, "لیست کاربران:", $keyboard);
        }
    } else {
        sendMessage($chat_id, "خطا در دریافت لیست کاربران.");
    }
}