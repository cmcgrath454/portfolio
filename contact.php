<?php
include('../../../secrets/secrets.php');

$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];
$token = $_POST['token-response'];
$ipaddress = $_SERVER['REMOTE_ADDR'];

$data = array('secret' => $portfolioSecretKey, 'response' => $token, 'remoteip' => $ipaddress);
$options = array(
    'http' => array(
        'header' => "Content-type: application/x-www-form-urlencoded\r\n",
        'method' => 'POST',
        'content' => http_build_query($data),
    ),
);

$context = stream_context_create($options);
$verify_response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
$response_data = json_decode($verify_response, true);


$body = "Name: {$name}\r\nEmail: {$email}\r\nMessage: {$message}";

$recaptcha_response_str = print_r($response_data, true);

$debug = false;

if ($debug) {
    echo print_r($body, true), '<br>', print_r($response_data, true);
    exit();
}

if (!$response_data['success']) {
    error_log("{$message} \n {$recaptcha_response_str}");
    echo "
    <script LANGUAGE='JavaScript'>
        window.alert('Sorry, we\'ve encountered an issue with Google Recaptcha. Please try again later.');
        window.location.href = '/';
    </script>
    ";
    exit();
}

if ($response_data['score'] <= 0.4) {
    error_log("{$message} \n {$recaptcha_response_str}");
    echo "
    <script LANGUAGE='JavaScript'>
        window.alert('Sorry, Google Recaptcha has identified this as spam with an interaction score of {$response_data['score']} / 1.0. This message won\'t be sent.');
        window.location.href = '/';
    </script>
    ";
    exit();
}


if (mail('cmcgrath454@gmail.com', 'Portfolio Contact Request', wordwrap($body, 70, "\r\n"))) {
    echo "
    <script LANGUAGE='JavaScript'>
        window.alert('Thanks for your message. I\'ll contact you shortly with a response.');
        window.location.href = '/';
    </script>
    ";
    exit();
} else {
    error_log("{$message} \n {$recaptcha_response_str}");
    echo "
    <script LANGUAGE='JavaScript'>
        window.alert('Sorry, but an error occurred trying to send your message. Please wait and try again later.');
        window.location.href = '/';
    </script>
    ";
    exit();
}

?>