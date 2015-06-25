<?php
/*Build by SaschArt all right reserved 2015 contact@saschart.com*/

add_action('admin_menu', 'adminMenuRc');

function adminMenuRc() {
  add_menu_page('Rich Counter - SaschArt', 'Rich Counter', 'manage_options', 'rich_counter_show', 'richCounterShow', WP_PLUGIN_URL_RC.'/images/tmp/icon.ico', '25.77777770');
}
function richCounterShow() {
  global $rich_counter,$top,$month,$csv_cols,$csv_cols_,$csv_rows,$counter_imgs,$counter_src,$arr_countries;
  if (!current_user_can('manage_options')) {
    wp_die( __( 'You do not have sufficient permissions to access this page.'));
  }

  include WP_PLUGIN_DIR_RC.'/includes/countries.php';

  extract($rich_counter->RA());
  if (!$action) $action="top";
  $counter_imgs=WP_PLUGIN_DIR_RC.'/images/';
  $counter_src=WP_PLUGIN_URL_RC.'/images/';
  $g_n='&nbsp;';
  $address_whois=get_option('rich_counter_address_whois');

  $content="<br><table>
  <tr>
    <td class=\"button\"><a href=\"?".$rich_counter->varsQuery("page",1)."&action=top\"><img src=\"".$counter_src."tmp/top.gif\" border=0 alt=\"".WP_PLUGIN_NM_RC."\"></a>$g_n<a href=\"?".$rich_counter->varsQuery("page",1)."&action=top\">Show Top Acceses</a>$g_n</td>
    <td class=\"button\">$g_n<a href=\"?".$rich_counter->varsQuery("page",1)."&action=last\"><img src=\"".$counter_src."tmp/data.gif\" border=0 alt=\"".WP_PLUGIN_NM_RC."\"></a>$g_n<a href=\"?".$rich_counter->varsQuery("page",1)."&action=last\">Show Last Acceses</a>$g_n</td>
    <td class=\"button\"><a href=\"?".$rich_counter->varsQuery("page",1)."&action=time\"><img src=\"".$counter_src."tmp/time.gif\" border=0 alt=\"".WP_PLUGIN_NM_RC."\"></a>$g_n<a href=\"?".$rich_counter->varsQuery("page",1)."&action=time\">Show Acceses by Time</a>$g_n</td>
    <td class=\"button\"><a href=\"?".$rich_counter->varsQuery("page",1)."&action=clean\"><img src=\"".$counter_src."tmp/del.gif\" border=0 alt=\"".WP_PLUGIN_NM_RC."\"></a>$g_n<a href=\"?".$rich_counter->varsQuery("page",1)."&action=clean\">Clean Stat</a>$g_n</td>
    <td class=\"button\"><a href=\"?".$rich_counter->varsQuery("page",1)."&action=opt\"><img src=\"".$counter_src."tmp/opt.gif\" border=0 alt=\"".WP_PLUGIN_NM_TVS."\"></a>$g_n<a href=\"?".$rich_counter->varsQuery("page",1)."&action=opt\">Options</a>$g_n</td>
  </tr></table><br>";

  $last_online=3;
  $csv_cols=" | ";
  $csv_rows="\r\n";
  $csv_cols_=str_replace("|","\|",$csv_cols);


  $nowtime=date("ymdHi");
  $lasttime=date("ymdHi",mktime(date("H"),date("i")-$last_online,0,date("m"),date("d"),date("y")));
  $nowhour=date("ymdH");
  $nowday=date("ymd");
  $nowmonth=date("ym");
  $nowyear=date("y");
  $launch=gmmktime(0,0,0,7,7,15);
  if (time()<$launch) {
    $launch='<div class="launch_title">Announcement of the launch.</div>
    <div class="launch">On the date of '.date("Y/m/d", $launch).' SaschArt team will launch <b>TV Stream</b> free plugin for video streaming directly from your Wordpress. Does not require additional video streaming server. You will can have your own online TV with very little charges</span><br>';
  }
  elseif (date("Y/m/d")==date("Y/m/d", $launch)) {
    $launch='<div class="launch_title">LAUNCH DAY.</div>
    <div class="launch">SaschArt team today launched <b><a href="https://wordpress.org/plugins/tv-stream/">TV Stream</a></b> free plugin for video streaming directly from your Wordpress. Does not require additional video streaming server. You can have now your own online TV with very little charges</span><br>';
  }
  else
    $launch='';


  if ($action=="top") {

    $on_users=0;
    $query="SELECT time FROM rcounter_last WHERE time_last>$lasttime";
  //$rich_counter->mysqlRows($query);
  //$on_users=$rich_counter->num_rows;

    $query="SELECT ref_rank, word_rank, browser_rank, os_rank, res_rank FROM rcounter_top";
  $result=$rich_counter->mysqlRows($query);
  foreach($result as $row) {
      $ref_rank+=$row[0];
      $word_rank+=$row[1];
      $max_rank=$row[2];
      if ($max_rank<$row[3])
        $max_rank=$row[3];
      if ($max_rank<$row[4])
        $max_rank=$row[4];
      $rank+=$max_rank;
    }

    $query="SELECT days, months, years FROM rcounter_time WHERE hours<>'' LIMIT 1";
  $row=$rich_counter->mysqlRow($query);
  if (is_array($row)) {
      $lastday=$lastmonth=$lastyear=0;
      if (preg_match("/$csv_rows$nowday$csv_cols_/","$csv_rows$row[0]"))
        $lastday=preg_replace("/.*$csv_rows$nowday$csv_cols_(.+?)$csv_rows.*/sm","\\1","$csv_rows$row[0]$csv_rows");
      if (preg_match("/$csv_rows$nowmonth$csv_cols_/","$csv_rows$row[1]"))
        $lastmonth=preg_replace("/.*$csv_rows$nowmonth$csv_cols_(.+?)$csv_rows.*/sm","\\1","$csv_rows$row[1]$csv_rows");
      if (preg_match("/$csv_rows$nowyear$csv_cols_/","$csv_rows$row[2]"))
        $lastyear=preg_replace("/.*$csv_rows$nowyear$csv_cols_(.+?)$csv_rows.*/sm","\\1","$csv_rows$row[2]$csv_rows");
      if ($row[2])
        $amount=array_sum(preg_split("/$csv_rows.+$csv_cols_/","$csv_rows$row[2]"));
      $retval="<table width=\"100%\">
      <tr>
        <td align=\"center\" class=\"cell2\">
          <div style=\"margin:6px\"><b class=\"title\"><u>Unique accesses</u></b><br></div>
          <b>This day: <span style=\"color:#0008AF\">$lastday</span>
          This month: <span style=\"color:#0008AF\">$lastmonth</span>
          This year: <span style=\"color:#0008AF\">$lastyear</span>
          Amount: <span style=\"color:#0008AF\">$amount</span><br>
          From referrers: <span style=\"color:#0008AF\">".sprintf("%01.2f",$ref_rank*100/$rank)."%</span>
          From keywords: <span style=\"color:#0008AF\">".sprintf("%01.2f",$word_rank*100/$rank)."%</span><br>
          Aproximately online users: <span style=\"color:#0008AF\">$on_users</span></b>
        </td>
      </tr></table>";
    }
    $month=$rich_counter->R('month');
    $top=$rich_counter->R('top');
    if (!$month) $month=$nowmonth;
    $top_show=get_option('rich_counter_top_show');
    if (!$top) $top=$top_show;


    $query="SELECT month FROM rcounter_top ORDER BY month";
  $result=$rich_counter->mysqlRows($query);
  foreach($result as $row) {
      $arr_months[$row[0]]="20".preg_replace("/(\d\d)(\d\d)/","\\1.\\2",$row[0]);
    }
    $arr_form[all]=array(t=>0);
    if ($arr_months)
      $arr_form[]=array(el=>'select',name=>'month',value=>$arr_months,onChange=>'submit()');
    $i=1;
    while ($i<=5) {
      $arr_top[$i*$top_show]=$i*$top_show;
      $i++;
    }
    $arr_top[1000]="all";
    $arr_form_top[all]=array(t=>0);
    $arr_form_top[]=array(el=>'select',name=>'top',value=>$arr_top,onChange=>'submit()',cls=>'width40');

    $query="SELECT page,page_rank,ref,ref_rank,word,word_rank,robot,robot_rank,browser,browser_rank,os,os_rank,res,res_rank,country,country_rank FROM rcounter_top WHERE month='$month' LIMIT 1";
  $row=$rich_counter->mysqlRow($query);
  if (is_array($row)) {
  $count_robots=get_option('rich_counter_count_robots');
      if ($count_robots) {
        $width_pag="31%";
        $width_ref="22%";
        $width_key="25%";
        $retrobots.="<td width=\"22%\" valign=\"top\" class=\"cell1\">
        <table width=\"100%\">
          <tr><td colspan=\"4\" align=\"center\"><b>Robots</b></td></tr>
          ".$rich_counter->writeCsvTop($row[6],$row[7],"","robot")."
        </table></td>";
      }
      else {
        $width_pag="37%";
        $width_ref="30%";
        $width_key="33%";
      }
      $max_rank=$row[9];
      if ($max_rank<$row[11])
        $max_rank=$row[11];
      if ($max_rank<$row[13])
        $max_rank=$row[13];

      $retval.="<table width=\"100%\">
      <tr>
        <td align=\"center\" class=\"cell2\">
          <div style=\"margin-top:6px;margin-bottom:14px\"><b class=\"title\"><u>Top accesses for 20".preg_replace("/(\d\d)(\d\d)/","\\1.\\2",$month)." month</u></b><br></div>
          <table width=\"100%\">
            <tr>
              <td width=\"$width_pag\" valign=\"top\" class=\"cell1\">
                <table width=\"100%\">
                  <tr><td colspan=\"3\" align=\"center\"><b>Pages</b></td></tr>
                  ".$rich_counter->writeCsvTop($row[0],$row[1],"http://")."
                </table>
              </td>
              <td width=\"$width_ref\" valign=\"top\" class=\"cell1\">
                <table width=\"100%\">
                  <tr><td colspan=\"3\" align=\"center\"><b>Referrers</b> (".sprintf("%01.2f",$row[3]*100/$max_rank)."%)</td></tr>
                  ".$rich_counter->writeCsvTop($row[2],$row[3],1)."
                </table>
              </td>
              <td width=\"$width_key\" valign=\"top\" class=\"cell1\">
                <table width=\"100%\">
                  <tr><td colspan=\"3\" align=\"center\"><b>Keywords</b> (".sprintf("%01.2f",$row[5]*100/$max_rank)."%)</td></tr>
                  ".$rich_counter->writeCsvTop($row[4],$row[5])."
                </table>
              </td>
              $retrobots
            </tr>
          </table>
          <table width=\"100%\">
            <tr>
              <td width=\"30%\" valign=\"top\" class=\"cell1\">
                <table width=\"100%\">
                  <tr><td colspan=\"4\" align=\"center\"><b>Browsers</b></td></tr>
                  ".$rich_counter->writeCsvTop($row[8],$row[9],"","browser")."
                </table>
              </td>
              <td width=\"25%\" valign=\"top\" class=\"cell1\">
                <table width=\"100%\">
                  <tr><td colspan=\"4\" align=\"center\"><b>Operating Systems</b></td></tr>
                  ".$rich_counter->writeCsvTop($row[10],$row[11],"","os")."
                </table>
              </td>
              <td width=\"20%\" valign=\"top\" class=\"cell1\">
                <table width=\"100%\">
                  <tr><td colspan=\"3\" align=\"center\"><b>Resolutions</b></td></tr>
                  ".$rich_counter->writeCsvTop($row[12],$row[13])."
                </table>
              </td>
              <td width=\"25%\" valign=\"top\" class=\"cell1\">
                <table width=\"100%\">
                  <tr><td colspan=\"4\" align=\"center\"><b>Countries</b></td></tr>
                  ".$rich_counter->writeCsvTop($row[14],$row[15],"","country")."
                </table>
              </td>
            </tr>
          </table>
          <form method=\"post\" action=\"\" style=\"margin:5px\">
            change the top with ".$rich_counter->writeForm($arr_form)." month, show ".$rich_counter->writeForm($arr_form_top)." results
          </form>
        </td>
      </tr></table>";
    }

    if ($retval) {
      $content.="<table width=\"888\">
      <tr><td class=\"box\">$retval</td></tr>
    </table>
    time difference: <b><span id=\"dif\"></span>&nbsp;hours</b><br><br>";
    $content.="<script type=\"text/javascript\">
    d=new Date();
    h=0;
    year=d.getFullYear();
    if (year!=".date('Y').") {
      h=-24;
      if (year>".date('Y').") {
        h=24;
      }
    }
    month=d.getMonth()+1;
    if (month!=".date('n').") {
      h=-24;
      if (month>".date('n').") {
        h=24;
      }
    }
    day=d.getDate();
    if (day!=".date('j').") {
      h=-24;
      if (day>".date('j').") {
        h=24;
      }
    }
    document.getElementById('dif').innerHTML=h+d.getHours()-".date('G').";
  </script>";
}
else {
  $message="No data!";
  $content.="<div style=\"margin:50px\">$message</div>";
}
}
elseif ($action=="last") {

  $tb_fields=array("time_last","ip","browser","os","country","ref","word","visits");

  $sort_field=$rich_counter->R('sort_field');
  $order=$rich_counter->R('order');

  if (!$sort_field) {
    $sort_field=$tb_fields[0];
    $order="desc";
  }
  $cell=1;
  $query="SELECT ".implode(",",$tb_fields)." FROM rcounter_last ORDER BY $sort_field $order";
  $result=$rich_counter->mysqlRows($query);
  foreach($result as $row) {
    $i=0;
    while($i<count($tb_fields)) {
      if (!$row[$i]) $row[$i]=$g_n;
      $i++;
    }
    if ($row[3]=="Robot")
      $browser="robot";
    else
      $browser="browser";
    if ($row[2]<>$g_n) {
      if (!is_file("$counter_imgs".$browser."_".str_replace(" ","_",strtolower($row[2])).".gif"))
        $row[2]="<img src=\"".$counter_src.$browser."_other.gif\" alt=\"".WP_PLUGIN_NM_RC."\">$g_n$row[2]";
      else
        $row[2]="<img src=\"".$counter_src.$browser."_".str_replace(" ","_",strtolower($row[2])).".gif\" alt=\"".WP_PLUGIN_NM_RC."\">$g_n$row[2]";
    }
    if ($row[3]<>$g_n)
      $row[3]="<img src=\"".$counter_src."os_".str_replace(" ","_",strtolower($row[3])).".gif\" alt=\"".WP_PLUGIN_NM_RC."\">$g_n$row[3]";
    if ($arr_countries[$row[4]])
      $row[4]="<img src=\"".$counter_src."country_".str_replace(" ","_",strtolower($row[4])).".gif\" alt=\"".WP_PLUGIN_NM_RC."\">$g_n".$rich_counter->cropUrl($arr_countries[$row[4]],15);
    else $nr_empty++;
    if ($row[5]<>$g_n) {
      $arr_ref=parse_url($row[5]);
      $row[5]="<a href=\"$row[5]\" title=\"See $row[5]\" class=\"counter\" target=\"nw\">".str_replace("www.","",$arr_ref['host'])."</a>";
    }
    $retval.="<tr>
    <td class=\"cell$cell\">".preg_replace("/[\d.+]{4}(\d\d)(\d\d)(\d\d)/","\\1 \\2:\\3",$row[0])."</td>
    <td align=\"left\" class=\"cell$cell\">$row[1]$g_n<a href=\"".str_replace('{ip}',$row[1],$address_whois)."\" target=\"nw\"><img src=\"".$counter_src."tmp/whois.gif\" width=\"24\" height=\"20\" alt=\"See who is $row[1]\" border=0></a></td>
    <td align=\"left\" class=\"cell$cell\">$row[2]</td>
    <td align=\"left\" class=\"cell$cell\">$row[3]</td>
    <td align=\"left\" class=\"cell$cell\">$row[4]</td>
    <td align=\"left\" class=\"cell$cell\">$row[5]</td>
    <td align=\"left\" class=\"cell$cell\">$row[6]</td>
    <td class=\"cell$cell\"><a href=\"?".$rich_counter->varsQuery("action")."&action=details&time=$row[0]&ip=$row[1]\" title=\"See details for this user\">$row[7]</a></td></tr>";
    if ($cell==1) $cell=2;
    else $cell=1;
    $nr_all++;
  }
  if ($retval) {
    if ($order=="asc") {
      $img_order="<img src=\"".$counter_src."tmp/sort_asc.gif\" alt=\"".WP_PLUGIN_NM_RC."\">";
      $set_order="desc";
    }
    else {
      $img_order="<img src=\"".$counter_src."tmp/sort_desc.gif\" alt=\"".WP_PLUGIN_NM_RC."\">";
      $set_order="asc";
    }
    $retcont="<table width=\"100%\">
    <tr>";
      $i=0;
      while($tb_fields[$i]) {
        if ($tb_fields[$i]==$sort_field) {
          if ($order=="asc") {
            $img_order="<img src=\"".$counter_src."tmp/sort_asc.gif\" alt=\"".WP_PLUGIN_NM_RC."\">";
            $set_order="desc";
          }
          else {
            $img_order="<img src=\"".$counter_src."tmp/sort_desc.gif\" alt=\"".WP_PLUGIN_NM_RC."\">";
            $set_order="asc";
          }
        }
        else {
          $img_order="";
          $set_order="asc";
        }
        $retcont.="<td align=\"center\" class=\"cell2\"><b><a href=\"?".$rich_counter->varsQuery('sort_field&order')."&sort_field=$tb_fields[$i]&order=$set_order\" title=\"Sort by ".str_replace("_"," ",$tb_fields[$i])." ".$set_order."endent\"  class=\"title\">".ucfirst(str_replace("_"," ",$tb_fields[$i]))."</a></b>$img_order</td>";
        $i++;
      }
      $retcont.="
    </tr>
    $retval</table>";
    if ($nr_empty>3 && $nr_empty/$nr_all>0.03) $warning="<br>WARNING: Your ip2country database is out of date, contact <a href=\"http://soft.saschart.com\" target=\"nw\">http://soft.saschart.com</a> to update at new one.";
    $content.="<table width=\"970\">
    <tr><td class=\"blue_rect\">$retcont</td></tr>
    <tr><td>$warning</td></tr>
  </table><br>";
}
else {
  $message="No data!";
  $content.="<div style=\"margin:50px\">$message</div>";
}
}
elseif ($action=="details") {
  $cell=1;
  $query="SELECT ip,browser,agent,os,res,country,ref,word,visits,details FROM rcounter_last WHERE time_last='".$rich_counter->R('time')."' AND ip='".$rich_counter->R('ip')."' LIMIT 1";
  $row=$rich_counter->mysqlRow($query);
  if (is_array($row)) {
    $ip=$row[0];
    $row[0]="<div style=\"float:left;width:80px\"><b>Ip:</b> </div><div align=\"left\">$row[0]$g_n<a href=\"".str_replace('{ip}',$row[0],$address_whois)."\" target=\"nw\"><img src=\"".$counter_src."tmp/whois.gif\" width=\"24\" height=\"20\" alt=\"See who is $row[0]\" border=0></a></div>";
    if ($ip<>gethostbyaddr($ip))
      $row[0].="<div style=\"float:left;width:80px\">$g_n</div><div align=\"left\" class=\"bottom_line\">".gethostbyaddr($ip)."</div>";
    $ip=$row[0];
    if ($row[1]) {
      if ($row[3]=="Robot")
        $browser="robot";
      else
        $browser="browser";
      if (!is_file("$counter_imgs".$browser."_".str_replace(" ","_",strtolower($row[1])).".gif"))
        $row[1]="<div style=\"float:left;width:80px\"><b>Browser:</b> </div><div align=\"left\" class=\"bottom_line\"><img src=\"".$counter_src.$browser."_other.gif\" alt=\"".WP_PLUGIN_NM_RC."\">$g_n".strip_tags($row[1])."</div>";
      else
        $row[1]="<div style=\"float:left;width:80px\"><b>Browser:</b> </div><div align=\"left\" class=\"bottom_line\"><img src=\"".$counter_src.$browser."_".str_replace(" ","_",strtolower($row[1])).".gif\" alt=\"".WP_PLUGIN_NM_RC."\">$g_n".strip_tags($row[1])."</div>";
    }
    if ($row[2])
      $row[2]="<div style=\"float:left;width:80px\"><b>Details:</b> </div><div align=\"left\" class=\"bottom_line\">$row[2]</div>";
    if ($row[3])
      $row[3]="<div style=\"float:left;width:80px\"><b>Os:</b> </div><div align=\"left\" class=\"bottom_line\"><img src=\"".$counter_src."os_".str_replace(" ","_",strtolower($row[3])).".gif\" alt=\"".WP_PLUGIN_NM_RC."\">$g_n$row[3]</div>";
    if ($row[4])
      $row[4]="<div style=\"float:left;width:80px\"><b>Resolution:</b> </div><div align=\"left\" class=\"bottom_line\">$row[4]</div>";
    if ($arr_countries[$row[5]])
      $row[5]="<div style=\"float:left;width:80px\"><b>Country:</b> </div><div align=\"left\" class=\"bottom_line\"><img src=\"".$counter_src."country_".str_replace(" ","_",strtolower($row[5])).".gif\" alt=\"".WP_PLUGIN_NM_RC."\">$g_n".$rich_counter->cropUrl($arr_countries[$row[5]],15)."</div>";
    if ($row[6]) {
      $arr_ref=parse_url($row[6]);
      $row[6]="<div style=\"float:left;width:80px\"><b>Ref:</b> </div><div align=\"left\" class=\"bottom_line\"><a href=\"$row[6]\" title=\"See $row[6]\" class=\"counter\" target=\"nw\">".str_replace("www.","",$arr_ref['host'])."</a></div>";
    }
    if ($row[7])
      $row[7]="<div style=\"float:left;width:80px\"><b>Word:</b> </div><div align=\"left\" class=\"bottom_line\">$row[7]</div>";
    if ($row[8])
      $row[8]="<div style=\"float:left;width:80px\"><b>Hits:</b> </div><div align=\"left\">$row[8]</div>";
    $i=0;
    if ($row[9]) {
      foreach (explode($csv_rows,$row[9]) as $arr_var) {
        if ($arr_var) {
          $arrr_var=explode($csv_cols,$arr_var);
          if ($details) $details.="<tr>";
          $details.="<td class=\"cell$cell\">".preg_replace("/[\d.+]{4}(\d\d)(\d\d)(\d\d)/","\\1 \\2:\\3",$arrr_var[1])."</td>
          <td class=\"cell$cell\"><a href=\"http://$arrr_var[0]\" title=\"See $arrr_var[0]\" target=\"nw\" class=\"counter\">".$rich_counter->cropUrl($arrr_var[0])."</a></td>
          <td class=\"cell$cell\">$arrr_var[2]</td></tr>";
          if ($cell==1) $cell=2;
          else $cell=1;
          $i++;
        }
      }
    }
    $retval="<tr>
    <td rowspan=\"$i\" valign=\"top\" class=\"cell1\">$row[0]$row[1]$row[3]$row[2]$row[4]$row[5]$row[6]$row[7]$row[8]</td>
    $details";
  }
  if ($retval) {
    $content.="<table width=\"700\">
    <tr>
      <td width=\"245\" align=\"center\" class=\"cell2\"><b>Informations</b></td>
      <td width=\"60\" align=\"center\" class=\"cell2\"><b>Time</b></td>
      <td width=\"290\" align=\"center\" class=\"cell2\"><b>Page</b></td>
      <td width=\"45\" align=\"center\" class=\"cell2\"><b>Reload</b></td>
    </tr>$retval</table><br>";
  }
  else {
    $message="No data!";
    $content.="<div style=\"margin:50px\">$message</div>";
  }
}
elseif ($action=="time") {

  $month=$rich_counter->R('month');
  $year=$rich_counter->R('year');
  if (!$month) $month=$nowmonth;
  if (!$year) $year=$nowyear;
  $query="SELECT hours, days, months, years FROM rcounter_time WHERE hours<>'' OR months='$month' OR years='$year' ORDER BY hours, years, months";
  $result=$rich_counter->mysqlRows($query);
  foreach($result as $row) {
    if ($year==$nowyear and $month==$nowmonth) {
      $retval="<table width=\"100%\">
      <tr>
        <td height=\"30\" align=\"center\"><b>This day</b></td>
      </tr>
      <tr>
        <td height=\"150\" class=\"cell1\">
          ".$rich_counter->writeCsvTime($row[0],$nowday,0,23)."
        </td>
      </tr></table>";
    }
    if ($month and $month==$row[2]) {
      $retval.="<table width=\"100%\">
      <tr>
        <td height=\"30\" align=\"center\"><b>20".preg_replace("/^(\d\d)/","\\1.",$month)." month</b></td>
      </tr>
      <tr>
        <td height=\"150\" class=\"cell1\">
          ".$rich_counter->writeCsvTime($row[1],$month,1,date("d",mktime(0,0,0,substr($month,2,2)+1,0,date("y"))))."
        </td>
      </tr></table>";
    }
    elseif ($row[0] and $month==$nowmonth and substr($month,0,2)==$year) {
      $retval.="<table width=\"100%\">
      <tr>
        <td height=\"30\" align=\"center\"><b>This month</b></td>
      </tr>
      <tr>
        <td height=\"150\" class=\"cell1\">
          ".$rich_counter->writeCsvTime($row[1],$nowmonth,1,date("d",mktime(0,0,0,date("m")+1,0,date("y"))))."
        </td>
      </tr></table>";
    }
    if ($year and $year==$row[3]) {
      $retval.="<div style=\"width:70%;float:left\"><table width=\"100%\">
      <tr>
        <td height=\"30\" align=\"center\"><b>20$year year</b></td>
      </tr>
      <tr>
        <td height=\"150\" class=\"cell1\">
          ".$rich_counter->writeCsvTime($row[2],$year,1,12,'',1)."
        </td>
      </tr></table></div>";
    }
    elseif ($row[0] and $year==$nowyear) {
      $retval.="<div style=\"width:70%;float:left\"><table width=\"100%\">
      <tr>
        <td height=\"30\" align=\"center\"><b>This year</b></td>
      </tr>
      <tr>
        <td height=\"150\" class=\"cell1\">
          ".$rich_counter->writeCsvTime($row[2],$nowyear,1,12,'',1)."
        </td>
      </tr></table></div>";
    }
    if ($row[0]) {
      $retval.="<div style=\"width:30%;float:left\"><table width=\"100%\">
      <tr>
        <td height=\"30\" align=\"center\"><b>All years</b></td>
      </tr>
      <tr>
        <td height=\"150\" class=\"cell1\">
          ".$rich_counter->writeCsvTime($row[3],"",-1,-1,20,1)."
        </td>
      </tr></table></div>";
    }
  }
  if ($retval) {
    $content.="<table width=\"700\">
    <tr>
      <td align=\"center\" class=\"cell2\">$retval</td>
    </tr></table><br>";
  }
  else {
    $message="No data!";
    $content.="<div style=\"margin:50px\">$message</div>";
  }
}
elseif ($action=="clean") {

  $submit=$rich_counter->R('submit');
  $year_time=$rich_counter->R('year_time');
  $date=$rich_counter->R('date');
  if ($submit) {
    $confirm=$rich_counter->R('confirm');
    if (strlen($date)>2)
      $str_date="20".preg_replace("/^(\d\d)/","\\1.",$date);
    else
      $str_date="20$date";
    if ($confirm=="yes") {
      if ($date=="all") {
        $querydel="DELETE FROM rcounter_hold";
        $rich_counter->mysqlQuery($querydel);
        $querydel="DELETE FROM rcounter_last";
        $rich_counter->mysqlQuery($querydel);
        $querydel="DELETE FROM rcounter_time";
        $rich_counter->mysqlQuery($querydel);
        $querydel="DELETE FROM rcounter_top";
        $rich_counter->mysqlQuery($querydel);
        $message="Successfully delete all the stats!";
      }
      else {
        if ($submit=='Clean the top stats') {
          $querydel="DELETE FROM rcounter_top WHERE month<='$date'";
          $rich_counter->mysqlQuery($querydel);
          $message="Successfully clean the top stats for old months then $str_date!";
        }
        elseif ($submit=='Clean details days') {
          $querydel="DELETE FROM rcounter_time WHERE hours='' AND months<='$date' AND years=''";
          $rich_counter->mysqlQuery($querydel);
          $message="Successfully clean the days details for old months then $str_date!";
        }
        elseif ($submit=='Clean details months') {
          $querydel="DELETE FROM rcounter_time WHERE hours='' AND days='' AND years<='$date'";
          $rich_counter->mysqlQuery($querydel);
          $message="Successfully clean the months details for old years then $str_date!";
        }
      }
    }
    else {
      $message="Confirm <a href=\"?confirm=yes&date=$date&submit=$submit&".$rich_counter->varsQuery('date&submit')."\">here</a> if you really want to do this job:<br>";
      if ($date=="all")
        $message.="delete all the stats";
      else
        $message.=strtolower($submit).$g_n."older then $str_date";
    }
    $content.="<div style=\"margin:50px\">$message</div>";
  }
  else {

    $query="SELECT month FROM rcounter_top WHERE month<'".date('ym')."' ORDER BY month";
  $result=$rich_counter->mysqlRows($query);
  foreach($result as $row) {
      $arr_months[$row[0]]="20".preg_replace("/(\d\d)(\d\d)/","\\1.\\2",$row[0]);
    }
  $arr_form[all]=array(t=>0,el=>'input');
    if ($arr_months) {
      $arr_form[]=array(type=>'submit',name=>'submit',value=>'Clean the top stats',onClick=>'document.form.date.value=document.form.month_top.value',cls=>'button width150');
      $arr_form[]=array(lb=>' older then'.$g_n,el=>'select',name=>'month_top',value=>$arr_months);
    }

    unset($arr_months);
    $query="SELECT months FROM rcounter_time WHERE hours='' AND years='' ORDER BY months";
  $result=$rich_counter->mysqlRows($query);
  foreach($result as $row) {
      $arr_months[$row[0]]="20".preg_replace("/(\d\d)(\d\d)/","\\1.\\2",$row[0]);
    }
    if ($arr_months) {
      $arr_form[]=array(type=>'submit',name=>'submit',value=>'Clean details days',onClick=>'document.form.date.value=document.form.month_time.value',cls=>'button width150');
      $arr_form[]=array(lb=>' older then'.$g_n,el=>'select',name=>'month_time',value=>$arr_months);
    }
    $query="SELECT years FROM rcounter_time WHERE hours='' AND days='' ORDER BY years";
  $result=$rich_counter->mysqlRows($query);
  foreach($result as $row) {
      $arr_years[$row[0]]="20$row[0]";
    }
    if ($arr_years) {
      $arr_form[]=array(type=>'submit',name=>'submit',value=>'Clean details months',onClick=>'document.form.date.value=document.form.year_time.value',cls=>'button width150');
      $arr_form[]=array(lb=>' older then'.$g_n,el=>'select',name=>'year_time',value=>$arr_years);
    }

    $content.="<br><form name=\"form\" method=\"post\" action=\"\" style=\"margin:5px\"><table width=\"350\">";
    if ($arr_form) {
      $content.="<tr>
      <td align=\"center\" class=\"cell2\">
        ".$rich_counter->writeForm($arr_form)."
      </td>
    </tr>";
    unset($arr_form);
  }
  $arr_form[all]=array(t=>0,el=>'input');
  $arr_form[]=array(type=>'hidden',name=>'date',value=>'all');
  $arr_form[]=array(type=>'submit',name=>'submit',value=>'Click here to delete all the stats',cls=>'button width200');
  $content.="<tr>
  <td align=\"center\" class=\"cell1\">
    ".$rich_counter->writeForm($arr_form)."
  </td>
</tr>
</table>
</form><br>";
}
}
    elseif ($action=="opt") {
      if ($submit=='Set Options') {
        $last_entries=get_option('rich_counter_last_entries');
        if (isset($last_entries)) {
          update_option('rich_counter_count_robots', $count_robots);
          update_option('rich_counter_count_others', $count_others);
          update_option('rich_counter_top_show', $top_show);
          update_option('rich_counter_top_entries', $top_entries);
          update_option('rich_counter_last_entries', $last_entries);
          update_option('rich_counter_address_whois', $address_whois);

        }
        else {
          add_option('rich_counter_count_robots', $count_robots);
          add_option('rich_counter_count_others', $count_others);
          add_option('rich_counter_top_show', $top_show);
          add_option('rich_counter_top_entries', $top_entries);
          add_option('rich_counter_last_entries', $last_entries);
          add_option('rich_counter_address_whois', $address_whois);
        }
        $content.="<div class=\"msg\">Options successfully stored<div>";
      }
      else {
        global $count_robots,$count_others,$top_show,$top_entries,$last_entries,$address_whois;

        $count_robots=get_option('rich_counter_count_robots');
        $count_others=get_option('rich_counter_count_others');
        $top_show=get_option('rich_counter_top_show');
        $top_entries=get_option('rich_counter_top_entries');
        $last_entries=get_option('rich_counter_last_entries');
        $address_whois=get_option('rich_counter_address_whois');


        for ($i=10; $i<=50; $i+=10) {
          $arr_top_show[]=$i;
        }
        for ($i=10; $i<=100; $i+=10) {
          $arr_top_entries[]=$i;
        }
        for ($i=50; $i<=500; $i+=50) {
          $arr_last_entries[]=$i;
        }

        $arr_form[all]=array(tdl=>'width="120" class="cell_right"',tdr=>'class="cell_left"',el=>'input');
        $arr_form[]=array(lb=>'<b>Count robots:</b>',lbe=>'<span style=\"float:left\">Count all robots, spiders, this feature consumes some resources.</span>',type=>'checkbox',name=>'count_robots',value=>1);
        $arr_form[]=array(lb=>'<b>Other browsers:</b>',lbe=>'<span style=\"float:left\">Count unknown browsers and devices.</span>',type=>'checkbox',name=>'count_others',value=>1);
        $arr_form[]=array(lb=>'<b>Top show:</b>',lbe=>'<span style=\"float:left\">Data show in top.</span>',el=>'select',name=>'top_show',value=>$arr_top_show);
        $arr_form[]=array(lb=>'<b>Top entries:</b>',lbe=>'<span style=\"float:left\">Data store in top.</span>',el=>'select',name=>'top_entries',value=>$arr_top_entries);
        $arr_form[]=array(lb=>'<b>Last entries:</b>',lbe=>'<span style=\"float:left\">Hold last entries data.</span>',el=>'select',name=>'last_entries',value=>$arr_last_entries);
        $arr_form[]=array(lb=>'<b>Address whois:</b>',lbe=>'<span style=\"float:left\">External address to get whois, {ip} will be replaced with user IP.</span>',type=>'text',name=>'address_whois');
        $arr_form[]=array(lbr=>'<br>',type=>'submit',name=>'submit',value=>'Set Options',cls=>'button width150');


        $content.="<form name=\"form\" action=\"\" method=\"post\">
        <table class=\"opt\">".$rich_counter->writeForm($arr_form)."</table>
      </form>";
    }
  }

$content="<link rel=\"stylesheet\" type=\"text/css\" href=\"".WP_PLUGIN_URL_RC."/style.css\">
<table width=\"100%\">
  <tr><td align=\"center\">$content<br><br></td></tr>
  <tr><td class=\"line_down\">$launch<br></td></tr>
</table><a href=\"http://www.saschart.com\">SaschArt Counter</a>";


echo $content;
}

?>