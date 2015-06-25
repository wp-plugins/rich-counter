<?php
/*
Plugin Name: Rich Counter
Plugin URI: http://soft.saschart.com
Download: http://soft.saschart.com/download/rich_counter_wp_1.5.0.zip
Description: <strong>Rich Counter</strong> is a powerful, rich, small and easy to handle counter . It not delay page loading and consumes <strong>very low resources</strong> and retain data many years ago, make tops and details visits. To update periodically your ip2country database, visit <a href="http://soft.saschart.com">SaschArt</a> website.
Version: 1.5.0
Author: SaschArt
Author URI: http://www.saschart.com

Copyright (c) 2015 SaschArt

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

if (!defined('WP_CONTENT_URL')) define('WP_CONTENT_URL', get_option("siteurl").'/wp-content');
if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH.'wp-content');
if (!defined('WP_PLUGIN_URL')) define('WP_PLUGIN_URL', WP_CONTENT_URL.'/plugins');
if (!defined('WP_PLUGIN_DIR')) define('WP_PLUGIN_DIR', WP_CONTENT_DIR.'/plugins');

define('WP_PLUGIN_URL_RC', WP_PLUGIN_URL.'/rich-counter');
define('WP_PLUGIN_DIR_RC', WP_PLUGIN_DIR.'/rich-counter');
define('WP_PLUGIN_NM_RC', 'Rich Counter');


require_once WP_PLUGIN_DIR_RC.'/includes/functions.php';
$rich_counter=new RichCounter();

require_once WP_PLUGIN_DIR_RC.'/includes/admin.php';
require_once WP_PLUGIN_DIR_RC.'/includes/install.php';
register_activation_hook(__FILE__,'installRichCounter');


if ($rich_counter->S('s_count_step')<1) $rich_counter->S_('s_count_step',1);

$counter_page=$rich_counter->SV('HTTP_HOST').$rich_counter->SV('REQUEST_URI',0);
if (!preg_match("/^http:\/\/".$rich_counter->SV('HTTP_HOST')."/",$rich_counter->SV('HTTP_REFERER'))) $counter_ref=$rich_counter->SV('HTTP_REFERER',0);

$count_robots=get_option('rich_counter_count_robots');
$count_others=get_option('rich_counter_count_others');

$counter_robot=$rich_counter->getRobot();

if ($counter_robot) {
  if ($count_robots)
    require_once WP_PLUGIN_DIR_RC.'/index.php';
}
else {
  $counter_browser=$rich_counter->getBrowser();
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