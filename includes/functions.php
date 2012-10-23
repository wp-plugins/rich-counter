<?php
/*Build by SaschArt all right reserved 2012 contact@saschart.com*/

class RichCounter {
  function R($in_var,$escape=1) {
    if (isset($_POST[$in_var]))
    return $escape ? $this->doEscape($_POST[$in_var]):$this->delEscape($_POST[$in_var]);
    if (isset($_GET[$in_var]))
      return $escape ? $this->doEscape($_GET[$in_var]):$this->delEscape($_GET[$in_var]);
  }
  function S($in_var) {
    return $_SESSION[$in_var];
  }
  function S_($in_var,$in_val) {
    $_SESSION[$in_var]=$in_val;
  }
  function SV($in_var,$escape=1) {
    return $escape ? $this->doEscape($_SERVER[$in_var]):$this->delEscape($_SERVER[$in_var]);
  }
  function GL($in_var,$escape=1) {
  if (ereg('\[',$in_var)) {
    $in_var=str_replace(array('"',"'"),'',$in_var);
    return $GLOBALS[preg_replace('/\[(.+)\]/','',$in_var)][preg_replace('/(.+)\[(.+)\]/','\\2',$in_var)];
  }
  elseif ($this->R($in_var)==$this->doEscape($GLOBALS[$in_var]))
    return $this->R($in_var,$escape);
  else return $GLOBALS[$in_var];
}
function GL_($in_var,$in_val) {
  $GLOBALS[$in_var]=$in_val;
}
function doEscape($str) {
  return get_magic_quotes_gpc() ? $str:addslashes($str);
}
function delEscape($str) {
  return get_magic_quotes_gpc() ? stripslashes($str):$str;
}
function mysqlQuery($query) {
  global $mysql_error;
  $result=mysql_query($query);
  if (!$result) {
    $mysql_error=mysql_error();
    handlError(0,"MySQL error: <i>$query</i> $mysql_error",$this->SV('PHP_SELF'),0);
  }
  return $result;
}
function handlError($errno,$errstr,$errfile,$errline) {
  $message="<b>ERROR</b> [$errno] $errstr, file $errfile, in line $errline<br>\r\n";
}
function cropUrl($text,$mx_chars=40) {
  $pattern_sep="\/\s_\.\?\!,:;";
  if (strlen($text)>$mx_chars) {
    $text=preg_replace("/^[^\/]*\//sm","",$text);
    if (strlen($text)>$mx_chars)
      $text=preg_replace("/(.+)[$pattern_sep].*/sm","\\1",substr($text,0,$mx_chars));
  }
  return $text;
}
function cropText($text,$mx_chars=50) {
  $pattern_sep="\/\s_\.\?\!,:;";
  if (strlen($text)>$mx_chars)
    $text=preg_replace("/(.+)[$pattern_sep](.*)/sm","\\1",substr($text,0,$mx_chars));
  return trim($text);
}
function decryptText($str_var) {
  $str_chrs_='e380DVXCBh7ay5ZvnJTstfU6q9IuHNlF1igpPLSGxK2QowWrEd4RzAjkmOcY_bM"/<>= :.';
  $str_chrs=strrev($str_chrs_);
  while ($str_var<>"") {
    $str_chr=substr($str_var,0,1);
    $chr_pos=strpos($str_chrs,$str_chr);
    if ($chr_pos===false)
      $str_out.=$str_chr;
    else
      $str_out.=substr($str_chrs_,$chr_pos,1);
    $str_var=substr($str_var,1);
  }
  return $str_out;
}
function varsQuery($vars='',$only=0) {
  $query=trim($this->SV('QUERY_STRING'),' ?&');
  if (!$vars || !$query) return $query;

  if (preg_match_all("/(\w+)=([^&]*)/",$query,$arr)) {
    $query='';
    $i=0;
    while($arr[1][$i]) {
      if ($only && ereg("&".$arr[1][$i]."&","&$vars&")) $query.=$arr[1][$i].'='.$arr[2][$i].'&';
      elseif (!$only && !ereg("&".$arr[1][$i]."&","&$vars&")) $query.=$arr[1][$i].'='.$arr[2][$i].'&';
      $i++;
    }
  }
  return rtrim($query,'&');
}
function writeForm($arr_form,$escape=0) {
  $arr_rem=array('lb','lbr','el','check','alert');
  foreach ($arr_form as $arr_fm) {
    $form_out.=$arr_fm['lb'];
    unset($arr_atr);
    foreach ($arr_fm as $arr_var=>$arr_val) {
      if ($arr_var==='cls') $arr_var='class';
      if (!in_array($arr_var,$arr_rem)) $arr_atr[$arr_var]=$arr_val;
    }
    if ($arr_fm['el']==='input') {
      $form_out.='<input';
      if (isset($arr_fm['value']))
        $arr_atr['value']=htmlspecialchars($arr_fm['value']);
      else
        $arr_atr['value']=htmlspecialchars($this->GL($arr_fm['name'],$escape));
      if (!isset($arr_atr['class']))
        $arr_atr['class']=$arr_fm['type'];
      foreach ($arr_atr as $arr_var=>$arr_val) {
        $form_out.=" $arr_var=\"$arr_val\"";
      }
      if (($arr_fm['type']=='checkbox' or $arr_fm['type']=='radio') and $this->GL($arr_fm['name'],$escape)==$arr_atr['value'])
        $form_out.=" checked";
      $form_out.='>';
    }
    elseif ($arr_fm[el]==='select') {
      $form_out.='<select';
      if (!isset($arr_atr['class']))
        $arr_atr['class']='select';
      foreach ($arr_atr as $arr_var=>$arr_val) {
        if ($arr_var<>'value' && $arr_var<>'selected') $form_out.=" $arr_var=\"$arr_val\"";
      }
      $form_out.='>';
      if (isset($arr_fm['value'][0])) {
        foreach ($arr_fm['value'] as $arr_val) {
          $form_out.="<option value=\"".htmlspecialchars($arr_val)."\"";
          if ((!$arr_fm['selected'] and $this->GL($arr_fm['name'],$escape)==$arr_val) or ($arr_fm['selected'] and $arr_fm['selected']==$arr_val))
            $form_out.=" selected";
          $form_out.=">".str_replace("_"," ",$arr_val)."</option>";
        }
      }
      else {
        foreach ($arr_fm['value'] as $arr_var=>$arr_val) {
          $form_out.="<option value=\"".htmlspecialchars($arr_var)."\"";
          if ((!$arr_fm['selected'] and $this->GL($arr_fm['name'],$escape)==$arr_var) or ($arr_fm['selected'] and $arr_fm['selected']==$arr_val))
            $form_out.=" selected";
          $form_out.=">$arr_val</option>";
        }
      }
      $form_out.='</select>';
    }
    $form_out.=$arr_fm['lbr'];
  }
  return $form_out;
}
function getIp() {
  if ($this->SV('HTTP_X_FORWARDED_FOR'))
    $ip = $this->SV('HTTP_X_FORWARDED_FOR');
  elseif ($this->SV('HTTP_CLIENT_IP'))
    $ip = $this->SV('HTTP_CLIENT_IP');
  else
    $ip = $this->SV('REMOTE_ADDR');
  return preg_replace("/,.*/","",$ip);
}
function getBrowser() {
  if (!$this->SV('HTTP_USER_AGENT'))
    return "";
  $arr_browsers = array(
    "Firefox"=>"Firefox\/([0-9.+]{1,10})",
    "Opera"=>"opera[ \/]([0-9.]{1,10})",
    "Internet Explorer"=>"\(compatible; MSIE[ \/]([0-9.]{1,10})",
    "Netscape"=>"(netscape[0-9]?\/([0-9.]{1,10}))|(^mozilla\/([0-4]\.[0-9.]{1,10}))",
    "Mozilla"=>"(^mozilla\/[5-9]\.[0-9.]{1,10}.+rv:([0-9a-z.+]{1,10}))|(^mozilla\/([5-9]\.[0-9a-z.]{1,10}))",
    "Avant Browser"=>"Avant[ ]?Browser",
    "AOL"=>"(aol[ \/\-]([0-9.]{1,10}))|(aol[ \/\-]?browser)",
    "Maxthon"=>" Maxthon[\);]",
    "Safari"=>"safari\/([0-9.]{1,10})",
    "Crazy Browser"=>"Crazy Browser[ \/]([0-9.]{1,10})",
    "Konqueror"=>"konqueror\/([0-9.]{1,10})",
    "Deepnet Explorer"=>"(Deepnet Explorer[\/ ]([0-9.]{1,10}))|( Deepnet Explorer[\);])",
  );
  foreach($arr_browsers as $arr_var=>$arr_val) {
    if (preg_match("/$arr_val/i",$this->SV('HTTP_USER_AGENT'))) return $arr_var;
  }
  return str_replace("'","",preg_replace("/^(\w+).*/","\\1",$this->SV('HTTP_USER_AGENT')));
}
function getRobot() {
  if (!$this->SV('HTTP_USER_AGENT'))
    return "";
  $arr_robots = array(
    "Google"=>"(Googl(e|ebot)(-Image)?\/([0-9.]{1,10}))|(Googl(e|ebot)(-Image)?\/)",
    "Yahoo"=>"Yahoo(! ([a-z]{1,3} )?Slurp;|-|FeedSeeker)",
    "MSN"=>"MSN(BOT|PTC)[ \/]([0-9.]{1,10})",
    "Jyxo"=>"Jyxobot[ \/]([0-9.]{1,10})",
    "Yell"=>"(YellCrawl[ \/]V?([0-9.]{1,10}))|(findlinks[ \/]([0-9.]{1,10}))",
    "Entireweb"=>"Speedy[ ]?Spider",
    "Alexa"=>"^ia_archive",
    "Gigablast"=>"((Gigabot|Sitesearch)[\/ ]([0-9.]{1,10}))|(GigabotSiteSearch[\/ ]([0-9.]{1,10}))",
    "Altavista"=>"Scooter[ \/\-]*[a-z]*([0-9.]{1,10})",
    "ASPseek"=>"^ASPseek[\/ ]([0-9.]{1,10})",
    "Ask Jeeves"=>"(Ask[ \-]?Jeeves)|(teomaagent)",
    "Excite"=>"Architext[ \-]?Spider",
    "Inktomi"=>"slurp@inktomi\.com",
    "Internet Seer"=>"^InternetSeer\.com",
    "Lycos"=>"Lycos_Spider_",
    "Netcraft"=>"netcraft",
    "PHP"=>"^PHP[ \/]([0-9.]{1,10})",
    "Planet"=>"Planet[ \/]([0-9.]{1,10})",
    "W3C Validator"=>"W3C_Validator[ \/]([0-9.]{1,10})",
    "CSS Validator"=>"CSS(Check|_Validator)",
    "Link Validator"=>"((checklin|linkchec)k)|(Link[ \-]?Val(et|idator))",
    "Other Robot"=>"(robot|crawler|spider|harvest)",
  );
  foreach($arr_robots as $arr_var=>$arr_val) {
    if (preg_match("/$arr_val/i",$this->SV('HTTP_USER_AGENT'))) {
      if ($arr_var=="Other Robot") {
        $out=str_replace("'","",preg_replace("/^(\w+).*/","\\1",$this->SV('HTTP_USER_AGENT')));
        if (preg_replace("/\w/","",$out))
          return $arr_var;
        else
          return $out;
      }
      else
        return $arr_var;
    }
  }
  return "";
}
function getWord($url) {
  if (!$url) return "";
  $arr_engines = array("google", "yahoo", "msn","alexa","altavista","entireweb","search","findlinks","lycos","excite","ask","seek","find");
  $arr_words = array(
    ".+(\?|&)q=(.*?)($|&.*)"=>"\\2",
    ".+(\?|&)p=(.*?)($|&.*)"=>"\\2",
    ".+(\?|&)query=(.*?)($|&.*)"=>"\\2",
    ".+(\?|&)keywords=(.*?)($|&.*)"=>"\\2",
    ".+\/search\/web\/(.*?)($|\/.*)"=>"\\1",
    ".+find.com\/(.*?)($|\/.*)"=>"\\1",
  );
  $arr_url=parse_url($url);
  foreach($arr_engines as $arr_val) {
    if (eregi("\.$arr_val\.",".".$arr_url['host'].".")) {
      $engine=1;
      break;
    }
  }
  if (!$engine) return "";
  foreach($arr_words as $arr_var=>$arr_val) {
    if (preg_match("/$arr_var/i",$url)) {
      return strtolower(preg_replace("/\s+/"," ",preg_replace("/[^\w\s-]/","",str_replace("_"," ",urldecode(preg_replace("/$arr_var/i",$arr_val,$url))))));
    }
  }
  return "";
}
function getOS() {
  if (!$this->SV('HTTP_USER_AGENT'))
    return "";
  $arr_oss = array(
    "Windows 2003"=>"wi(n|ndows)[ \-]?(2003|nt[ \/]?5\.2)",
    "Windows 2000"=>"wi(n|ndows)[ \-]?(2000|nt[ \/]?5\.0)",
    "Windows 95"=>"wi(n|ndows)[ \-]?95",
    "Windows CE"=>"wi(n|ndows)[ \-]?ce",
    "Windows ME"=>"(win 9x 4\.90)|(wi(n|ndows)[ \-]?me)",
    "Windows XP"=>"(Windows XP)|(wi(n|ndows)[ \-]?nt[ \/]?5\.1)",
    "Windows NT"=>"(wi(n|ndows)[ \-]?nt[ \/]?([0-4][0-9.]{1,10}))|(wi(n|ndows)[ \-]?nt)",
    "Windows 98"=>"wi(n|ndows)[ \-]?98",
    "Windows"=>"wi(n|n32|ndows)",
    "MacOS X"=>"Mac[ ]?OS[ ]?X",
    "MacOS PPC"=>"Mac(_Power|intosh.+P)PC",
    "MacOS"=>"mac[^hk]",
    "Linux"=>"(linux[ \/\-]([a-z0-9._]{1,10}))|(linux)",
    "BSD"=>"bsd",
  );
  foreach($arr_oss as $arr_var=>$arr_val) {
    if (preg_match("/$arr_val/i",$this->SV('HTTP_USER_AGENT'))) return $arr_var;
  }
  return "";
}
function getCountry() {
  global $g_addr;

  if (!$g_addr) $g_addr=getIp();
  $long = sprintf("%u", ip2long($g_addr));
  $str_file = WP_PLUGIN_DIR_."counter/ip2country/".preg_replace("/\..*/","",$g_addr).".csv";
  if (!is_readable($str_file)) {
    $str_file = "ip2country/".preg_replace("/\..*/","",$g_addr).".csv";
    if (!is_readable($str_file)) return "";
  }

  $fp = fopen($str_file, "r");
  while ($row = fgetc($this->SV($fp, 24, ","))) {
    if (($long >= $row[0]) and ($long < ($row[0] + $row[1]))) {
      fclose($fp);
      return $row[2];
    }
  }
  fclose($fp);

  return "";
}
function doCsvTime($csv_in, $add, $clean=1) {
  global $csv_cols, $csv_cols_, $csv_rows, $csv_clean;
  $csv_in=trim($csv_in,$csv_rows);
  if ($clean) {
    $last_var=preg_replace("/$csv_cols_.+/sm","",$csv_in);
    if (substr($last_var,0,strlen($last_var)-2)<substr($add,0,strlen($add)-2)) {
      unset($csv_in);
      if (strlen($add)==4)
        $csv_clean="months";
      elseif (strlen($add)==6)
        $csv_clean="days";
    }
  }
  if ($csv_in and ereg($csv_rows.$add.$csv_cols_,$csv_rows.$csv_in)) {
    $last_val=preg_replace("/.+$csv_cols_/sm","\\1",$csv_in);
    $csv_out=preg_replace("/(.+$csv_cols_).+/sm","\\1",$csv_in).($last_val+1);
  }
  else {
    if ($csv_in) $csv_in.=$csv_rows;
    $csv_out="$csv_in$add$csv_cols"."1";
  }
  return $csv_out;
}
function doCsvTop($csv_in, $add) {
  global $csv_cols, $csv_rows, $top_entries;

  $csv_in=trim($csv_in,$csv_rows);
  $arr=array();
  foreach (explode($csv_rows,$csv_in) as $arr_var) {
    if ($arr_var) {
      $arrr_var=explode($csv_cols,$arr_var);
      $arr[$arrr_var[0]]=$arrr_var[1];
    }
  }
  $arr[$add]+=1;
  arsort($arr);
  $i=0;
  foreach ($arr as $arr_var=>$arr_val) {
    if ($i<$top_entries or $arr_var==$add) {
      if ($csv_out) $csv_out.=$csv_rows;
      $csv_out.="$arr_var$csv_cols$arr_val";
      $i++;
    }
  }
  return $csv_out;
}
function writeCsvTime($csv_in,$now,$min_x,$max_x,$add="",$href="") {
  global $csv_cols, $csv_cols_, $csv_rows, $g_n;
  if (preg_match("/$csv_rows$now\d\d$csv_cols_/","$csv_rows$csv_in")) {
    foreach (explode($csv_rows,$csv_in) as $arr_var) {
      if ($arr_var) {
        $arrr_var=explode($csv_cols,$arr_var);
        $arr[substr($arrr_var[0],strlen($arrr_var[0])-2,2)]=$arrr_var[1];
      }
    }
    $arr_keys=array_keys($arr);
    if ($min_x<0) $min_x=round($arr_keys[0]);
    if ($max_x<0) $max_x=$arr_keys[count($arr_keys)-1];
    arsort($arr);
    $arr_vals=array_values($arr);
    $max=$arr_vals[0];
  }
  else
    $max=1;
  $i=$min_x;
  while($i<=$max_x) {
    if ($i<10) $str_i="0$i";
    else $str_i=$i;
    $val=0;
    if ($arr[$str_i]) $val=$arr[$str_i];
    $str_val="";
    if ($val) $str_val=$val;
    if ($val>1000000)
      $str_val=sprintf("%01.1f", $val/1000000)."m";
    elseif ($val>1000)
      $str_val=sprintf("%01.1f", $val/1000)."k";
    $out.="<td width=\"".sprintf("%01.2f", 100/(1+$max_x-$min_x))."%\" align=\"center\" valign=\"bottom\">
    <span class=\"counter_value\" title=\"$val\">$str_val<span>
    <table width=\"100%\" height=\"".sprintf("%01.2f", $val*110/$max)."\" cellspacing=\"0\" cellpadding=\"0\">
      <tr>
        <td class=\"cell3\"></td>
      </tr>
    </table>";
    if ($href and $str_val) {
      if ($now) {
        $href="?".$this->varsQuery()."&month=$now$str_i&year=$now";
        $title="See details for $now.$str_i month";
      }
      else {
        $href="?".$this->varsQuery()."&year=$str_i";
        $title="See details for $add$str_i year";
      }
      $out.="<a href=\"$href\" class=\"counter_small\" title=\"$title\">$add$str_i</a></td>";
    }
    else
      $out.="<span class=\"counter_small\" title=\"$val\">$add$str_i<span></td>";
    $i++;
  }
  $out="<table width=\"100%\" height=\"100%\" cellspacing=\"0\" cellpadding=\"0\">
  <tr>$out</tr></table>";
  return $out;
}
function writeCsvTop($csv_in,$amount,$link='',$icon='') {
  global $csv_cols, $csv_rows, $top, $counter_imgs, $counter_src, $arr_countries;
  $i=0;
  foreach (explode($csv_rows,$csv_in) as $arr_var) {
    if ($i>=$top) break;
    if ($arr_var) {
      $arrr_var=explode($csv_cols,$arr_var);
      unset($icon_img);
      if ($icon) {
        if (is_file("$counter_imgs".$icon."_".str_replace(" ","_",strtolower($arrr_var[0])).".gif"))
          $icon_img="<td width=\"14\"><img src=\"$counter_src".$icon."_".str_replace(" ","_",strtolower($arrr_var[0])).".gif\" alt=\"$g_site_name\"></td>";
        else
          $icon_img="<td width=\"14\"><img src=\"$counter_src".$icon."_other.gif\" alt=\"$g_site_name\"></td>";
      }
      if ($icon=="country")
        $arrr_var[0]=$this->cropUrl($arr_countries[$arrr_var[0]],15);
      if ($link) {
        if ($link==1) $link_="";
        else $link_=$link;
        $out.="<tr><td align=\"left\"><a href=\"$link_$arrr_var[0]\" title=\"See $arrr_var[0]\" target=\"nw\" class=\"counter\">".$this->cropUrl($arrr_var[0])."</a></td>
        <td width=\"10%\" align=\"right\" class=\"counter\">$arrr_var[1]</td>
        <td width=\"10%\" align=\"right\" class=\"counter\">".sprintf("%01.2f", $arrr_var[1]*100/$amount)."%</td></tr>";
      }
      else
        $out.="<tr>$icon_img<td align=\"left\" class=\"counter\">".$this->cropUrl($arrr_var[0])."</td>
      <td width=\"10%\" align=\"right\" class=\"counter\">$arrr_var[1]</td>
      <td width=\"10%\" align=\"right\" class=\"counter\">".sprintf("%01.2f", $arrr_var[1]*100/$amount)."%</td></tr>";
      $i++;
    }
  }
  return $out;
}
}
?>