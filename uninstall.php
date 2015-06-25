<?php
/*Build by SaschArt all right reserved 2015 contact@saschart.com*/

if (!defined('WP_UNINSTALL_PLUGIN')) exit ();

$arr_sql=array(
  "DROP TABLE IF EXISTS rcounter_hold",
  "DROP TABLE IF EXISTS rcounter_last",
  "DROP TABLE IF EXISTS rcounter_time",
  "DROP TABLE IF EXISTS rcounter_top"
);

global $wpdb;
foreach ($arr_sql as $sql)
  $wpdb->query($sql);

?>