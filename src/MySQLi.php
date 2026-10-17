<?php


class MySQLi extends Def
{
  private $mysqli;

  function __destruct()
  {
    $this->mysqli->close();
  }

  function __construct($host, $port, $dbname, $username, $password)
  {
    $this->mysqli = new mysqli($host, $username, $password, $dbname, $port);
    if ($this->mysqli->connect_error) {
      die("Connection failed: " . $this->mysqli->connect_error);
    }
    $this->mysqli->set_charset("utf8mb4");
  }

  function query($sql)
  {
    $result = $this->mysqli->query($sql);
    if ($result === false) {
      die("Query failed: " . $this->mysqli->error);
    }
    return $result;
  }

  function fetchAll($sql)
  {
    $result = $this->query($sql);
    return $result->fetch_all(MYSQLI_ASSOC);
  }

  function fetchOne($sql)
  {
    $result = $this->query($sql);
    return $result->fetch_assoc();
  }

  function fetchValue($sql)
  {
    $result = $this->fetchOne($sql);
    return $result ? array_values($result)[0] : null;
  }

  function execute($sql)
  {
    return $this->query($sql)->affected_rows;
  }
}
