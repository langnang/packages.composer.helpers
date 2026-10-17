<?php


class SQLite
{
  public $table;
  private $pdo;

  function __destruct()
  {
    $this->pdo = null;
  }

  function __construct($filePath)
  {
    try {
      $this->pdo = new PDO("sqlite:" . $filePath);
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
}
