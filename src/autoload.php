<?php

require_once __DIR__ . '/Def.php';

foreach (glob(__DIR__ . '/*.php') as $file) {
  require_once $file;
}
