<?php
function is_web()
{
  /**
   * Determine if current execution context is a web (HTTP) request.
   *
   * Strategy (mixed):
   *  - Prefer SAPI/STDIN detection: treat `cli` and `phpdbg` as CLI.
   *  - Treat `cli-server` (built-in web server) as web.
   *  - If `$_SERVER['REQUEST_METHOD']` exists, consider it a web request.
   *  - Fallback to legacy User-Agent heuristic for backward compatibility.
   *
   * @return bool
   */
  $sapi = php_sapi_name();
  if (defined('STDIN')) {
    return false;
  }
  // Pure CLI and phpdbg are CLI environments. The built-in server 'cli-server' should be treated as web.
  if ($sapi === 'cli' || $sapi === 'phpdbg') {
    return false;
  }
  // If a request method is present, it's almost certainly a web request.
  if (isset($_SERVER['REQUEST_METHOD'])) {
    return true;
  }
  // Fallback: preserve original User-Agent heuristic for compatibility
  if (!isset($_SERVER['HTTP_USER_AGENT'])) return false;
  $ua = $_SERVER['HTTP_USER_AGENT'];
  if (stripos($ua, 'Edge') !== false) return false;
  if (stripos($ua, 'OPR') !== false) return false;
  return stripos($ua, 'Chrome') !== false || stripos($ua, 'CriOS') !== false;
}

function is_get()
{
  return $_SERVER['REQUEST_METHOD'] == 'GET';
}

function is_post()
{
  return $_SERVER['REQUEST_METHOD'] == 'POST';
}

