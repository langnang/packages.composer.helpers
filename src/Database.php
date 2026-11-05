<?php

/**
 * 
 */
class Database
{

  public $table;
  private $pdo;
  function __destruct()
  {
    $this->pdo = null;
  }
  function __construct($pdo, $host, $port, $dbname, $username, $password)
  {
    $dsn = "{$pdo}:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";
    try {
      $this->pdo = new PDO($dsn, $username, $password);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
      die("Connection failed: " . $e->getMessage());
    }
  }

  function query($sql, $params = [])
  {
    try {
      $stmt = $this->pdo->prepare($sql);
      $stmt->execute($params);
      return $stmt;
    } catch (PDOException $e) {
      die("Query failed: " . $e->getMessage());
    }
  }

  function fetchAll($sql, $params = [])
  {
    return $this->query($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
  }

  function fetchOne($sql, $params = [])
  {
    return $this->query($sql, $params)->fetch(PDO::FETCH_ASSOC);
  }

  function fetchValue($sql, $params = [])
  {
    $result = $this->fetchOne($sql, $params);
    return $result ? array_values($result)[0] : null;
  }
  function execute($sql, $params = [])
  {
    return $this->query($sql, $params)->rowCount();
  }

  function update($table, $data, $where, $whereParams = [])
  {
    $setClause = implode(", ", array_map(function ($col) {
      return "$col = ?";
    }, array_keys($data)));
    $sql = "UPDATE `$table` SET $setClause WHERE $where";
    return $this->execute($sql, array_merge(array_values($data), $whereParams));
  }
  function delete($table, $where, $whereParams = [])
  {
    $sql = "DELETE FROM `$table` WHERE $where";
    return $this->execute($sql, $whereParams);
  }
  function columns() {}
  function insert($data)
  {
    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), "?"));
    $sql = "INSERT INTO `$this->table` ($columns) VALUES ($placeholders)";
    return $this->execute($sql, array_values($data));
  }
  function insertOrIgnore($data)
  {
    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), "?"));
    $sql = "INSERT IGNORE INTO `$this->table` ($columns) VALUES ($placeholders)";

    return $this->execute($sql, array_values($data));
  }
  function insertGetId($data)
  {
    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), "?"));
    $sql = "INSERT INTO `$this->table` ($columns) VALUES ($placeholders)";

    $this->execute($sql, array_values($data));
    return $this->pdo->lastInsertId();
  }

  function upsert($data, $uniqueKeys)
  {
    $columns = implode(", ", array_keys($data));
    $placeholders = implode(", ", array_fill(0, count($data), "?"));

    $updateClause = implode(", ", array_map(function ($col) {
      return "$col = VALUES($col)";
    }, array_keys($data)));

    $sql = "INSERT INTO `$this->table` ($columns) VALUES ($placeholders) ON DUPLICATE KEY UPDATE $updateClause";
    return $this->execute($sql, array_values($data));
  }
  private $wheres = [];
  function where($column, $condition, $value = null)
  {
    if ($value) $this->wheres[] = "$column $condition '$value'";
    else $this->wheres[] = "{$column} = '$value'";
    return $this;
  }
  function get_whereString()
  {
    if (sizeof($this->wheres) > 0) {
      return "WHERE " . implode(" AND ", $this->wheres);
    }
    return '';
  }
  function get()
  {

    $sql = "SELECT * FROM `$this->table` {$this->get_whereString()}";
    return $this->fetchAll($sql);
  }
}
