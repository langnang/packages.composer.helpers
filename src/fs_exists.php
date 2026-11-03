<?php

/**
 * Check if a file or directory exists.
 *
 * @param string $path The path to check.
 * @return bool True if the path exists, false otherwise.
 */
function fs_exists($path)
{
  if (function_exists('file_exists')) {
    return file_exists($path);
  } else {
    return is_file($path) || is_dir($path);
  }
}
