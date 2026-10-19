<?php


function is_win()
{
  return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}

function is_linux()
{
  return stripos(PHP_OS, 'Linux') !== false;
}

function is_cli()
{
  return php_sapi_name() === 'cli' || defined('STDIN');
}

function is_chrome()
{
  if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;
  $ua = $_SERVER['HTTP_USER_AGENT'];
  if (stripos($ua, 'Edge') !== false) return false;
  if (stripos($ua, 'OPR') !== false) return false;
  return stripos($ua, 'Chrome') !== false || stripos($ua, 'CriOS') !== false;
}

function is_get()
{
  return $_SERVER['REQUEST_METHOD'] == 'GET';
}

function is_post()
{
  return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function is_ajax()
{
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}
