<?php

//here we have session manager class
class LogerHelper {

    /**
     * Function to set log
     */
    public static function log($message) {
        $timestamp = date('[Y-m-d H:i:s]');
        file_put_contents(Config::LOG_FILE_PATH, "$timestamp $message\n", FILE_APPEND);
    }

    /**
     * Function to set error
     */
    public static function error($message) {
        $timestamp = date('[Y-m-d H:i:s]');
        file_put_contents(Config::LOG_FILE_PATH, "$timestamp [ERROR] $message\n", FILE_APPEND);
    }
}