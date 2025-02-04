<?php

//here we have session manager class
class AuthService {
    private $loginFile;

    /**
     * Constructor 
     */
    public function __construct($chat_id) {
        $this->loginFile = __DIR__ . "/../Storage/sessions/$chat_id"."_user_login_data.json";
    }


    /**
     * Check if user is logged in 
     */
    public function getLogin() {
        if (file_exists($this->loginFile)) {
            return json_decode(file_get_contents($this->loginFile), true);
        }
        return null;
    }


    /**
     * Save user logged in
     */
    public function saveLogin($data) {
        file_put_contents($this->loginFile, json_encode($data));
    }

    /**
     * Destroy loggin
     */
    public function clearLogin() {
        if (file_exists($this->loginFile)) {
            unlink($this->loginFile);
        }
    }
}