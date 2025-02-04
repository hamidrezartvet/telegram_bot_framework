<?php

    class ImageHelper{

        /**
         * Function to create image
         */
        function createImage($username = "newUser", $password = "newPass", $user_id = "user_id")
        {

            if (!file_exists(Config::IMAGE_PATH_SAMPLE)) {
                return "Image not found.";
            }

            $image = imagecreatefromjpeg(Config::IMAGE_PATH_SAMPLE);

            if (!$image) {
                return "Failed to load image.";
            }

            // Set text properties
            $textColor = imagecolorallocate($image, 255, 255, 255); // White color
            $fontSize = 21;

            // Define positions for username and password
            $xUsername = 100; // X-coordinate for username
            $yUsername = 745; // Y-coordinate for username
            $xPassword = 100; // X-coordinate for password
            $yPassword = 940; // Y-coordinate for password

            // Add text to the image
            imagettftext($image, $fontSize, 0, $xUsername, $yUsername, $textColor, Config::FONT_PATH_IMAGE, $username);
            imagettftext($image, $fontSize, 0, $xPassword, $yPassword, $textColor, Config::FONT_PATH_IMAGE, $password);

            // Save the image
            if (!imagejpeg($image, Config::IMAGE_PATH_SAVE . $user_id.'_config.jpeg')) {
                "Failed to save image.";
            }
            
            // Free memory
            imagedestroy($image);
        }
    }
?>