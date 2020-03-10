<?php
//Строка для подтверждения адреса сервера из настроек Callback API
$confirmation_token = 'b13a85c3';

//Ключ доступа сообщества
$token = 'd7e09c5a7832dd2e37c5425a5f7e19340040957af117bf48bd24866aa91c9e3a14a38d9604a77ba556079';
define('VK_API_VERSION', '5.67'); //Используемая версия API
define('VK_API_ENDPOINT', "https://api.vk.com/method/");

$user_id = 188585016;//160492927;
//затем с помощью users.get получаем данные об авторе
$user_info = json_decode(file_get_contents("https://api.vk.com/method/users.get?user_ids={$user_id}&access_token={$token}&v=5.0"));

//и извлекаем из ответа его имя
$user_name = $user_info->response[0]->first_name;

//С помощью messages.send отправляем ответное сообщение
$request_params = array(
    'message' => "Сейчас первая секунда твоего дня рождения(по Москве)! Поздравляю, хоть и не могу в полной мере воспринять значимость данного праздника, я ведь всего лишь бот",
    'user_id' => $user_id,
    'access_token' => $token,
    'v' => '5.0'
);

$get_params = http_build_query($request_params);

sleep(1581282001-time());
file_get_contents('https://api.vk.com/method/messages.send?'. $get_params);
