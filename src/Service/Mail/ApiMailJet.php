<?php
/*
This call sends a message based on a template.
*/
namespace App\Service\Mail;


use Mailjet\Client;
use Mailjet\Resources;

class ApiMailJet
{

    private $api_key = "f5c2029d0b7eaaaf3e35dcb28a409c53";
    private $api_key_privat = "3b0f58a74088b3a329d0a0316792719c";

    public function send($emailTo, $link, $password){
        $mj = new Client($this->api_key, $this->api_key_privat,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "fcklph@gmail.com",
                        'Name' => "test nom"
                    ],
                    'To' => [
                        [
                            'Email' => $emailTo,
                            'Name' => ''
                        ]
                    ],
                    'TemplateID' => 4141884 ,
                    'TemplateLanguage' => true,
                    'Variables' => [
                        'link' => $link,
                        'password' => $password
                    ]
                ]
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        //$response->success() && var_dump($response->getData());
        //dd($response->getData());
    }

}