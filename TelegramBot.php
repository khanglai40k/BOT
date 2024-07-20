<?php
class Bot
{
    private $bot = NULL;

    function __construct($token)
    {
        $this->bot = $token;
    }

    public function sendMessage($id, $text, $reply = "")
    {
        return $this->GET('sendMessage?chat_id=' . $id . '&text=' . urlencode($text) . '&reply_to_message_id=' . $reply . '&allow_sending_without_reply=true')["ok"];
    }

    public function sendPhoto($params)
    {
        $url = "https://api.telegram.org/bot" . $this->bot . "/sendPhoto";
        return $this->sendRequest($url, $params);
    }

    public function sendMessageWithKeyboard($chat_id, $text, $keyboard)
    {
        $url = "https://api.telegram.org/bot" . $this->bot . "/sendMessage";
        $params = [
            'chat_id' => $chat_id,
            'text' => $text,
            'reply_markup' => json_encode(['keyboard' => $keyboard, 'resize_keyboard' => true, 'one_time_keyboard' => true])
        ];
        return $this->sendRequest($url, $params);
    }

    private function GET($param)
    {
        $url = "https://api.telegram.org/bot" . $this->bot . "/" . $param;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $resp = curl_exec($curl);
        curl_close($curl);
        return json_decode($resp, 1);
    }

    private function sendRequest($url, $params)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response, true);
    }
}
