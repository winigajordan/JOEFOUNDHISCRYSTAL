<?php

namespace App\Service\MessageSender;

class WhatsAppApi
{

    private $apiKey = "7b6DRN3EBBoB";


    public function text($number, $message){

        $url =
            "http://api.textmebot.com/send.php?".
            "recipient=".$number.
            "&apikey=".$this->apiKey.
            "&text=".$message."&json=yes";
        //dd($url);
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $data = file_get_contents($url, false, $context);
        //dd($data);

    }
}