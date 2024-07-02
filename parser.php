<?php

error_reporting(E_ALL);


$file1_url = "http://myip.ms/files/blacklist/htaccess/latest_blacklist.txt";
$file2_url = "http://myip.ms/files/blacklist/htaccess/latest_blacklist_users_submitted.txt";


$json_file = rtrim($_SERVER['DOCUMENT_ROOT'], "/ ") . "/blacklist.json";

$exclusions_file = rtrim($_SERVER['DOCUMENT_ROOT'], "/ ") . "/exclusions.json";

function fetch_blacklist($url) {
    $data = @file_get_contents($url);
    if ($data === false || !$data) die ("<font color='red'><b>Error!</b></font> No access to file: $url");
    return $data;
}

function load_exclusions($exclusions_file) {
    if (file_exists($exclusions_file)) {
        $json_data = file_get_contents($exclusions_file);
        return json_decode($json_data, true);
    } else {
        return [];
    }
}

function filter_exclusions($data, $exclusions) {
    $filtered_data = [];
    $lines = explode("\n", $data);
    foreach ($lines as $line) {
        if (strpos($line, 'deny from') !== false) {
            $ip = trim(str_replace('deny from', '', $line));
            if (!in_array($ip, $exclusions) && filter_var($ip, FILTER_VALIDATE_IP)) {
                $filtered_data[] = $ip;
            }
        }
    }
    return $filtered_data;
}

function update_json_file($json_file, $blacklist_data) {
    $json_data = json_encode($blacklist_data, JSON_PRETTY_PRINT);
    $res = file_put_contents($json_file, $json_data);
    if ($res === false) die ("<font color='red'><b>Error!</b></font> Cannot write blacklist IP to file: $json_file. Please change file permissions.");
}

$data1 = fetch_blacklist($file1_url);
$data2 = fetch_blacklist($file2_url);

$exclusions = load_exclusions($exclusions_file);

$data1_filtered = filter_exclusions($data1, $exclusions);
$data2_filtered = filter_exclusions($data2, $exclusions);

$blacklist_data = array_merge($data1_filtered, $data2_filtered);

update_json_file($json_file, $blacklist_data);

echo "<font color='green'><b>Blacklist successfully updated.</b></font><br>Date: " . date("r");

?>
