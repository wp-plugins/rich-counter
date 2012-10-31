<?php
/*Build by SaschArt all right reserved 2012 contact@saschart.com*/

if (!defined('WP_UNINSTALL_PLUGIN')) exit ();

global $wpdb;
$rich_counter_prefix=$wpdb->prefix."rcounter_";

$arr_sql=array(
  "DROP TABLE IF EXISTS ".$rich_counter_prefix."hold",
  "DROP TABLE IF EXISTS ".$rich_counter_prefix."last",
  "DROP TABLE IF EXISTS ".$rich_counter_prefix."time",
  "DROP TABLE IF EXISTS ".$rich_counter_prefix."top"
);

foreach ($arr_sql as $sql)
  mysql_query($sql);

?>