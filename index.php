<?php
/*Build by SaschArt all right reserved 2014 contact@saschart.com*/

if (!defined('ABSPATH')) {
  session_start();
  header("Content-type: image/gif");

  define('ABSPATH', realpath('../../../').'/');
  define('WP_PLUGIN_DIR_RC', realpath('.'));

  require_once ABSPATH.'wp-config.php';

  $counter_page=strtolower($saschart->delEscape(urldecode($saschart->R('counter_page'))));
  if (!$counter_page) exit();
  if ($saschart->S('s_count_step')<1) exit();
}
$last_entries=100;
$last_visit=24;


$csv_cols=" | ";
$csv_rows="\r\n";
$csv_cols_=str_replace("|","\|",$csv_cols);

if (!isset($g_addr)) $g_addr=$saschart->getIp();
$nowtime=date("ymdHi");
$nowmonth=date("ym");

$lasttime=date("ymdHi",mktime(date("H")-$last_visit,date("i"),0,date("m"),date("d"),date("y")));
$queryck="SELECT details FROM rcounter_last WHERE time>'$lasttime' AND ip='$g_addr'";
$resultck = $saschart->mysqlQuery($queryck);
while ($rowck = mysql_fetch_row ($resultck)) {
  $rowck[0]=trim($rowck[0],$csv_rows);
  if ($counter_page) {
    if (ereg($csv_rows.preg_quote($counter_page).$csv_cols_,$csv_rows.$rowck[0])) {
      unset($details);
      foreach (explode($csv_rows,$rowck[0]) as $arr_var) {
        if ($arr_var) {
          $arrr_var=explode($csv_cols,$arr_var);
          if ($details) $details.="$csv_rows";
          if ($arrr_var[0]==$counter_page) $details.="$arrr_var[0]$csv_cols$arrr_var[1]$csv_cols".($arrr_var[2]+1);
          else $details.="$arrr_var[0]$csv_cols$arrr_var[1]$csv_cols$arrr_var[2]";
        }
      }
    }
    else
      $details="$rowck[0]$csv_rows$counter_page$csv_cols$nowtime$csv_cols"."1";
  }
  else
    $details=$rowck[0];
  $query="UPDATE rcounter_last SET time_last='$nowtime', visits=visits+1, details='".mysql_escape_string($details)."' WHERE time>'$lasttime' AND ip='$g_addr'";
  $saschart->mysqlQuery($query);

  $query="SELECT page FROM rcounter_top WHERE month='$nowmonth'";
  $result = $saschart->mysqlQuery($query);
  while ($row = mysql_fetch_row ($result)) {
    $csv_page=$saschart->doCsvTop($row[0], $counter_page);
    $queryup="UPDATE rcounter_top SET page='$csv_page', page_rank=page_rank+1 WHERE month='$nowmonth'";
    $saschart->mysqlQuery($queryup);
  }
  mysql_free_result($result);
  if (!$csv_page) {
    $csv_page=mysql_escape_string($counter_page).$csv_cols."1";
    $queryin="INSERT INTO rcounter_top SET month='$nowmonth', page='$csv_page', page_rank=1";
    $saschart->mysqlQuery($queryin);
  }
  $saschart->S_('s_count_step',2);
}
mysql_free_result($resultck);
if ($saschart->S('s_count_step')==1) {
  if (!isset($counter_ref))
    $counter_ref=strtolower($saschart->delEscape(urldecode($saschart->R('counter_ref'))));
  $counter_ref=$counter_ref;
  if ($counter_ref)
    $counter_word=$saschart->cropText($saschart->getWord($counter_ref),35);
  $counter_res=$saschart->R('counter_res');
  $nowhour=date("ymdH");
  $nowday=date("ymd");
  $nowyear=date("y");
  if (!isset($counter_robot)) $counter_robot=$saschart->getRobot();
  if ($counter_robot) {
    $counter_browser=$counter_robot;
    $counter_os="Robot";
  }
  else {
    if (!isset($counter_browser))
      $counter_browser=$saschart->getBrowser();
    $counter_os=$saschart->getOS();
  }
  if (!isset($counter_country)) $counter_country=$saschart->getCountry();
  $details=mysql_escape_string($counter_page).$csv_cols.$nowtime.$csv_cols."1";

  $query="INSERT INTO rcounter_last SET time='$nowtime', time_last='$nowtime', ip='$g_addr', ref='".mysql_escape_string($counter_ref)."', word='".mysql_escape_string($counter_word)."', browser='$counter_browser', agent='".mysql_escape_string($saschart->SV('HTTP_USER_AGENT'))."', os='$counter_os', res='$counter_res', country='$counter_country', visits=1, details='$details'";
  $saschart->mysqlQuery($query);

  $query="SELECT ip FROM rcounter_last";
  $result = $saschart->mysqlQuery($query);
  $num_entries=mysql_num_rows($result);
  if ($num_entries>$last_entries) {
    $del_entries=$num_entries-$last_entries;
    $querydel="DELETE FROM rcounter_last ORDER BY time LIMIT $del_entries";
    $saschart->mysqlQuery($querydel);
  }
  mysql_free_result($result);

  $queryck="SELECT ip FROM rcounter_hold WHERE ip='$g_addr'";
  $resultck = $saschart->mysqlQuery($queryck);
  if (!mysql_num_rows($resultck)) {

    $queryin="INSERT INTO rcounter_hold SET time='$nowtime', ip='$g_addr'";
    $saschart->mysqlQuery($queryin);

    $querydel="DELETE FROM rcounter_hold WHERE time<='$lasttime'";
    $saschart->mysqlQuery($querydel);

    $query="SELECT hours, days, months, years FROM rcounter_time WHERE hours<>'' LIMIT 1";
    $result = $saschart->mysqlQuery($query);
    while ($row = mysql_fetch_row ($result)) {
      $queryup="UPDATE rcounter_time SET
      hours='".$saschart->doCsvTime($row[0], $nowhour)."',
      days='".$saschart->doCsvTime($row[1], $nowday)."',
      months='".$saschart->doCsvTime($row[2], $nowmonth)."',
      years='".$saschart->doCsvTime($row[3], $nowyear, 0)."'
      WHERE hours<>''";
      $saschart->mysqlQuery($queryup);
      if ($csv_clean=="days" or $csv_clean=="months") {
        $lastmonth=substr($row[1],0,4);
        $queryin="INSERT INTO rcounter_time SET days='$row[1]', months='$lastmonth'";
        $saschart->mysqlQuery($queryin);
      }
      if ($csv_clean=="months") {
        $lastyear=substr($row[2],0,2);
        $queryin="INSERT INTO rcounter_time SET months='$row[2]', years='$lastyear'";
        $saschart->mysqlQuery($queryin);
      }
      $counter_time=1;
    }
    mysql_free_result($result);
    if (!$counter_time) {
      $queryin="INSERT INTO rcounter_time SET
      hours='$nowhour$csv_cols"."1',
      days='$nowday$csv_cols"."1',
      months='$nowmonth$csv_cols"."1',
      years='$nowyear$csv_cols"."1'";
      $saschart->mysqlQuery($queryin);
    }
    if ($counter_ref) {
      $counter_ref=preg_replace("/([^:\/])\/.*/","\\1",$counter_ref);
    }
    $query="SELECT page, ref, word, robot, browser, os, res, country FROM rcounter_top WHERE month='$nowmonth' LIMIT 1";
    $result = $saschart->mysqlQuery($query);
    while ($row = mysql_fetch_row ($result)) {
      $queryup="UPDATE rcounter_top SET ";
      if ($counter_page)
        $queryup.="page='".$saschart->doCsvTop($row[0], $counter_page)."',page_rank=page_rank+1,";
      if ($counter_ref)
        $queryup.="ref='".$saschart->doCsvTop($row[1], $counter_ref)."',ref_rank=ref_rank+1,";
      if ($counter_word)
        $queryup.="word='".$saschart->doCsvTop($row[2], $counter_word)."',word_rank=word_rank+1,";
      if ($counter_robot)
        $queryup.="robot='".$saschart->doCsvTop($row[3], $counter_robot)."',robot_rank=robot_rank+1,";
      if ($counter_browser and !$counter_robot)
        $queryup.="browser='".$saschart->doCsvTop($row[4], $counter_browser)."',browser_rank=browser_rank+1,";
      if ($counter_os)
        $queryup.="os='".$saschart->doCsvTop($row[5], $counter_os)."',os_rank=os_rank+1,";
      if ($counter_res)
        $queryup.="res='".$saschart->doCsvTop($row[6], $counter_res)."',res_rank=res_rank+1,";
      if ($counter_country)
        $queryup.="country='".$saschart->doCsvTop($row[7], $counter_country)."',country_rank=country_rank+1,";
      $queryup=trim($queryup,',');
      $queryup.=" WHERE month='$nowmonth'";
      $saschart->mysqlQuery($queryup);
      $counter_top=1;
    }
    mysql_free_result($result);
    if (!$counter_top) {
      $queryin="INSERT INTO rcounter_top SET month='$nowmonth',";
      if ($counter_page)
        $queryin.="page='".mysql_escape_string($counter_page).$csv_cols."1',page_rank=1,";
      if ($counter_ref)
        $queryin.="ref='".mysql_escape_string($counter_ref).$csv_cols."1',ref_rank=1,";
      if ($counter_word)
        $queryin.="word='".mysql_escape_string($counter_word).$csv_cols."1',word_rank=1,";
      if ($counter_robot)
        $queryin.="robot='$counter_robot$csv_cols"."1',robot_rank=1,";
      if ($counter_browser)
        $queryin.="browser='$counter_browser$csv_cols"."1',browser_rank=1,";
      if ($counter_os)
        $queryin.="os='$counter_os$csv_cols"."1',os_rank=1,";
      if ($counter_res)
        $queryin.="res='$counter_res$csv_cols"."1',res_rank=1,";
      if ($counter_country)
        $queryin.="country='$counter_country$csv_cols"."1',country_rank=1,";
      $queryin=trim($queryin,',');
      $saschart->mysqlQuery($queryin);
    }
  }
  mysql_free_result($resultck);
}
?>