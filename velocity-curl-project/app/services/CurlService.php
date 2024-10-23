<?php
namespace App\Services;

use Exception;
use App\Models\Message;

class CurlService
{
    public function sendMessageData($url, Message $message)
    {
        $data = $this->transformMessageData($message);
        $this->makePostRequest($url, $data);
    }

    private function transformMessageData(Message $message)
    {
        //Apenas para caso futuramente seja necessario modificar mensagem antes do envio
        return $message->getData();
    }

    public function makePostRequest($url, $data)
    {
        $json_data = json_encode($data);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($json_data))
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            throw new Exception('cURL error: ' . curl_error($ch));
        }

        curl_close($ch);

        return $response;
    }
}