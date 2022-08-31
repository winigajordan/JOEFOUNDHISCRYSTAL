<?php
/*
This call sends a message based on a template.
*/
namespace App\Service\Mail;


use Mailjet\Client;
use Mailjet\Resources;

class ApiMailJet
{

    private $api_key = "429492cd53254d9a81b4982846766008";
    private $api_key_privat = "1e22cae3eeb67cb70a081d6bc2ed78b3";

    public function send($emailTo, $link, $password){
        $mj = new Client($this->api_key, $this->api_key_privat,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "winigajordan@gmail.com",
                        'Name' => "Marriage de Joe & Christal "
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


    public function physique($emailTo, $link){
        $mj = new Client($this->api_key, $this->api_key_privat,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "winigajordan@gmail.com",
                        'Name' => "Marriage de Joe & Christal "
                    ],
                    'To' => [
                        [
                            'Email' => $emailTo,
                            'Name' => ''
                        ]
                    ],
                    'TemplateID' => 4154878 ,
                    'TemplateLanguage' => true,
                    'Variables' => [
                        'link' => $link,
                    ]
                ]
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        //$response->success() && var_dump($response->getData());
        //dd($response->getData());
    }

}