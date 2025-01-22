<?php

/**
 * Function to check if user is logged in
 */
function check_user_is_logged_in($loginManager) {
    $check_log_in = $loginManager->getLogin();
    if($check_log_in != null){
        if (!$check_log_in['logged_in'] || (time() - intval($check_log_in['login_at']) > 1209600)) {
            //here we delete session file
            $loginManager->clearLogin();
            return false;
        }else{
            return true;
        }
    }else{
        return false;
    }
}
