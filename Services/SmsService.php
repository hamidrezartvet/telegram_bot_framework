<?php

    class SmsService{        

        /**
         * Send sms - can be used sigle or multiple sms
         * Very important note: for sending sms in each part make a 0/5 second delay.
         * In ordert to prevent error.
         */
        function sendSms($message_array,$receiver_array,$template_id,$delay){

            $params = [
                'token'         => Config::sms_parsianwebco_panel_token,
                'TemplateID'    => $template_id,
                'MessageVars'   => $message_array, 
                'Receiver'      => $receiver_array, 
                'Delay'         => $delay 
            ];

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://api.parsianwebco.ir/webservice-send-sms/send');
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'content-type' => 'application/x-www-form-urlencoded'
            ));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
            $response = curl_exec($curl);
            curl_close($curl);
        }

        /**
         * Get sms status - this method can get one sms status
         */
        function smsStatus($sms_id){
            $params = [
                'token' => Config::sms_parsianwebco_panel_token,
                'smsID' => $sms_id
            ];
            $curl = curl_init();
            curl_setopt(
                $curl,
                CURLOPT_URL, 
                'https://api.parsianwebco.ir/webservice-check-sms/get'
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'content-type' => 'application/x-www-form-urlencoded'
            ));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
            $response = curl_exec($curl);
            curl_close($curl);
        }

        /**
         * Get credit
         */
        function getCredit(){

            $params = [
                'token' => Config::sms_parsianwebco_panel_token,
            ];
            $curl = curl_init();
            curl_setopt(
                $curl, 
                CURLOPT_URL, 
                'https://api.parsianwebco.ir/webservice-get-credit/get' 
            );
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'content-type' => 'application/x-www-form-urlencoded'
            ));
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($params));
            $response = curl_exec($curl);
            curl_close($curl);
        }
    }