<?php

/**
 * Move a file or directory to a new location.
 *
 * @param string $source The source path.
 * @param string $destination The destination path.
 * @return void
 * @throws Exception If the source does not exist or the move fails.
 */
function fs_move($source, $destination)
{
  if (!file_exists($source)) {
    throw new Exception("Source file or directory does not exist: $source");
  }

  if (file_exists($destination)) {
    throw new Exception("Destination already exists: $destination");
  }

  if (!rename($source, $destination)) {
    throw new Exception("Failed to move $source to $destination");
  }
}
