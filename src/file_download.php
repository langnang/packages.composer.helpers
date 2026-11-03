<?php

function file_download($url, $destination)
{
  // Initialize a cURL session
  $ch = curl_init($url);

  // Open a file for writing
  $fp = fopen($destination, 'w');

  // Set cURL options
  curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_FAILONERROR, true);

  // Execute the cURL request
  $result = curl_exec($ch);

  // Check for errors
  if ($result === false) {
    fclose($fp);
    curl_close($ch);
    return false; // Download failed
  }

  // Close the file and cURL session
  fclose($fp);
  curl_close($ch);

  return true; // Download successful
}
