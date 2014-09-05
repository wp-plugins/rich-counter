<?php
/*
Plugin Name: Rich Counter
Plugin URI: http://soft.saschart.com
Download: http://soft.saschart.com/download/rich_counter_wp_1.1.5.zip
Description: <strong>Rich Counter</strong> is a powerful, rich, small and easy to handle counter . It not delay page loading and consumes <strong>very low resources</strong> and retain data many years ago, make tops and details visits. To update periodically your ip2country database, visit <a href="http://soft.saschart.com">SaschArt</a> website.
Version: 1.1.5
Author: SaschArt
Author URI: http://www.saschart.com

Copyright (c) 2014 SaschArt

SaschArt hereby gives you a non-exclusive license to use the Plugin ONLY to WordPress platform.

You may not:
- use this software or part of it in other platform than WordPress;
- modify, translate, reverse engineer, decompile, disassemble and use it in other platform than WordPress;
- create derivative plugins based on this software;
- rent, lease, transfer or otherwise transfer rights to the software;

This program is free software; you can redistribute it and/or modify
it under the limitations described above and the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
if (!defined('ABSPATH')) exit ();
if (!session_id()) session_start();

global $rich_counter_version;
$rich_counter_version="1.0";

if (!defined('WP_CONTENT_URL')) define('WP_CONTENT_URL', get_option("siteurl").'/wp-content');
if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if (!defined('WP_PLUGIN_URL')) define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR')) define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');

define('WP_PLUGIN_URL_RC', WP_PLUGIN_URL.'/rich-counter');
define('WP_PLUGIN_DIR_RC', WP_PLUGIN_DIR.'/rich-counter');
$count_robots=0;
$count_others=1;
$top_show=10;
$top_entries=50;
$address_whois='http://wq.apnic.net/apnic-bin/whois.pl?searchtext=$addr';


require_once WP_PLUGIN_DIR_RC.'/includes/functions.php';
$saschart=new RichCounter();
require_once WP_PLUGIN_DIR_RC.'/includes/admin.php';
require_once WP_PLUGIN_DIR_RC.'/includes/mysql_install.php';
register_activation_hook(__FILE__,'installMysqlRc');


if ($saschart->S('s_count_step')<1) $saschart->S_('s_count_step',1);

$counter_page=$saschart->SV('HTTP_HOST').$saschart->SV('REQUEST_URI',0);
if (!preg_match("/^http:\/\/".$saschart->SV('HTTP_HOST')."/",$saschart->SV('HTTP_REFERER'))) $counter_ref=$saschart->SV('HTTP_REFERER',0);

$counter_robot=$saschart->getRobot();

if ($counter_robot) {
  if ($count_robots)
    require_once WP_PLUGIN_DIR_RC.'/index.php';
}
else {
  $counter_browser=$saschart->getBrowser();
  if (!$counter_browser) {
    if ($count_others)
      require_once WP_PLUGIN_DIR_RC.'/index.php';
  }
  else {
    $url=WP_PLUGIN_URL_RC."/index.php?counter_page=".urlencode($counter_page)."&counter_ref=".urlencode($counter_ref);

    function writeWpFooter ($content) {
      global $url, $copyright;
      echo $content."<noscript><img src=\"$url\" width=\"0\" height=\"0\" border=\"0\" alt=\"$g_site_name\"></noscript>
      <script language=\"JavaScript\" type=\"text/javascript\">
        counter=new Image();
        counter.src='$url&counter_res='+screen.width+'x'+screen.height;
      </script>";
    }

    add_filter('wp_footer', 'writeWpFooter');
  }
}

?>