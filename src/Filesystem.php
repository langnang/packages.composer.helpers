<?php

class FileSystem
{
  public static function createDirectory($path)
  {
    if (!is_dir($path)) {
      mkdir($path, 0777, true);
    }
  }

  public static function deleteDirectory($path)
  {
    if (!is_dir($path)) {
      return;
    }
    $files = array_diff(scandir($path), ['.', '..']);
    foreach ($files as $file) {
      $filePath = $path . DIRECTORY_SEPARATOR . $file;
      if (is_dir($filePath)) {
        self::deleteDirectory($filePath);
      } else {
        unlink($filePath);
      }
    }
    rmdir($path);
  }

  public static function copyDirectory($source, $destination)
  {
    if (!is_dir($source)) {
      return;
    }
    self::createDirectory($destination);
    $files = array_diff(scandir($source), ['.', '..']);
    foreach ($files as $file) {
      $sourcePath = $source . DIRECTORY_SEPARATOR . $file;
      $destinationPath = $destination . DIRECTORY_SEPARATOR . $file;
      if (is_dir($sourcePath)) {
        self::copyDirectory($sourcePath, $destinationPath);
      } else {
        copy($sourcePath, $destinationPath);
      }
    }
  }
}
