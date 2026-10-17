<?php

class Controller
{
  /**
   * @var mixed $query
   */
  public $query;
  function set_query($query = null)
  {
    if (!$query) $query = $_GET;
    $this->query = $query;
  }
  function get_queryString($mergeData = [])
  {
    return array_query(array_merge($this->query ?? [], $mergeData));
  }
  /**
   * @var mixed $params
   */
  public $params;
  function set_params($params = null)
  {
    if (!$params) $params = $_POST;
    $this->params = $params;
  }

  public $files;
  function set_files($files = null)
  {
    if (!$files) $files = $_FILES;
    $this->files = $files;
  }
  function set($key, $value = null)
  {
    if (is_null($key)) {
      return $this;
    }
    if (isset($this->{$key})) {
      $this->{$key} = $value;
    }
    $keys = explode('.', $key);
    if (sizeof($keys) > 1 && isset($this->{$keys[0]})) {
      array_set($this->{$keys[0]}, implode('.', array_slice($keys, 1)), $value);
    } else {
      $this->{$key} = $value;
    }
    return $this;
  }
  function get($key, $default = null)
  {
    if (is_null($key)) {
      return $this;
    }

    if (isset($this->{$key})) {
      return $this->{$key};
    }
    $keys = explode('.', $key);
    if (isset($this->{$keys[0]})) {
      return array_get($this->{$keys[0]}, implode('.', array_slice($keys, 1)), $default);
    }
    return $default;
  }
  function has($key)
  {
    if (is_null($key)) {
      return false;
    }

    if (isset($this->{$key})) {
      return true;
    }
    $keys = explode('.', $key);
    if (isset($this->{$keys[0]})) {
      return array_has($this->{$keys[0]}, implode('.', array_slice($keys, 1)));
    }
    return false;
  }
  function is($key, $value = null)
  {
    return $this->get($key) === $value;
  }
  function can($key) {}
  function in($key, $keyOrvalues)
  {
    $values = is_array($keyOrvalues) ? $keyOrvalues : $this->get($keyOrvalues, []);
    return in_array($this->get($key), $values);
  }

  function echo($key, $default = '')
  {
    $return = $this->get($key, $default);
    if (is_string($return) || is_numeric($return)) echo $return;
    else echo json_encode($return, JSON_UNESCAPED_UNICODE);
  }
  private $db;
  function set_db($db = null)
  {
    if (!$db) {
      $db = new DB($_ENV['DB_CONNECTION'], $_ENV['DB_HOST'], $_ENV['DB_PORT'], $_ENV['DB_DATABASE'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD']);
    }
    $this->db = $db;
  }
  private $tables;
  function tables() {}
  private $columns;
  function set_columns($columns = null)
  {
    if (!$columns) {
    }
    $this->columns = $columns;
  }
  private $table;
  function set_table($table)
  {
    $this->table = $table;
    $this->db->table = $table;
    $this->set_columns();
  }
  private $item;
  private $list;
  private $page_size;
  private $page_total;
  private $page_count;
  private $page_num;
  private $page;
  function page() {}
  private $models = [];
  function set_model($name, $value)
  {
    $this->models[$name] = $value;
  }

  public $commands = [];
  function set_command($name, $value)
  {
    $this->commands[$name] = $value;
  }
  /**
   * @var mixed $tables
   */
  function __construct($table = null)
  {
    // throw new \Exception('Not implemented');
    $this->set_query();
    $this->set_params();
    $this->set_files();
    $this->set_db();
    if ($table) $this->set_table($table);
  }

  function __call($name, $arguments)
  {
    // var_dump(method_exists($this->db, $name));
    // var_dump([__METHOD__, $name, $arguments]);
    if (method_exists($this->db, $name)) {
      return $this->db->{$name}(...$arguments);
    }
  }

  public $on_get;
  public $on_post;
  public $on_cmd;
  public $on_chrome;
}
