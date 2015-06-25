<?php
/*Build by SaschArt all right reserved 2015 contact@saschart.com*/

function installRichCounter() {
  global $wpdb;


  $version='1.5.0';
  $get_version=get_option('rich_counter_version');

  if (isset($get_version)) {
    update_option('rich_counter_version', $version);
  }
  else
    add_option('rich_counter_version', $version);

  //add default values of options
  add_option('rich_counter_count_robots', 0);
  add_option('rich_counter_count_others', 1);
  add_option('rich_counter_top_show', 10);
  add_option('rich_counter_top_entries', 50);
  add_option('rich_counter_last_entries', 100);
  add_option('rich_counter_address_whois', 'http://wq.apnic.net/apnic-bin/whois.pl?searchtext={ip}');

  if ($wpdb->prefix) {
    $query="SHOW TABLES LIKE '".$wpdb->prefix."rcounter_hold'";
    if (is_array($wpdb->get_row($query))) {
      $query="RENAME TABLE ".$wpdb->prefix."rcounter_hold TO rcounter_hold, ".$wpdb->prefix."rcounter_last TO rcounter_last, ".$wpdb->prefix."rcounter_time TO rcounter_time, ".$wpdb->prefix."rcounter_top TO rcounter_top";
      $wpdb->query($query);
    }
  }

  $arr_sql=array(
    "CREATE TABLE IF NOT EXISTS rcounter_hold (
      time varchar(10) NOT NULL default '',
      ip varchar(20) NOT NULL default '',
      UNIQUE KEY ip (ip)
    )",
    "CREATE TABLE IF NOT EXISTS rcounter_last (
      time varchar(10) NOT NULL default '',
      time_last varchar(10) NOT NULL default '',
      ip varchar(20) NOT NULL default '',
      ref varchar(150) NOT NULL default '',
      word varchar(25) NOT NULL default '',
      browser varchar(20) NOT NULL default '',
      agent varchar(255) NOT NULL default '',
      os varchar(20) NOT NULL default '',
      res varchar(10) NOT NULL default '',
      country char(2) NOT NULL default '',
      visits int(5) NOT NULL default '0',
      details text NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS rcounter_time (
      hours text NOT NULL,
      days text NOT NULL,
      months text NOT NULL,
      years text NOT NULL
    )",
    "CREATE TABLE IF NOT EXISTS rcounter_top (
      month varchar(4) NOT NULL default '',
      page text NOT NULL,
      page_rank int(11) NOT NULL default '0',
      ref text NOT NULL,
      ref_rank int(11) NOT NULL default '0',
      word text NOT NULL,
      word_rank int(11) NOT NULL default '0',
      robot text NOT NULL,
      robot_rank int(11) NOT NULL default '0',
      browser text NOT NULL,
      browser_rank int(11) NOT NULL default '0',
      os text NOT NULL,
      os_rank int(11) NOT NULL default '0',
      res text NOT NULL,
      res_rank int(11) NOT NULL default '0',
      country text NOT NULL,
      country_rank int(11) NOT NULL default '0',
      UNIQUE KEY month (month)
    )");

    foreach ($arr_sql as $sql)
      $wpdb->query($sql);
}
?>