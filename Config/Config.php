<?php

class Config{

    /**
     * Here I defined variables as const in this class.
     * Note: php has two phase to run: 
     * 1-compiling phase 2-execution phase
     * Compiling phase: classes are defined and saved in memory and prepare php for main execution.
     * Execution phase: varibles outside a class and other things start to execute.
     * When we define constant variables inside a class with const , they are saved in memory in first phase.
     * but when you define variables , they are executed and saved into php database. php must search for them
     * Hence variables in class takes shorter time to run and php already knows them.
     */

    // Telegram bot token
    const BOT_TOKEN         = 'REPLACE_YOUR_BOT_TOKEN';

    // For example your bot is communicating with API , send this token to your app for authentication
    const API_TOKEN         = 'THIS_TOKEN_IS_FOR_SECURITY_COMMUNICATION_BETWEEN_BOT_AND_API';

    // Define your main server's API URL
    const MAIN_SERVER_URL   = '_REPLACE_YOUR_API_ADDRESS_THAT_BOT_CONNECT_TO';

    // Define telegram API address
    const API_URL           = 'https://api.telegram.org/bot' . self::BOT_TOKEN . "/";

    // Define session file address in server
    const SESSION_FILE      = '/var/www/telegram/Storage/sessions';

    //here we define log file path
    const LOG_FILE_PATH     = __DIR__ . '/../Storage/sessions/debug.log';

    // Number of users per page
    const USER_PER_PAGE     = 5;

    // Database connection data
    // const HOST       = "localhost"; // Change to your database host
    // const DBNAME     = "your_database"; // Change to your database name
    // const USERNAME   = "your_username"; // Change to your database username
    // const PASSWORD   = "your_password"; // Change to your database password

    // Image generation path
    const IMAGE_PATH_SAMPLE     = __DIR__ . '/../Assets/images/config.jpeg'; // Replace with the path to your image
    const IMAGE_PATH_SAVE       = __DIR__ . '/../Storage/user_images/';
    const FONT_PATH_IMAGE       = __DIR__ . '/../Assets/fonts/Raleway-Medium.ttf';
}