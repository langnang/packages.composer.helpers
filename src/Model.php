<?php

class Model
{
  public static $table_name;
  public static $primary_keys;
  public static $unique_keys;

  function toArrray()
  {
    $arr = [];
    foreach ($this as $key => $value) {
      $arr[$key] = $value;
    }
    return $arr;
  }
}
