<?php
$email = strtolower(trim($_POST['email']));

/**
 * cUrl Request
 * 
 * Rate limiting
 *
 * Requests to the breaches and pastes APIs are limited 
 * to one per every 1500 milliseconds each from any given IP address.
 * 
 * https://haveibeenpwned.com/API/v2#RateLimiting
 * 
 * Rate limit and burst are handle through Nginx.
 */
$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => 'https://haveibeenpwned.com/api/breachedaccount/' . $email,
    CURLOPT_USERAGENT => 'Conoscerelinux',
    CURLOPT_HTTPHEADER => [
        'api-version: 2',
    ],
    CURLOPT_RETURNTRANSFER => TRUE,
]);

$output = curl_exec($ch);
$info = curl_getinfo($ch);
curl_close($ch);

/**
 * Response
 */
header('Content-type:application/json;charset=utf-8');

if ($info['http_code'] == '429') {
    echo json_encode(['result' => 'Too many request']);
}elseif ($info['http_code'] == '404') {
    echo json_encode(['result' => FALSE]);
}else {
    echo $output;
}