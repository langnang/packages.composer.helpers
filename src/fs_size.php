<?php

/**
 * Get the size of a file or directory.
 *
 * @param string $path The path to check.
 * @return int The size in bytes, or 0 if the path does not exist.
 */
function fs_size($path)
{
  if (is_dir($path)) {
    $size = 0;
    $items = scandir($path);
    foreach ($items as $item) {
      if ($item === '.' || $item === '..') {
        continue;
      }
      $size += fs_size("$path/$item");
    }
    return $size;
  } elseif (is_file($path)) {
    return filesize($path);
  } else {
    return 0; // Path does not exist
  }
}
