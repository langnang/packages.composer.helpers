<?php

function fs_write($path, $data)
{
  return file_put_contents($path, $data);
}
