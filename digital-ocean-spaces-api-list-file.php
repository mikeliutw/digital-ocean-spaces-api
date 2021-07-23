<?php
$aws_access_key_id = "--";
$aws_secret_access_key = "--";

// Bucket
$bucket_name = '--';

$aws_region = 'sgp1';
$host_name = $bucket_name . '.sgp1.digitaloceanspaces.com';

$aws_service_name = 's3';

$timestamp = gmdate('Ymd\THis\Z');
$date = gmdate('Ymd');

$request_headers = array();
$request_headers['x-amz-date'] = $timestamp;
$request_headers['Host'] = $host_name;
$request_headers['x-amz-content-sha256'] = "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855";
//hash('sha256', '');
// Sort it in ascending order
ksort($request_headers);

// Canonical headers
$canonical_headers = [];
foreach ($request_headers as $key => $value) {
    $canonical_headers[] = strtolower($key) . ":" . $value;
}
$canonical_headers = implode("\n", $canonical_headers);

// Signed headers
$signed_headers = [];
foreach ($request_headers as $key => $value) {
    $signed_headers[] = strtolower($key);
}
$signed_headers = implode(";", $signed_headers);

// Cannonical request
$canonical_request = [];
$canonical_request[] = "GET";
$canonical_request[] = "/";
$canonical_request[] = urlencode("prefix") . "=" . urlencode("user/images/");
$canonical_request[] = $canonical_headers;
$canonical_request[] = "";
$canonical_request[] = $signed_headers;
$canonical_request[] = "e3b0c44298fc1c149afbf4c8996fb92427ae41e4649b934ca495991b7852b855";
$canonical_request = implode("\n", $canonical_request);
$hashed_canonical_request = hash('sha256', $canonical_request);

// AWS Scope
$scope = [];
$scope[] = $date;
$scope[] = $aws_region;
$scope[] = $aws_service_name;
$scope[] = "aws4_request";

// String to sign
$string_to_sign = [];
$string_to_sign[] = "AWS4-HMAC-SHA256";
$string_to_sign[] = $timestamp;
$string_to_sign[] = implode('/', $scope);
$string_to_sign[] = $hashed_canonical_request;
$string_to_sign = implode("\n", $string_to_sign);

// Signing key
$kSecret = 'AWS4' . $aws_secret_access_key;
$kDate = hash_hmac('sha256', $date, $kSecret, true);
$kRegion = hash_hmac('sha256', $aws_region, $kDate, true);
$kService = hash_hmac('sha256', $aws_service_name, $kRegion, true);
$kSigning = hash_hmac('sha256', 'aws4_request', $kService, true);

// Signature
$signature = hash_hmac('sha256', $string_to_sign, $kSigning);

// Authorization
$authorization = [
    'Credential=' . $aws_access_key_id . '/' . implode('/', $scope),
    'SignedHeaders=' . $signed_headers,
    'Signature=' . $signature,
];
$authorization = 'AWS4-HMAC-SHA256' . ' ' . implode(',', $authorization);

// Curl headers
$curl_headers = ['Authorization: ' . $authorization];
foreach ($request_headers as $key => $value) {
    $curl_headers[] = $key . ": " . $value;
}

$url = 'https://' . $host_name . '/?prefix=user/images/';

// echo $url;
$ch = curl_init($url);
//curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $curl_headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);

$xml = simplexml_load_string($response, 'SimpleXMLElement', LIBXML_NOCDATA);
//print_r($xml->Name);
// echo count($xml->Contents);
//  print_r($xml->Contents);
foreach ($xml->Contents as $item) {
    echo $item->Key . "<br />";
}

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
if ($http_code != 200) {
    exit('Error : Failed to list');
}
