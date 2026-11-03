<?php


function request_post($url, $data = null, $headers = [])
{
  $ch = request_init($url, 'POST', $data, $headers);
  $response = curl_exec($ch);
  curl_close($ch);
  return $response;
}
