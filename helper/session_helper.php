<?php

//here we have session manager class
class SessionManager {
    private $sessionFile;

    public function __construct($chatId) {
        $this->sessionFile = "/var/www/telegram/sessions/$chatId.json";
    }

    public function getSession() {
        if (file_exists($this->sessionFile)) {
            return json_decode(file_get_contents($this->sessionFile), true);
        }
        return null;
    }

    public function saveSession($data) {
        file_put_contents($this->sessionFile, json_encode($data));
    }

    public function clearSession() {
        if (file_exists($this->sessionFile)) {
            unlink($this->sessionFile);
        }
    }
}