<?php


function to_bytes($bytes, $from = 'KB', $to = 'GB')
{
  $units = array(
    'KB' => 1,
    'MB' => 1024,
    'GB' => 1024 * 1024,
  );
  $fromValue = $units[$from];
  $toValue = $units[$to];
  return ($bytes / $fromValue) * $toValue;
}

function filesize_format($num)
{
  $p = 0;
  $format = 'bytes';
  if ($num > 0 && $num < 1024) {
    $p = 0;
    return number_format($num) . ' ' . $format;
  }
  if ($num >= 1024 && $num < pow(1024, 2)) {
    $p = 1;
    $format = 'KB';
  }
  if ($num >= pow(1024, 2) && $num < pow(1024, 3)) {
    $p = 2;
    $format = 'MB';
  }
  if ($num >= pow(1024, 3) && $num < pow(1024, 4)) {
    $p = 3;
    $format = 'GB';
  }
  if ($num >= pow(1024, 4) && $num < pow(1024, 5)) {
    $p = 3;
    $format = 'TB';
  }
  $num /= pow(1024, $p);
  return number_format($num, 2) . ' ' . $format;
}

/**
 * @param string $pattern
 * The pattern. No tilde expansion or parameter substitution is done.
 * @param int $flags
 * Valid flags: 
 *   GLOB_MARK - Adds a slash to each directory returned 
 *   GLOB_NOSORT - Return files as they appear in the directory (no sorting). When this flag is not used, the pathnames are sorted alphabetically
 *   GLOB_NOCHECK - Return the search pattern if no files matching it were found GLOB_NOESCAPE - Backslashes do not quote metacharacters 
 *   GLOB_BRACE - Expands {a,b,c} to match 'a', 'b', or 'c'
 *   GLOB_ONLYDIR - Return only directory entries which match the pattern
 *   GLOB_ERR - Stop on read errors (like unreadable directories), by default errors are ignored.
 * @return array|false an array containing the matched files/directories, an empty array if no file matched or false on error.
 * On some systems it is impossible to distinguish between empty match and an error.
 */
function deep_glob($dir, $parttern = null, $flat = 0)
{
  $dir_paths = [];
  $file_paths = [];
  // dir
  foreach (glob($dir . "/*", GLOB_ONLYDIR) as $dirname) {
    $dir_paths[] = $dirname;
    $dir_paths = array_merge($dir_paths, deep_glob($dirname, null, GLOB_ONLYDIR));
  }
  // foreach
  // file
  return $dir_paths;
}

function deep_scandir() {}