function is_ajax()
{
  return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

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
/**
 * 判读表达式中括号是否对应匹配
 *
 * @param mixed $expstr
 * @return bool
 */
if (!function_exists('is_valid')) {
  function is_valid($expstr)
  {
    $temp = array();
    for ($i = 0; $i < strlen($expstr); $i++) {
      $ch = $expstr[$i];
      switch ($ch) {
        case '(':
          array_push($temp, '(');
          break;
        case ')':
          if (empty($temp) || array_pop($temp) != '(') {
            return "缺少左括号（";
          }
      }
    }
    return empty($temp);
  }
}
/**
 * 
 */
if (!function_exists('data_transform')) {
  function data_transform($values, $transformations)
  {
    $vars = [
      "__METHOD__" => __METHOD__,
      '00:values' => $values,
      '00:transformations' => $transformations
    ];
    $return = [];
    $vars['01:funcs'] =   $funcs = explode('|', $transformations);
    // var_dump($funcs);
    foreach ($funcs as $func) {
      $var = ['00:func' => $func];
      // 开始标签
      $var['01:startTag'] = $startTag = strpos($func, "(");
      // 结束标签
      $var['01:endTag'] = $endTag = strripos($func, ")");
      // 函数
      $var['02:latestStr'] =  $latestStr = substr($func,  $endTag === false ? 0 : $endTag + 1);
      // 参数
      $var['02:funcName'] = $funcName = $startTag == false ? $func : substr($func, 0, $startTag);

      $argStr = null;
      if ($startTag && $endTag) {
        $var['02:argStr'] =  $argStr = substr($func, $startTag === false ? 0 :  $startTag + 1, ($endTag ?? 0) - $startTag - 1);
      }

      $var['02:args'] =  $args = preg_split('/,/', $argStr);
      // var_dump($var);

      $isValid = true;
      // 参数数组长度大于0
      if (count($args) > 0) {
        foreach ($args as $arg) {
          if (!is_valid($arg)) {
            $isValid = false;
            break;
          }
        }
      }
      // var_dump($args);

      if (!$isValid) $args = array($argStr);
      // var_dump($args);
      // var_dump($args);

      // 判断分隔符前是否有匹配对应的括号
      $splitTag = strpos($argStr, ",");

      foreach ($args as $index => $arg) {
        $startTag = strpos($arg, "(");
        $endTag = strripos($arg, ")");
        if ($startTag !== false && $endTag !== false) {
          $args[$index] = call_method($values, $arg);
        }
      }
      // var_dump($args);
      // $splits = preg_split("/\(|,|\)/", $filter);
      // 去除最后一个空元素
      // var_dump($splits);
      // exit();
      if ($args[count($args) - 1] == "") {
        $args = array_slice($args, 0, -1);
      }
      // var_dump($func, $args);
      // $func = $splits[0];
      // $args = array_slice($splits, 1);
      // 获取数组中指定参数的值
      $keys = [];
      if (strpos($latestStr, ".") === 0) {
        $keys = preg_split("/\./", $latestStr);
        // 去除第一个空白元素
        $keys = array_slice($keys, 1);
        if ($keys[count($keys) - 1] == "") {
          $keys = array_slice($keys, 0, -1);
        }
        // $args = array_slice($args, -1);
      }
      // var_dump($args);
      // if (function_exists($func)) {
      //   if (in_array($func, ['array_search', 'array_map', 'array_filter'])) {
      //     if (is_array($values)) {
      //       foreach ($values as $index => $value) {
      //         $args[0] = $value;
      //         $values[$index] = call_user_func($func, ...$args);
      //       }
      //     }
      //     $values = call_user_func($func, $args[0], $values, ...array_slice($args, 1));
      //   } else {
      //     // 第一个参数是变量
      //     $values = call_user_func($func, $values, ...$args);
      //   }
      //   // } else if (method_exists($this, $func)) {
      //   // $values = $this->{$func}($values, ...$args);
      // } else if (is_array($values) && isset($values[$func])) {
      //   // 获取数组的参数值
      //   $values = $values[$func];
      // } else {
      //   // var_dump($func);
      //   // var_dump($values);
      // }
      $values = call_method($values, $funcName, ...$args);
      if (count($keys) > 0) {
        $values = array_reduce($keys, function ($total, $key) {
          return isset($total[$key]) ? $total[$key] : null;
        }, $values);
      }
      $vars[] = $var;
    }
    // var_dump($vars);
    // var_dump($values);
    return $values;
  }
}


function call_method($values, $funcName, ...$args)
{
  // var_dump($funcName, $args);
  if (!function_exists($funcName)) return $values;
  $return = [];
  $flag = 'array';
  if (!is_array($values)) {
    $values = array($values);
    $flag = 'string';
  }
  if (in_array($funcName, ['implode',])) {
    $return[]  = call_user_func($funcName, $values, ...$args);
  } else {
    foreach ($values as $index => $value) {
      if (in_array($funcName, ['array_search', 'array_map', 'array_filter'])) {
        $return[] = call_user_func($funcName, $args[0], $value, ...array_slice($args, 1));
      } else {
        // 第一个参数是变量
        // var_dump(" call_user_func($funcName, $value, " . implode(', ', $args) . ")");
        $return[]  = call_user_func($funcName, $value, ...$args);
      }
    }
  }

  // var_dump($return);
  return $flag == 'string' ? $return[0] : $return;
}

function fill_url($url, $collect_url)
{
  $url         = trim($url);
  $collect_url = trim($collect_url);

  // 排除JavaScript的连接
  //if (strpos($url, "javascript:") !== false)
  if (preg_match("@^(mailto|javascript:|#|'|\")@i", $url) || $url == '') {
    return false;
  }
  // 排除没有被解析成功的语言标签
  if (substr($url, 0, 3) == '<%=' or substr($url, 0, 1) == '{' or substr($url, 0, 2) == ' {')
  // if(substr($url, 0, 3) == '<%=')
  {
    return false;
  }

  $parse_url = @parse_url($collect_url);
  if (empty($parse_url['scheme']) || empty($parse_url['host'])) {
    return false;
  }
  // 过滤mailto、tel、sms、wechat、sinaweibo、weixin等协议
  if (! in_array($parse_url['scheme'], array('http', 'https'))) {
    return false;
  }
  $scheme        = $parse_url['scheme'];
  $domain        = $parse_url['host'];
  $path          = empty($parse_url['path']) ? '' : $parse_url['path'];
  $base_url_path = $domain . $path;
  $base_url_path = preg_replace("/\/([^\/]*)\.(.*)$/", '/', $base_url_path);
  $base_url_path = preg_replace("/\/$/", '', $base_url_path);
  $i             = $path_step             = 0;
  $dstr          = $pstr          = '';
  $pos           = strpos($url, '#');
  if ($pos > 0) {
    // 去掉#和后面的字符串
    $url = substr($url, 0, $pos);
  }

  // 修正url格式为 //www.jd.com/111.html 为正确的http
  if (substr($url, 0, 2) == '//') {
    $url = preg_replace('/^\/\//iu', '', $url);
  }
  // /1234.html
  elseif ($url[0] == '/') {
    $url = $domain . $url;
  }
  // ./1234.html、../1234.html 这种类型的
  elseif ($url[0] == '.') {
    if (!isset($url[2])) {
      return false;
    } else {
      $urls = explode('/', $url);
      foreach ($urls as $u) {
        if ($u == '..') {
          $path_step++;
        }
        // 遇到 ., 不知道为什么不直接写$u == '.', 貌似一样的
        else if ($i < count($urls) - 1) {
          $dstr .= $urls[$i] . '/';
        } else {
          $dstr .= $urls[$i];
        }
        $i++;
      }
      $urls = explode('/', $base_url_path);
      if (count($urls) <= $path_step) {
        return false;
      } else {
        $pstr = '';
        for ($i = 0; $i < count($urls) - $path_step; $i++) {
          $pstr .= $urls[$i] . '/';
        }
        $url = $pstr . $dstr;
      }
    }
  } else {
    if (strtolower(substr($url, 0, 7)) == 'http://') {
      $url    = preg_replace('#^http://#i', '', $url);
      $scheme = 'http';
    } else if (strtolower(substr($url, 0, 8)) == 'https://') {
      $url = preg_replace('#^https://#i', '', $url);
      $scheme = "https";
    }
    // 相对路径，像 1111.html 这种
    else {
      $arr = explode("/", $base_url_path);
      // 去掉空值
      $arr = array_filter($arr);
      $base_url_path = implode("/", $arr);
      $url = $base_url_path . '/' . $url;
    }
  }
  // 两个 / 或以上的替换成一个 /
  $url = preg_replace('/\/{1,}/i', '/', $url);
  $url = $scheme . '://' . $url;

  $parse_url = @parse_url($url);
  $domain    = empty($parse_url['host']) ? $domain : $parse_url['host'];
  // 如果host不为空, 判断是不是要爬取的域名
  if (! empty($parse_url['host'])) {
    //2018-1-3 通配所有域名
    // if (empty(self::$configs['domains']) or self::$configs['domains'][0] == '*') {
    //   return $url;
    // }
    //排除非域名下的url以提高爬取速度
    // if (!in_array($parse_url['host'], self::$configs['domains'])) {
    //   return false;
    // }
  }

  return $url;
}

function link_compress($link)
{
  if (empty($link['url_type'])) {
    unset($link['url_type']);
  }

  if (empty($link['method']) || strtolower($link['method']) == 'get') {
    unset($link['method']);
  }

  if (empty($link['headers'])) {
    unset($link['headers']);
  }

  if (empty($link['params'])) {
    unset($link['params']);
  }

  if (empty($link['context_data'])) {
    unset($link['context_data']);
  }

  if (empty($link['proxy'])) {
    unset($link['proxy']);
  }

  if (empty($link['try_num'])) {
    unset($link['try_num']);
  }

  if (empty($link['max_try'])) {
    unset($link['max_try']);
  }

  if (empty($link['depth'])) {
    unset($link['depth']);
  }
  //$json = json_encode($link);
  //$json = gzdeflate($json);
  return $link;
}
