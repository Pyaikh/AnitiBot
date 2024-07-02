

<?php

error_reporting(E_ALL);

$json_file = rtrim($_SERVER['DOCUMENT_ROOT'], "/ ") . "/blacklist.json";

function load_json_file($json_file) {
    if (file_exists($json_file)) {
        $json_data = file_get_contents($json_file);
        return json_decode($json_data, true);
    } else {
        die ("<font color='red'><b>Error!</b></font> No access to file: $json_file. Please create this file or change file permissions.");
    }
}

function block_ip($ip) {
    die("<font color='red'><b>Error!</b></font> Access denied for IP: $ip.");
}

$blacklist = load_json_file($json_file);
$user_ip = $_SERVER['REMOTE_ADDR'];

if (in_array($user_ip, $blacklist)) {
    block_ip($user_ip);
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<h1>123</h1>
</body>
</html>
