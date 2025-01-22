<?php

/**
 * Function to convert farsi number to english
 */
function convertToEnglishNumbers($string) {
    $farsiArabicNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    
    return str_replace($farsiArabicNumbers, $englishNumbers, $string);
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