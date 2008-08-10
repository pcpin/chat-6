<?php
/**
 *    This file is part of "PCPIN Chat 6".
 *
 *    "PCPIN Chat 6" is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation; either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    "PCPIN Chat 6" is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

define('PCPIN_ADMIN_ACCESS', true);

// Initialize
require_once('./init.inc.php');

// Get session handler
$session=&$_pcpin_init_session;
unset($_pcpin_init_session);

// Current user data
_pcpin_loadClass('user'); $current_user=new PCPIN_User($session);
if (!empty($session->_s_user_id)) {
  $current_user->_db_loadObj($session->_s_user_id);
  if ($current_user->is_admin!=='y') {
    $session->_s_logOut(true);
    header('Location: '.PCPIN_FORMLINK);
    die();
  }
}

/**
 * Receive version update information
 */
if (!empty($sk) && !empty($nv) && !empty($dl)) {
  _pcpin_loadClass('version'); $version=new PCPIN_Version($session);
  if ($version->_db_getList(1)) {
    $current_version=$version->_db_list[0]['version'];
    $last_check=($version->_db_list[0]['last_version_check']>'0000-00-00 00:00:00')?
                      $current_user->makeDate(PCPIN_Common::datetimeToTimestamp($version->_db_list[0]['last_version_check']))
                    : $l->g('never');
    $new_version_available=$version->_db_list[0]['new_version_available'];
    $new_version_url=$version->_db_list[0]['new_version_url'];
    $version_check_key=$version->_db_list[0]['version_check_key'];
  } else {
    $current_version=6.00;
    $last_check=$l->g('never');
    $new_version_available=$current_version;
    $new_version_url='';
    $version_check_key=PCPIN_Common::randomString(mt_rand(10, 20));
  }
  $version->_db_freeList();
  // Check security key
  if (!empty($version_check_key) && md5($sk)==$version_check_key) {
    if ($session->_db_getList('_s_id', '_s_security_code = '.$version_check_key, 1)) {
      // Security key check passed
      $old_session=$session->_db_list[0]['_s_id'];
      // Save version number
      $version->setLastVersionCheckTime();
      $version->setNewestAvailableVersion($nv);
      $version->setVersionCheckKey();
      $version->setNewVersionDownloadUrl(base64_decode($dl));
      $session->_s_updateSession($old_session, false, true, null, null, null, '');
      header('Location: '.PCPIN_ADMIN_FORMLINK.'?s_id='.$old_session.'&ainc=versions&version_checked');
      die();
    }
  }
}

if (!empty($b_id)) {
  // Binary file requested
  require_once('./inc/get_binary.inc.php');
  die();
} elseif (!empty($ajax)) {
  // AJAX request
  require_once('./inc/ajax/_main.inc.php');
  die();
} elseif (!empty($external_url)) {
  require_once('./inc/url_redirection.inc.php');
  die();
}

// Default window title
$_window_title=$session->_conf_all['chat_name'].' '.PCPIN_WINDOW_TITLE_SEPARATOR.' '.$l->g('administration_area');

// Default: Do not load colorbox
$_load_colorbox=false;

// onLoad event handlers for BODY element
$_body_onload=array('window.focus()',
                    'setSid(\''.$session->_s_id.'\')',
                    'setIP(\''.PCPIN_CLIENT_IP.'\')',
                    'setFormLink(\''.PCPIN_ADMIN_FORMLINK.'\')',
                    'setAdminFormLink(\''.PCPIN_ADMIN_FORMLINK.'\')',
                    'setMainFormLink(\''.PCPIN_FORMLINK.'\')',
                    'setExitURL(\''.htmlspecialchars($session->_conf_all['exit_url']).'\')',
                    'setUserId('.$session->_s_user_id.')',
                    'setImgResizeFlag('.(('2'==PCPIN_GD_VERSION)? 'true' : 'false').')',
                    'window.appName_=\'pcpin_chat\'', // <-- DO NOT CHANGE THIS LINE!!!
                    'window.adminArea_=true', // <-- DO NOT CHANGE THIS LINE!!!
                    'setDateFormat(\''.str_replace('\'', '\\\'', ($current_user->date_format!='')? $current_user->date_format : $session->_conf_all['date_format']).'\')',
                    'setAdminFlag('.($current_user->is_admin==='y'? 'true' : 'false').')',
                    'setSlaveMode('.(PCPIN_SLAVE_MODE? 'true' : 'false').')',
                    'setCurrentRoomID('.$session->_s_room_id.')',
                    );

