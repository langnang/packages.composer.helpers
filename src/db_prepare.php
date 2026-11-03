<?php

function db_prepare($table, $data, $where = null)
{
  global $wpdb;
  $table = $wpdb->prefix . $table;

  // Prepare the SQL query for updating or inserting data
  if ($where) {
    // Update existing record
    $set = [];
    foreach ($data as $field => $value) {
      $set[] = "`$field` = %s";
    }
    $set_clause = implode(', ', $set);
    $where_clause = [];
    foreach ($where as $field => $value) {
      $where_clause[] = "`$field` = %s";
    }
    $where_clause_str = implode(' AND ', $where_clause);
    $sql = "UPDATE `$table` SET $set_clause WHERE $where_clause_str";
    return [$sql, array_merge(array_values($data), array_values($where))];
  } else {
    // Insert new record
    $fields = array_keys($data);
    $placeholders = array_fill(0, count($fields), '%s');
    $fields_str = implode('`, `', $fields);
    $placeholders_str = implode(', ', $placeholders);
    $sql = "INSERT INTO `$table` (`$fields_str`) VALUES ($placeholders_str)";
    return [$sql, array_values($data)];
  }
}
