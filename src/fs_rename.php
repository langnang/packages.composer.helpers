<?php

/**
 * Rename a file or directory.
 *
 * @param string $old_path The old path.
 * @param string $new_path The new path.
 * @return bool True if the rename is successful, false otherwise.
 */
function fs_rename($old_path, $new_path)
{
  return rename($old_path, $new_path);
}
