<?php

//here we have session manager class
class SessionManager {
    private $sessionFile;

    public function __construct($chatId) {
        $this->sessionFile = "/var/www/telegram/sessions/$chatId.json";
    }

    public function getSession() {
        if (file_exists($this->sessionFile)) {
            // Logger::log("Loading session for chat ID: $this->sessionFile");
            return json_decode(file_get_contents($this->sessionFile), true);
        }
        // Logger::log("No session found for chat ID: $this->sessionFile");
        return null;
    }

    public function saveSession($data) {
        // Logger::log("Saving session for chat ID: $this->sessionFile with data: " . json_encode($data));
        file_put_contents($this->sessionFile, json_encode($data));
    }

    public function clearSession() {
        if (file_exists($this->sessionFile)) {
            // Logger::log("Clearing session for chat ID: $this->sessionFile");
            unlink($this->sessionFile);
        }
    }
}