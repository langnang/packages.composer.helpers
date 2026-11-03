<?php

function request_delete($url, $headers = [])
{
  $ch = request_init($url, 'DELETE', null, $headers);
  $response = curl_exec($ch);
  curl_close($ch);
  return $response;
}
