<?php

class InputController {

    private $messageClaseSample;
    private $sessionClassSample;
    private $authClassSample;
    private $imageClassSample;

    /**
     * Construct
     */
    function __construct(MessageService $messageClaseSample, SessionHelper $sessionClassSample, AuthService $authClassSample, ImageHelper $imageClassSample)
    {
        $this->messageClaseSample  = $messageClaseSample;
        $this->sessionClassSample  = $sessionClassSample;
        $this->authClassSample     = $authClassSample;
        $this->imageClassSample    = $imageClassSample;
    }

    /**
     * Define your methods here to react user input
     */
    function askUsername() {
        $this->sendMessage->sendMessage("خوش آمدید! لطفا نام کاربری خود را وارد کنید.");
    }
}