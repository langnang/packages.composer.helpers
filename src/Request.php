<?php


class Request
{
  function init() {}
  function get($url, $fields = array(), $allow_redirects = true, $cert = NULL)
  {
    self::init();
    return $this->request($url, 'get', $fields, NULL, $allow_redirects, $cert);
  }
  function post() {}
  function put() {}
  function delete() {}
  function head() {}
  function options() {}
  function patch() {}
  static function request($url, $method = 'GET', $fields = array(), $files = array(), $allow_redirects = true, $cert = NULL)
  {
    $method = strtoupper($method);
    if (!self::_is_url($url)) {
      self::$error = "You have requested URL ({$url}) is not a valid HTTP address";
      return false;
    }

    // 如果是 get 方式，直接拼凑一个 url 出来
    if ($method == 'GET' && !empty($fields)) {
      $url = $url . (strpos($url, '?') === false ? '?' : '&') . http_build_query($fields);
    }

    $parse_url = parse_url($url);
    if (empty($parse_url) || empty($parse_url['host']) || !in_array($parse_url['scheme'], array('http', 'https'))) {
      self::$error = "No connection adapters were found for '{$url}'";
      return false;
    }
    $scheme = $parse_url['scheme'];
    $domain = $parse_url['host'];

    // 随机绑定 hosts，做负载均衡
    if (self::$hosts) {
      if (isset(self::$hosts[$domain])) {
        $hosts = self::$hosts[$domain];
        $key = rand(0, count($hosts) - 1);
        $ip = $hosts[$key];
        $url = str_replace($domain, $ip, $url);
        self::$rawheaders['Host'] = $domain;
      }
    }

    curl_setopt(self::$ch, CURLOPT_URL, $url);

    if ($method != 'GET') {
      // 如果是 post 方式
      if ($method == 'POST') {
        //curl_setopt( self::$ch, CURLOPT_POST, true );
        $tmpheaders = array_change_key_case(self::$rawheaders, CASE_LOWER);
        // 有些RESTful服务只接受JSON形态的数据
        // CURLOPT_POST会把上傳的文件类型设为 multipart/form-data
        // 把CURLOPT_POSTFIELDS的内容按multipart/form-data 的形式编码
        // CURLOPT_CUSTOMREQUEST可以按指定内容上传
        if (isset($tmpheaders['content-type']) && $tmpheaders['content-type'] == 'application/json') {
          curl_setopt(self::$ch, CURLOPT_CUSTOMREQUEST, $method);
        } else {
          curl_setopt(self::$ch, CURLOPT_POST, true);
        }

        $file_fields = array();
        if (!empty($files)) {
          foreach ($files as $postname => $file) {
            $filepath = realpath($file);
            // 如果文件不存在
            if (!file_exists($filepath)) {
              continue;
            }

            $filename = basename($filepath);
            $type = self::get_mimetype($filepath);
            $file_fields[$postname] = curl_file_create($filepath, $type, $filename);
            // curl -F "name=seatle&file=@/absolute/path/to/image.png" htt://localhost/uploadfile.php
            //$cfile = '@'.realpath($filename).";type=".$type.";filename=".$filename;
          }
        }
      } else {
        self::$rawheaders['X-HTTP-Method-Override'] = $method;
        curl_setopt(self::$ch, CURLOPT_CUSTOMREQUEST, $method);
      }

      if ($method == 'POST') {
        // 不是上传文件的，用http_build_query, 能实现更好的兼容性，更小的请求数据包
        if (empty($file_fields)) {
          // post方式
          if (is_array($fields)) {
            $fields = http_build_query($fields);
          }
        } else {
          // 有post数据
          if (is_array($fields) && !empty($fields)) {
            // 某些server可能会有问题
            $fields = array_merge($fields, $file_fields);
          } else {
            $fields = $file_fields;
          }
        }

        // 不能直接传数组，不知道是什么Bug，会非常慢
        curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $fields);
      }
    }

    $cookies = self::get_cookies();
    $domain_cookies = self::get_cookies($domain);
    $cookies =  array_merge($cookies, $domain_cookies);
    // 是否设置了cookie
    if (!empty($cookies)) {
      foreach ($cookies as $key => $value) {
        $cookie_arr[] = $key . '=' . $value;
      }
      $cookies = implode('; ', $cookie_arr);
      curl_setopt(self::$ch, CURLOPT_COOKIE, $cookies);
    }

    if (!empty(self::$useragents)) {
      $key = rand(0, count(self::$useragents) - 1);
      self::$rawheaders['User-Agent'] = self::$useragents[$key];
    }

    if (!empty(self::$client_ips)) {
      $key                                 = rand(0, count(self::$client_ips) - 1);
      self::$rawheaders['CLIENT-IP']       = self::$client_ips[$key];
      self::$rawheaders['X-FORWARDED-FOR'] = self::$client_ips[$key];
    }

    if (self::$rawheaders) {
      $http_headers = array();
      foreach (self::$rawheaders as $k => $v) {
        $http_headers[] = $k . ': ' . $v;
      }
      curl_setopt(self::$ch, CURLOPT_HTTPHEADER, $http_headers);
    }

    curl_setopt(self::$ch, CURLOPT_ENCODING, 'gzip');

    // 关闭验证
    if ($scheme == 'https') {
      curl_setopt(self::$ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt(self::$ch, CURLOPT_SSL_VERIFYHOST, false);
    }

    if (self::$proxies) {
      $key = rand(0, count(self::$proxies) - 1);
      $proxy = self::$proxies[$key];
      curl_setopt(self::$ch, CURLOPT_PROXY, $proxy);
    }

    // header + body，header 里面有 cookie
    curl_setopt(self::$ch, CURLOPT_HEADER, true);
    // 请求跳转后的内容
    if ($allow_redirects) {
      curl_setopt(self::$ch, CURLOPT_FOLLOWLOCATION, true);
    }

    self::$raw = curl_exec(self::$ch);
    // 真实url
    //$location = curl_getinfo( self::$ch, CURLINFO_EFFECTIVE_URL);
    self::$info = curl_getinfo(self::$ch);
    //print_r(self::$info);
    self::$status_code = self::$info['http_code'];
    if (self::$raw === false) {
      self::$error = 'Curl error: ' . curl_error(self::$ch);
      //trigger_error(self::$error, E_USER_WARNING);
    }

    // 关闭句柄
    curl_close(self::$ch);

    // 请求成功之后才把URL存起来
    list($header, $text) = self::split_header_body();
    self::$history = self::get_history($header);
    self::$headers = self::get_response_headers($header);
    self::get_response_cookies($header, $domain);
    //$data = substr($data, 10);
    //$data = gzinflate($data);
    return $text;
  }
}
