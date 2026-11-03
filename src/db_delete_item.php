<?php

/**
 * Deletes an item from the database.
 *
 * @param string $table The table name.
 * @param string $key The key of the item to delete.
 * @return bool True if the item was deleted, false otherwise.
 */
function db_delete_item($table, $key)
{
  global $wpdb;
  $table = $wpdb->prefix . $table;
  $key = esc_sql($key);

  // Prepare the SQL query to delete the item
  $sql = "DELETE FROM `$table` WHERE `key` = %s";

  // Execute the query
  return $wpdb->query($wpdb->prepare($sql, $key));
}
