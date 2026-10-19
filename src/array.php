<?php

/**
 * https://learnku.com/docs/laravel/11.x/helpersmd/16679
 */


/**
 * 判断给定值是否可作为数组访问
 */
function array_accessible() {}
function array_add() {}
function array_collapse() {}
function array_crossJoin() {}
function array_divide() {}
function array_dot() {}
function array_except() {}
function array_exists() {}
function array_first() {}
function array_flatten() {}
function array_forget() {}
if (!function_exists('array_get')) {
  function array_get($array, $key, $default = null)
  {
    if (is_null($key)) {
      return $array;
    }

    if (isset($array[$key])) {
      return $array[$key];
    }

    foreach (explode('.', $key) as $segment) {
      if (!is_array($array) || !array_key_exists($segment, $array)) {
        return $default;
      }
      $array = $array[$segment];
    }

    return $array;
  }
}

if (!function_exists('array_has')) {
  function array_has($array, $key)
  {
    if (!is_array($array) || is_null($key)) {
      return false;
    }
    if (isset($array[$key])) {
      return true;
    }
    $keys = explode('.', $key);
    foreach ($keys as $segment) {
      if (!is_array($array) || !array_key_exists($segment, $array)) {
        return false;
      } else {
        return array_has($array[$segment], implode('.', array_slice($keys, 1)));
      }
    }
    return false;
  }
}
function array_hasAny() {}
function array_isAssoc() {}
function array_isList() {}
function array_join() {}
function array_keyBy() {}
function array_last($array)
{
  return array_slice($array, -1)[0];
}
// function array_map() {}
function array_mapSpread() {}
function array_mapWithKeys() {}
// function array_merge() {}
function array_only() {}
function array_pluck() {}
function array_prepend() {}
function array_prependKeysWith() {}
function array_pull() {}

if (!function_exists('array_query')) {
  /**
   * 将数组转换为查询字符串
   * @param array $array 要转换的数组
   * @param array $mergeData 要合并的额外数据
   */
  function array_query($array, $mergeData = [])
  {
    $array = array_merge($array, $mergeData);
    return http_build_query($array);
  }
}
function array_random() {}
if (!function_exists('array_set')) {
  function array_set(array &$array, $key, $value, $separator = '.')
  {
    $keys = explode($separator, $key);

    while (count($keys) > 1) {
      $key = array_shift($keys);
      if (!isset($array[$key]) || !is_array($array[$key])) {
        $array[$key] = [];
      }
      $array = &$array[$key];
    }
    $array[array_shift($keys)] = $value;
  }
}
function array_shuffle() {}
function array_sort() {}
function array_sortDesc() {}
function array_sortRecursive() {}
function array_sortRecursiveDesc() {}
function array_take() {}
function array_toCssClasses() {}
function array_toCssStyles() {}
function array_undot() {}
function array_where() {}
function array_whereNotNull() {}
function array_wrap() {}
// function array_fill() {}
function array_head() {}
