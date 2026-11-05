<?php

function db_increase_item($table, $id, $field, $amount)
{
  global $wpdb;
  $table = $wpdb->prefix . $table;
  $id = intval($id);
  $amount = floatval($amount);
  $field = esc_sql($field);

  // Prepare the SQL query to increase the field value
  $sql = "UPDATE `$table` SET `$field` = `$field` + %f WHERE `id` = %d";

  // Execute the query
  return $wpdb->query($wpdb->prepare($sql, $amount, $id));
}
