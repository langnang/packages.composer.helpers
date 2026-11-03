<?php

function request_init($url, $method = 'GET', $data = null, $headers = [])
{
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

  if ($data) {
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
  }

  if (!empty($headers)) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  }

  return $ch;
}
