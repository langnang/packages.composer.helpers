<?php

/**
 * Get the real path of a file or directory.
 *
 * @param string $path The path to check.
 * @return string The real path, or false if the path does not exist.
 */
function fs_path($path)
{
  return realpath($path);
}
