<?php
function is_win()
{
  return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
}
