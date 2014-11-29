<?php
/*Build by SaschArt all right reserved 2014 contact@saschart.com*/

if (!defined('WP_UNINSTALL_PLUGIN')) exit ();

$arr_sql=array(
  "DROP TABLE IF EXISTS rcounter_hold",
  "DROP TABLE IF EXISTS rcounter_last",
  "DROP TABLE IF EXISTS rcounter_time",
  "DROP TABLE IF EXISTS rcounter_top"
);

foreach ($arr_sql as $sql)
  mysql_query($sql);

?>