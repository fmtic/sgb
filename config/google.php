<?php
require_once '../vendor/autoload.php'; // composer require google/apiclient

session_start();

$client = new Google_Client();
$client->setClientId('SEU_CLIENT_ID');
$client->setClientSecret('SEU_CLIENT_SECRET');
$client->setRedirectUri('http://SEU_DOMINIO/view/login.php'); // URL de retorno
$client->addScope('email');
$client->addScope('profile');