// onLoad event handlers for main FRAMESET element
$_frameset_onload=array('setMainFormLink(\''.PCPIN_FORMLINK.'\')',
                        'setSid(\''.$session->_s_id.'\')',
                        );

// JavaScript files
$_js_files=array('./js/base/screen.js',
                 './js/base/strings.js',
                 './js/base/time.js',
                 './js/base/xmlhttprequest.js',
                 './js/base/connectionstatus.js',
                 './js/base/global.js',
                 './js/base/main.js',
                 './js/admin/frames.js',
                 './js/base/alertbox.js',
                 './js/base/confirmbox.js',
                 './js/base/promptbox.js',
                 );

// JavaScript language expressions
$_js_lng=array();

// CSS files
$_css_files=array('./main.css');

// Global template variables
$global_tpl_vars=array('s_id'=>htmlspecialchars($session->_s_id),
                       'formlink'=>PCPIN_ADMIN_FORMLINK,
                       'main_formlink'=>PCPIN_FORMLINK,
                       'ainc'=>htmlspecialchars(isset($ainc)? $ainc : ''),
                       );

// Init main template handler
_pcpin_loadClass('pcpintpl'); $template=new PcpinTpl();
$template->setBasedir('./tpl');

if (isset($inc) && $inc=='do_logout') {
  $ainc='do_logout';
}

if (isset($inc) && $inc=='upload') {
  $ainc='upload';
}
if (!isset($ainc) && !empty($session->_s_user_id)) {
  // Load frameset
  $frameset_loaded=true;
  $template->readTemplatesFromFile('./admin/frames.tpl');
  $_frameset_onload[]='initAdminFames()';
} else {
  // Load main template
  $frameset_loaded=false;
  $template->readTemplatesFromFile('./main.tpl');
  $_body_onload[]='window.mainApp_=window.parent;';
}


// Specify the page to load
if (empty($session->_s_user_id)) {
  // Login page
  $hide_account_options=true;
  $admin_login=true;
  require_once('./inc/login.inc.php');
  $_body_onload[]='checkOpener(true)';
} else {
  $_body_onload[]='checkOpener()';

  if (isset($ainc)) {
    switch ($ainc) {

      default:
        // Invalid call
        require_once('./inc/dummy.inc.php');
      break;

      case 'add_new_user':
        // Add new user
        if (!PCPIN_SLAVE_MODE) {
          require_once('./inc/admin/add_new_user.inc.php');
        } else {
          require_once('./inc/dummy.inc.php');
        }
      break;

      case 'avatar_gallery':
        // Manage avatar gallery
        require_once('./inc/admin/avatar_gallery.inc.php');
      break;

      case 'ban_control':
        // Manage banned users
        require_once('./inc/admin/ban_control.inc.php');
      break;

      case 'banners':
        // Manage banners
        require_once('./inc/admin/banners.inc.php');
      break;

      case 'custom_profile_fields':
        // Manage custom profile fields
        require_once('./inc/admin/custom_profile_fields.inc.php');
      break;

      case 'db_backup':
        // Backup database
        require_once('./inc/admin/db_backup.inc.php');
      break;

      case 'db_optimize':
        // Optimize database
        require_once('./inc/admin/db_optimize.inc.php');
      break;

      case 'db_restore':
        // Restore database
        require_once('./inc/admin/db_restore.inc.php');
      break;

      case 'disallow_names':
        // Manage disallowed usernames
        require_once('./inc/admin/disallow_names.inc.php');
      break;

      case 'do_logout':
        // Log out window
        require_once('./inc/do_logout.inc.php');
      break;

      case 'edit_moderator':
        // Edit moderators
        require_once('./inc/admin/edit_moderator.inc.php');
      break;

      case 'header_frame':
        // Header frame
        require_once('./inc/admin/header.inc.php');
      break;

      case 'ip_filter':
        // IP filter
        require_once('./inc/admin/ip_filter.inc.php');
      break;

      case 'languages':
        // Manage languages
        require_once('./inc/admin/languages.inc.php');
      break;

      case 'navigation_frame':
        // Navigation frame
        require_once('./inc/admin/navigation.inc.php');
      break;

      case 'settings':
        // Settings
        require_once('./inc/admin/settings.inc.php');
      break;

      case 'rooms':
        // Manage rooms
        require_once('./inc/admin/rooms.inc.php');
      break;

      case 'show_image':
        // Image window
        require_once('./inc/show_image.inc.php');
      break;

      case 'smilies':
        // Smilies management
        require_once('./inc/admin/smilies.inc.php');
      break;

      case 'translate':
        // Create / edit language translation
        require_once('./inc/admin/translate.inc.php');
      break;

      case 'versions':
        // Version check
        require_once('./inc/admin/versions.inc.php');
      break;

      case 'upload':
        // File upload window
        require_once('./inc/file_upload.inc.php');
      break;

      case 'word_blacklist':
        // Word blacklist
        require_once('./inc/admin/word_blacklist.inc.php');
      break;

    }
  }

}

