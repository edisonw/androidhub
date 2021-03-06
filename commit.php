<?php

error_reporting(E_ALL);
$emailaddress = 'androidhub@intel.com';

define(DEBUG_MODE, TRUE);

if (!$_SERVER || $_SERVER['REQUEST_METHOD'] != "POST") {
  die ("go away\n\n");
}

$json = stream_get_contents(detectRequestBody());
$body = formatData(json_decode($json));

$headers = 'From: androidhub@intel.com' . "\r\n" .
    'Reply-To: androidhub@intel.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

if (!email($emailaddress, "Someone wants to commit to the Android Hub!", $body, $headers)) {
  header('HTTP/1.1 400 Internal Server Error');
  echo json_encode(array(
    'message' => "An error occured sending email. Try again later."
  ));
} else {
  header('HTTP/1.1 200 OK');
  echo json_encode(array(
    'message' => "Successfully sent email. We look forward to working with you!"
  ));
}

function formatData($data) {
  $emailBody = array();;

  foreach ($data as $key => $value) {
    $emailBody[] = $key . ': ' . $value;
  }

  return implode("\r\n", $emailBody);
}

function email($to, $subject, $body, $headers) {
  if (DEBUG_MODE) {
    return TRUE;
  }

  return mail($to, $subject, $body, $headers);
}

function detectRequestBody() {
    $rawInput = fopen('php://input', 'r');
    $tempStream = fopen('php://temp', 'r+');
    stream_copy_to_stream($rawInput, $tempStream);
    rewind($tempStream);

    return $tempStream;
}
