<?php

namespace App\Service\MessageSender;

class WhatsAppApi
{

    public function sender(){
        $url = 'https://graph.facebook.com/v13.0/102069372646783/messages';
       $curl = curl_init($url);


        $authorization = "Bearer EAAGVpZApLcP4BADhphrP3s6RWaBMD8qJkXl5zWKeaWRnUVoE1f6BjZAaAXzp5s4MmsYINazR4DAGi3HS8CnhzJvP0jrYaSQELF0V7SK9D8SqtsrqUH3PMcgFb3VGFRN5whEuL6oblZByvFCZAPSsahUmZCsTwNNZB5xTeTY6ZCZCPnESEXMJZC1z5tItNthc9FLu1KtrtWvnw3GxKGQ7Fa3rY";
        $data = array(
            "messaging_product"=>"whatsapp",
            "to"=>"221772570206",
            "type"=>"template",
            "template"=>array(
                "name"=>"hello_world",
                "language"=>array(
                    "code"=>"en_US"
                )
            ),
        );

        //dd(json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        //curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));


        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        dd($resp);
    }
}