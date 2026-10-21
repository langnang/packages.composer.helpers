<?php

/**
 * Connects to the database.
 *
 * @param string $pdo The PDO driver.
 * @param string $host The database host.
 * @param int $port The database port.
 * @param string $username The database username.
 * @param string $password The database password.
 * @param string $dbname The database name.
 * @return PDO The database connection.
 */
function db_connect($pdo, $host, $port, $username, $password, $dbname)
{
  $dsn = "{$pdo}:host={$host};port={$port};dbname={$dbname}";
  try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
  } catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
  }
}
