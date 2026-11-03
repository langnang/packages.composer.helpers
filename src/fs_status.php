<?php

/**
 * Get the status of a file or directory.
 *
 * @param string $path The path to check.
 * @return string 'file', 'directory', or 'not found'.
 */
function fs_status($path)
{
  if (is_dir($path)) {
    return 'directory';
  } elseif (is_file($path)) {
    return 'file';
  } else {
    return 'not found';
  }
}
