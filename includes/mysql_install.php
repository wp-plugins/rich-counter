<?php
/*Build by SaschArt all right reserved 2012 contact@saschart.com*/

function jal_install() {
  global $rich_counter_prefix, $rich_counter_version;

  $arr_sql=array(
    "CREATE TABLE IF NOT EXISTS ".$rich_counter_prefix."hold (
      time varchar(10) NOT NULL default '',
      ip varchar(20) NOT NULL default '',
      UNIQUE KEY ip (ip)
    ) TYPE=MyISAM;",
    "CREATE TABLE IF NOT EXISTS ".$rich_counter_prefix."last (
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
    ) TYPE=MyISAM;",
    "CREATE TABLE IF NOT EXISTS ".$rich_counter_prefix."time (
      hours text NOT NULL,
      days text NOT NULL,
      months text NOT NULL,
      years text NOT NULL
    ) TYPE=MyISAM;",
    "CREATE TABLE IF NOT EXISTS ".$rich_counter_prefix."top (
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
    ) TYPE=MyISAM;");

    foreach ($arr_sql as $sql)
      mysql_query($sql);

    add_option("jal_db_version", $rich_counter_version);
  }
  ?>