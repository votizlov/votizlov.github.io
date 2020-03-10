<?php

define('VK_API_VERSION', '5.67'); //Используемая версия API
define('VK_API_ENDPOINT', 'https://api.vk.com/method/');
define('CALLBACK_API_EVENT_CONFIRMATION', 'confirmation');
define('CALLBACK_API_EVENT_MESSAGE_NEW', 'message_new');
define('CALLBACK_API_CONFIRMATION_TOKEN', 'b13a85c3'); //Строка для подтверждения адреса сервера из настроек Callback API
define('VK_API_ACCESS_TOKEN', 'd7e09c5a7832dd2e37c5425a5f7e19340040957af117bf48bd24866aa91c9e3a14a38d9604a77ba556079'); //Ключ доступа сообщества

//require_once 'config.php';
//require_once 'global.php';

//require_once 'api/vk_api.php';
//require_once 'api/yandex_api.php';

//require_once 'bot/bot.php';

if (!isset($_REQUEST)) {
  exit;
}

callback_handleEvent();

function callback_handleEvent() {
  $event = _callback_getEvent();

  try {
    switch ($event['type']) {
      //Подтверждение сервера
      case CALLBACK_API_EVENT_CONFIRMATION:
        _callback_handleConfirmation();
        break;

      //Получение нового сообщения
      case CALLBACK_API_EVENT_MESSAGE_NEW:
        _callback_handleMessageNew($event['object']);
        break;

      default:
        _callback_response('Unsupported event');
        break;
    }
  } catch (Exception $e) {
    //log_error($e);
  }

  _callback_okResponse();
}

function _callback_getEvent() {
  return json_decode(file_get_contents('php://input'), true);
}

function _callback_handleConfirmation() {
  _callback_response(CALLBACK_API_CONFIRMATION_TOKEN);
}

function _callback_handleMessageNew($data) {
  $user_id = $data['user_id'];
  //bot_sendMessage($user_id);
    $users_get_response = vkApi_usersGet($user_id);
    $user = array_pop($users_get_response);
    $msg = "Привет,";// {$user['first_name']}!";
    vkApi_messagesSend($user_id, $msg);
  _callback_okResponse();
}

function _callback_okResponse() {
  _callback_response('ok');
}

function _callback_response($data) {
  echo $data;
  exit();
}

function vkApi_messagesSend($peer_id, $message, $attachments = array()) {
    return _vkApi_call('messages.send', array(
        'peer_id'    => $peer_id,
        'message'    => $message,
        'attachment' => implode(',', $attachments)
    ));
}

function vkApi_usersGet($user_id) {
    return _vkApi_call('users.get', array(
        'user_id' => $user_id,
    ));
}

function _vkApi_call($method, $params = array()) {
    $params['access_token'] = VK_API_ACCESS_TOKEN;
    $params['v'] = VK_API_VERSION;

    $query = http_build_query($params);
    $url = VK_API_ENDPOINT.$method.'?'.$query;

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    $json = curl_exec($curl);
    $error = curl_error($curl);
    if ($error) {
        //log_error($error);
        throw new Exception("Failed {$method} request");
    }

    curl_close($curl);

    $response = json_decode($json, true);
    if (!$response || !isset($response['response'])) {
        //log_error($json);
        throw new Exception("Invalid response for {$method} request");
    }

    return $response['response'];
}


