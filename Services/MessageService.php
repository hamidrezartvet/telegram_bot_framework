<?php
class MessageService{
    private $chat_id;

    /**
     * Contructor
     */
    function __construct($chat_id){
        $this->chat_id = $chat_id;
    }
    
    /**
     * Method to Send a message with inline keyboard
     */
    function sendKeyboard($text, $keyboard,$message_id) {
        
        if($message_id == 0){
            
            $encoded_keyboard   = json_encode(['inline_keyboard' => $keyboard]);
            $url                = 
            Config::API_URL .
            "sendMessage?chat_id=".$this->chat_id.
            "&text=". urlencode($text) .
            "&reply_markup=" . $encoded_keyboard;
            file_get_contents($url);
        }else{

            $encoded_keyboard   = json_encode(['inline_keyboard' => $keyboard]);
            $url                = 
            Config::API_URL .
            "editMessageText?chat_id=".$this->chat_id.
            "&message_id=". $message_id .
            "&text=". urlencode($text) .
            "&reply_markup=" . $encoded_keyboard;
            file_get_contents($url);
        }
    }

    /**
     * Method to send a message to a user via Telegram
     */
    function sendMessage($text) {

        $url = Config::API_URL . "sendMessage?chat_id=".$this->chat_id."&text=" . urlencode($text);
        file_get_contents($url);
    }

    /**
     * Method to send photo to telegram bot
     */
    function sendPhotoToTelegram($caption = "test",$user_id){

        $apiUrl                     = Config::API_URL . "sendPhoto";
        $sample_Config_image_Path   = "/var/www/telegram/Storage/user_images/".$user_id."_config.jpeg";

        // Check if the image is a URL or a local file
        if (filter_var($sample_Config_image_Path, FILTER_VALIDATE_URL)) {

            // If it's a URL, send it directly
            $url = $apiUrl."?chat_id=".$this->chat_id."&photo=" . urlencode($sample_Config_image_Path) . "&caption=" . urlencode($caption);
            return file_get_contents($url);
        } else {

            // If it's a local file, use cURL
            $postFields = [
                'chat_id'   => $this->chat_id,
                'photo'     => new CURLFile(realpath($sample_Config_image_Path)), // Ensure correct file path
                'caption'   => $caption
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);
            return $response;
        }
    }

    /**
     * Method to send multiple photo to telegram bot
     */
    function sendMultiplePhotosToTelegram($imagePaths, $captions = [])
    {
        $apiUrl = Config::API_URL . "sendMediaGroup";

        // Prepare media array
        $media = [];
        $postFields = ['chat_id' => $this->chat_id];

        foreach ($imagePaths as $index => $imagePath) {
            $mediaItem = [
                'type' => 'photo',
                'media' => filter_var($imagePath, FILTER_VALIDATE_URL) ? $imagePath : 'attach://' . basename($imagePath),
            ];
            
            // Add caption if available
            if (!empty($captions[$index])) {
                $mediaItem['caption'] = $captions[$index];
                $mediaItem['parse_mode'] = 'HTML'; // Optional, for formatted captions
            }

            $media[] = $mediaItem;
        }

        $postFields['media'] = json_encode($media);

        // Attach local files if needed
        $attachments = [];
        foreach ($imagePaths as $imagePath) {
            if (!filter_var($imagePath, FILTER_VALIDATE_URL)) {
                $attachments[basename($imagePath)] = new CURLFile(realpath($imagePath));
            }
        }

        // Initialize cURL session
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, array_merge($postFields, $attachments));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
     * Method to show main menu
     */
    function showMainMenu() {

        //here we get wallet and users count value
        $chat_id = $this->chat_id;
        $session_file = Config::SESSION_FILE."/".$chat_id."_user_login_data.json";
        $wallet = 0;
        $users  = 0;
        if(file_exists($session_file)){
            $welcome_text = "لطفا یکی از گزینه های زیر را انتخاب کنید.";
            $state  = json_decode(file_get_contents($session_file), true);
            $wallet = $state['wallet'];
            $users  = $state['users_count'];
                
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'جستجو کاربر'                    , 'callback_data' => 'search_user'],
                        ['text' => 'کاربر جدید'                     , 'callback_data' => 'create_user'],
                    ],
                    [
                        ['text' => 'لیست کاربران'.'('.$users.')'    , 'callback_data' => 'user_mgmt'],
                    ],
                    [
                        ['text' => 'کیف پول'.'('.$wallet.' تومان)'  , 'callback_data' => 'wallet'],
    
                    ],
                    [
                        ['text' => 'خروج'                           , 'callback_data' => 'logout']
                    ]
                ]
            ];
        }else{
    
            //here we define welcome message
            $welcome_text = "خوش آمدید! برای استفاده از ربات ابتدا باید وارد شوید. روی دکمه زیر کلیک کنید.";
            $keyboard = [
                'inline_keyboard' => [
                    [
                        ['text' => 'ورود'                           , 'callback_data' => 'login']
                    ]
                ]
            ];
        }
    
        $encoded_keyboard = json_encode($keyboard);
    
        $url = Config::API_URL . "sendMessage?chat_id=$this->chat_id&text=" . urlencode($welcome_text) . "&reply_markup=" . $encoded_keyboard;
        file_get_contents($url);
    }
}