// Add language expressions to template
foreach ($template->tpl_vars_plain as $var) {
  if (0===strpos($var, 'LNG_')) {
    $var=strtolower($var);
    $template->addGlobalVar($var, htmlspecialchars($l->g(substr($var, 4))));
  }
}

// Add JS language expressions to template
if (!empty($_js_lng) && is_array($_js_lng)) {
  $_js_lng=array_unique($_js_lng);
  foreach ($_js_lng as $lng_key) {
    if ($lng_key!='') {
      $lng_val=str_replace('\'', '\\\'', htmlspecialchars($l->g($lng_key)));
      $lng_val=str_replace("\n", '\\n', str_replace("\r", '\\r', $lng_val));
      array_unshift($_body_onload, 'setLng(\''.str_replace('\'', '\\\'', $lng_key).'\', \''.$lng_val.'\')');
    }
  }
}

// Close database conection
$session->_db_close();

// Add language data to main template
$template->addVar('main', 'iso_lng', $l->iso_name);

// Add title
$template->addVar('main', 'title', htmlspecialchars($_window_title));

// Add global vars to template
foreach ($global_tpl_vars as $key=>$val) {
  $template->addGlobalVar($key, htmlspecialchars($val));
}

// Load colorbox JavaScript code and template?
if ($_load_colorbox) {
  $_js_files[]='./js/base/colorbox.js';
  $template->addVar('colorbox', 'display', true);
}

// Add JavaScript files to template
foreach ($_js_files as $file) {
  if (file_exists($file)) {
    $template->addVar('js_files', 'file', $file.'?'.filemtime($file));
    $template->parseTemplate('js_files', 'a');
  }
}

// Add CSS files to template
foreach ($_css_files as $file) {
  if (file_exists($file)) {
    $template->addVar('css_files', 'file', $file.'?'.filemtime($file));
    $template->parseTemplate('css_files', 'a');
  }
}

// Add OnLoad event handlers for BODY element
if (!empty($_body_onload) && is_array($_body_onload)) {
  $template->addVar('main', 'body_onload', implode(' ; ', $_body_onload));
}

// Add OnLoad event handlers for FRAMESET element
if (!empty($_frameset_onload) && is_array($_frameset_onload)) {
  $template->addVar('main', 'frameset_onload', implode(' ; ', $_frameset_onload));
}

// Get timers
if (PCPIN_DEBUGMODE && PCPIN_LOG_TIMER) {
  $end_times=explode(' ', microtime());
  $start_times=explode(' ', $_pcpin_log_timer_start);
  $start=1*(substr($start_times[1], -5).substr($start_times[0], 1, 5));
  $end=1*(substr($end_times[1], -5).substr($end_times[0], 1, 5));
  $diff=round($end-$start, 3);
  $mysql_usage=round($_GET['_pcpin_log_mysql_usage'], 3);
  $timers="\n<!--\n"
         ."===========================================================================\n"
         ."\tThe page has been generated in total ".($diff+$mysql_usage)." seconds\n"
         ."\t\tPHP time:\t$diff seconds\n"
         ."\t\tMySQL time:\t$mysql_usage seconds\n"
         ."===========================================================================\n"
         ."-->";
  $template->addVar('main', 'timers', $timers);
}

// Send content-type/encoding header
if(!headers_sent()) {
  header('Content-Type: text/html; charset=UTF-8');
  header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
  if(PCPIN_CLIENT_AGENT_NAME=='IE'){
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Pragma: public');
  }else{
    header('Pragma: no-cache');
  }
}

// Display parsed template
if (!empty($tpl) && is_object($tpl)) {
  $template->addVar('main', 'contents', $tpl->getParsedTemplate());
}
// Hide "<!DOCTYPE>" declaration, if needed (IE6 behavior)
$template->addVar('doctype', 'hide', empty($_force_buggy_doctype) && PCPIN_CLIENT_AGENT_NAME=='IE' && (PCPIN_CLIENT_AGENT_VERSION*1)<7);

// Parse and display results
echo ltrim($template->getParsedTemplate());

// Terminate script.
// Warning: do not remove the next line!
die();
?>