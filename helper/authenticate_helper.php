<?php

/**
 * Function to check if user is logged in
 */
function check_user_is_logged_in($chat_id) {
    $state_file = SESSION_FILE."/$chat_id"."_user_login_data.json";
    if(file_exists($state_file)){
        $state = file_exists($state_file) ? json_decode(file_get_contents($state_file), true) : ['logged_in' => false];
        if (!$state['logged_in'] || (time() - intval($state['login_at']) > 1209600)) {
            //here we delete session file
            if (file_exists($state_file)) unlink($state_file);
            return false;
        }else{
            return true;
        }
    }else{
        return false;
    }
}

/**
 * Function to check if mobile number is valid or not
 */
function isValidMobileNumber($mobileNumber) {
    // Check if the input matches the pattern for an 11-digit number starting with '09'
    return preg_match('/^09\d{9}$/', $mobileNumber) === 1;
}

/**
 * Function to check input digit is correct
 */
function isValidDigit($input) {
    // Check if the input matches the pattern for a single digit between 1 and 7
    return preg_match('/^[1-7]$/', $input) === 1;
}
