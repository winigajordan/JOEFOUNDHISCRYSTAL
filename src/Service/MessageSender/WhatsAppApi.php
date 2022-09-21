<?php

namespace App\Service\MessageSender;

class WhatsAppApi
{

    private $apiKey = "9x1KRAPTf393";


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

    public function img($number,$link, $message){

        $url =
            "http://api.textmebot.com/send.php?".
            "recipient=".$number.
            "&apikey=".$this->apiKey.
            "&text=".$message.
            "&file=".$link.
            "&json=yes";
        //dd($url);
        $context = stream_context_create(['http' => ['ignore_errors' => true]]);
        $data = file_get_contents($url, false, $context);


    }
}