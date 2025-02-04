<?php

//here we have session manager class
class SessionHelper {
    private $sessionFile;

    /**
     * Constructor 
     */
    public function __construct($chat_id) {
        $this->sessionFile = __DIR__ . "/../Storage/sessions/$chat_id.json";
    }

    /**
     * Get session data 
     */
    public function getSession() {
        if (file_exists($this->sessionFile)) {
            return json_decode(file_get_contents($this->sessionFile), true);
        }
        return null;
    }

    /**
     * Save session data
     */
    public function saveSession($data) {
        file_put_contents($this->sessionFile, json_encode($data));
    }

    /**
     * Clear session data
     */
    public function clearSession() {
        if (file_exists($this->sessionFile)) {
            unlink($this->sessionFile);
        }
    }
}