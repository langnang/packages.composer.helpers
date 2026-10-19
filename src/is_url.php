<?php
function is_url($url)
{
  //$pattern = '/^http(s)?:\\/\\/.+/';
  $pattern = "/\b(([\w-]+:\/\/?|www[.])[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|\/)))/";
  if (preg_match($pattern, $url)) {
    return true;
  }
  return false;
}
