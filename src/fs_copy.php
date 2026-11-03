<?php

/**
 * Copy a file or directory recursively.
 *
 * @param string $source The source path.
 * @param string $destination The destination path.
 * @return void
 */
if (!function_exists('fs_copy')) {
  function fs_copy($source, $destination)
  {
    if (is_dir($source)) {
      // Create the destination directory if it doesn't exist
      if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
      }
    }

    // Copy each file and subdirectory recursively
    $items = scandir($source);
    foreach ($items as $item) {
      if ($item === '.' || $item === '..') {
        continue;
      }
      fs_copy("$source/$item", "$destination/$item");
    }
  }
}
