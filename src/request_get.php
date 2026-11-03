<?php

function request_get($url, $headers = [])
{
  $ch = request_init($url, 'GET', null, $headers);
  $response = curl_exec($ch);
  curl_close($ch);
  return $response;
}
