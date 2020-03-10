<?php
define('VK_API_VERSION', '5.67'); //Используемая версия API
define('VK_API_ENDPOINT', "https://api.vk.com/method/");
define('CALLBACK_API_CONFIRMATION_TOKEN', 'b13a85c3'); //Строка для подтверждения адреса сервера из настроек Callback API
define('VK_API_ACCESS_TOKEN', 'd7e09c5a7832dd2e37c5425a5f7e19340040957af117bf48bd24866aa91c9e3a14a38d9604a77ba556079'); //Ключ доступа сообщества

//Функция для вызова произвольного метода API
function _vkApi_call($method, $params = array()) {
$params['access_token'] = VK_API_ACCESS_TOKEN;
$params['v'] = VK_API_VERSION;
$url = VK_API_ENDPOINT.$method.'?'.http_build_query($params);
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$json = curl_exec($curl);
curl_close($curl);
$response = json_decode($json, true);
return 'ok';
}

//Функция для вызова messages.send
function vkApi_messagesSend($peer_id, $message, $attachments = array()) {
return _vkApi_call('messages.send', array(
'peer_id' => $peer_id,
'message' => $message,
'attachment' => implode(',', $attachments)
));
}

vkApi_messagesSend(160492927, 'Hello world!');
echo 'ok';
