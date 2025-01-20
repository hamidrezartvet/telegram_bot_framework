<?php

//here we have session manager class
class LoginManager {
    private $loginFile;

    public function __construct($chat_id) {
        $this->loginFile = "/var/www/telegram/sessions/$chat_id"."_user_login_data.json";
    }

    public function getLogin() {
        if (file_exists($this->loginFile)) {
            return json_decode(file_get_contents($this->loginFile), true);
        }
        return null;
    }

    public function saveLogin($data) {
        file_put_contents($this->loginFile, json_encode($data));
    }

    public function clearLogin() {
        if (file_exists($this->loginFile)) {
            unlink($this->loginFile);
        }
    }
}