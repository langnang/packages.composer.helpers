<?php

/**
 * @name Redirect to Index
 * @description 重定向至首页
 */

function redirect_to_index()
{


  $uri = parse_url($_SERVER['REQUEST_URI'])['path'];

  // var_dump($uri);

  $uri = str_replace('//', '/', $uri);

  // var_dump($uri);

  if ($uri != '/' && $uri != '/index.php') {
    $exp_uri = explode('/', $uri);
    // var_dump($exp_uri);
    $i = sizeof($exp_uri);
    // var_dump($i);

    while ($i > 0) {
      $imp_uri = implode("/", array_slice($exp_uri, 0, $i));
      // var_dump($imp_uri);
      if (file_exists($path = __DIR__ . $imp_uri . '/index.php')) {
        // var_dump($path);
        // require_once $path;
        header("Location:" . $imp_uri . '/index.php/' . implode("/", array_slice($exp_uri, $i)));
        break;
      }
      $i--;
    }
  }
}
