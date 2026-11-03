<?php

/**
 * Create a directory if it doesn't exist.
 *
 * @param string $path The path to create.
 * @param int $mode The permissions for the directory (default: 0755).
 * @param bool $recursive Whether to create directories recursively (default: true).
 * @return void
 */
function fs_create($path, $mode = 0755, $recursive = true)
{
  if (!is_dir($path)) {
    mkdir($path, $mode, $recursive);
  }
}
