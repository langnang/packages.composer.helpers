<?php

function is_linux()
{
  return stripos(PHP_OS, 'Linux') !== false;
}
