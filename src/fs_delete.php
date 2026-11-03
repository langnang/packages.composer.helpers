<?php

/**
 * Delete a file or directory recursively.
 *
 * @param string $path The path to delete.
 * @return void
 */
function fs_delete($path)
{
  if (is_dir($path)) {
    // Recursively delete the directory and its contents
    $items = scandir($path);
    foreach ($items as $item) {
      if ($item === '.' || $item === '..') {
        continue;
      }
      fs_delete("$path/$item");
    }
    rmdir($path);
  } else {
    unlink($path);
  }
}
