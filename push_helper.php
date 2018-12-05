<?php defined('BASEPATH') OR exit('No direct script access allowed');

	//add firebase_api config in config/config.php file
    function send_push($token_or_topic,$payload,$expire = 0) {

        $api	=	config_item('firebase_api');

        $fields = array
        (
            'content_available'	=>	TRUE,
            'data'				=> $payload
        );

        if ($expire != 0)
            $fields['time_to_live'] =   $expire;

        $token_info =   get_token_info($token_or_topic);

        if ($token_info == FALSE) {
            $fields['to'] = "/topics/$token_or_topic";
            $fields['notification'] = $payload;
        }
        else {

            $fields['registration_ids'] =  array($token_or_topic);

            if (strtolower($token_info->platform) == 'ios')
                $fields['notification'] = $payload;
        }

        $headers = array
        (
            'Authorization: key=' . $api,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt( $ch,CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send' );
        curl_setopt( $ch,CURLOPT_POST, true );
        curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
        $result = curl_exec($ch );
        curl_close( $ch );
        return $result;

    }

    function get_token_info($token) {

        $api_key    =   config_item('firebase_api');

        $opts = [
            "http" => [
                "method" => "GET",
                "header" => "Accept-language: en\r\n" .
                    "Authorization: key=$api_key\r\n"
            ]
        ];

        $context = stream_context_create($opts);

        $result = @file_get_contents('https://iid.googleapis.com/iid/info/'.$token, false, $context);

        if (json_decode($result) === FALSE)
            return FALSE;
        else
            return json_decode($result);

    }