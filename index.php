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

// Initialize
require_once('./init.inc.php');


// Get session handler
$session=&$_pcpin_init_session;
unset($_pcpin_init_session);


_pcpin_loadClass('user'); $current_user=new PCPIN_User($session);


// Slave mode: need login?
if (PCPIN_SLAVE_MODE && empty($session->_s_user_id) && empty($b_id) && empty($external_url) && empty($load_banner) && !defined('PCPIN_NO_SESSION')) {
  // User is not logged in yet
  require('./mods/slave_mode.inc.php');
}

// Current user data
$_is_moderator=false;
$current_nickname='';
$current_room_name='';
if (!empty($session->_s_user_id)) {
  $current_user->_db_loadObj($session->_s_user_id);
  if (!empty($session->_s_room_id) && $current_user->moderated_rooms!='') {
    $_is_moderator=false!==strpos(','.$current_user->moderated_rooms.',', ','.$session->_s_room_id.',');
  }
  _pcpin_loadClass('nickname'); $nickname_=new PCPIN_Nickname($session);
  $current_nickname=$nickname_->getDefaultNickname($current_user->id);
  unset($nickname_);
  if (!empty($session->_s_room_id)) {
    _pcpin_loadClass('room'); $room_=new PCPIN_Room($session);
    if ($room_->_db_getList('name', 'id = '.$session->_s_room_id, 1)) {
      $current_room_name=$room_->_db_list[0]['name'];
    }
  }
}

if (!empty($b_id)) {
  // Binary file requested
  require_once('./inc/get_binary.inc.php');
  die();
} elseif (!empty($ajax) && is_scalar($ajax)) {
  // AJAX request
  require_once('./inc/ajax/_main.inc.php');
  die();
} elseif (!empty($external_url)) {
  // Redirect to an external URL
  require_once('./inc/url_redirection.inc.php');
  die();
} elseif (!empty($load_banner)) {
  // Load banner
  require_once('./inc/load_banner.inc.php');
  die();
} elseif (isset($inc) && $inc=='page_unloaded') {
  // Client unloaded chat page
  require_once('./inc/page_unloaded.inc.php');
  die();
}

// Default window title
$_window_title=$session->_conf_all['chat_name'];

// Default: Do not load colorbox
$_load_colorbox=false;

// Default: Do not load smiliebox
$_load_smiliebox=false;

// Default: Do not context menu user options
$_load_cm_user_options=false;

// onLoad event handlers for BODY element
$_body_onload=array('setSid(\''.$session->_s_id.'\')',
                    'setIP(\''.PCPIN_CLIENT_IP.'\')',
                    'setFormLink(\''.PCPIN_FORMLINK.'\')',
                    'setAdminFormLink(\''.PCPIN_ADMIN_FORMLINK.'\')',
                    'setMainFormLink(\''.PCPIN_FORMLINK.'\')',
                    'setExitURL(\''.htmlspecialchars($session->_conf_all['exit_url']).'\')',
                    'setUserId('.$session->_s_user_id.')',
                    'setImgResizeFlag('.(('2'==PCPIN_GD_VERSION)? 'true' : 'false').')',
                    'window.appName_=\'pcpin_chat\'', // <-- DO NOT CHANGE THIS LINE!!!
                    'setDateFormat(\''.str_replace('\'', '\\\'', ($current_user->date_format!='')? $current_user->date_format : $session->_conf_all['date_format']).'\')',
                    'setAdminFlag('.($current_user->is_admin==='y'? 'true' : 'false').')',
                    'startMousePosCapture()',
                    'setSlaveMode('.(PCPIN_SLAVE_MODE? 'true' : 'false').')',
                    'setCurrentRoomID('.$session->_s_room_id.')',
                    );

// JavaScript files
$_js_files=array('./js/base/screen.js',
                 './js/base/strings.js',
                 './js/base/time.js',
                 './js/base/xmlhttprequest.js',
                 './js/base/connectionstatus.js',
                 './js/base/global.js',
                 './js/base/main.js',
                 './js/base/alertbox.js',
                 './js/base/confirmbox.js',
                 './js/base/promptbox.js',
                 );

// Add mp3 player javascript code
if (!empty($session->_conf_all['allow_sounds'])) {
  $_js_files[]='./js/base/mp3_player.js';
}

// JavaScript language expressions
$_js_lng=array('password', 'ok', 'cancel');

// CSS files
$_css_files=array('./main.css');

// Global template variables
$global_tpl_vars=array('s_id'=>$session->_s_id, 'formlink'=>PCPIN_FORMLINK);

// Init main template handler
_pcpin_loadClass('pcpintpl'); $template=new PcpinTpl();
$template->setBasedir('./tpl');
$template->readTemplatesFromFile('./main.tpl');

// Add language data to main template
$template->addVar('main', 'iso_lng', $l->iso_name);

// Default inc
if (!isset($inc)) $inc='';

// Specify the page to load
if (empty($session->_s_user_id)) {
  if (!empty($activation_code)) {
    // Something to activate...
    require_once('./inc/activation.inc.php');
  } else {
    // Login page
    if (PCPIN_SLAVE_MODE) {
      header('Content-Type: text/html; charset=UTF-8');
      header('Expires: '.gmdate('D, d M Y H:i:s').' GMT');
      echo '<html><head><meta http-equiv="Content-Type" content="text/xml; charset=utf-8" /></head><body onload="rdrForm.submit()"><form id="rdrForm" action="'.PCPIN_SLAVE_LOGIN_PATH.'" method="'.PCPIN_SLAVE_LOGIN_METHOD.'">';
      if (PCPIN_SLAVE_LOGIN_VARS!='') {
        $pairs=explode('&', PCPIN_SLAVE_LOGIN_VARS);
        foreach ($pairs as $pair) {
          echo '<input type="hidden" name="'.htmlspecialchars(substr($pair, 0, strpos($pair, '='))).'" value="'.htmlspecialchars(substr($pair, strpos($pair, '=')+1)).'" />';
        }
      }
      echo '</form></body></html>';
      die();
    } else {
      require_once('./inc/login.inc.php');
      $_body_onload[]='checkOpener(true)';
    }
  }
} else {
  $_body_onload[]='checkOpener()';
  switch ($inc) {

    case 'abuse':
      // Abuse window
      require_once('./inc/abuse.inc.php');
    break;

    case 'avatar_gallery':
      // Avatar Gallery
      require_once('./inc/avatar_gallery.inc.php');
    break;

    case 'call_moderator':
      // "Call moderator" window
      require_once('./inc/call_moderator.inc.php');
    break;

    case 'chat_room':
      // Chat room page
      require_once('./inc/chat_room.inc.php');
    break;

    case 'client_info':
      // Client info page
      require_once('./inc/client_info.inc.php');
    break;

    case 'create_user_room':
      // "Create user room" page
      require_once('./inc/create_user_room.inc.php');
    break;

    case 'do_logout':
      // Log out window
      require_once('./inc/do_logout.inc.php');
    break;

    case 'dummy':
      // Dummy frame
      require_once('./inc/dummy.inc.php');
    break;

    case 'invitation':
      // An invitation arrived
      require_once('./inc/invitation.inc.php');
    break;

    case 'memberlist':
      // Display memberlist
      require_once('./inc/memberlist.inc.php');
    break;

    case 'pm_box':
      // PM box
      require_once('./inc/pm_box.inc.php');
    break;

    case 'profile_main':
      // User profile page: frameset
      require_once('./inc/profile_main.inc.php');
    break;

    case 'profile_main_navigation':
      // User profile page: navigation frame
      require_once('./inc/profile_main_navigation.inc.php');
    break;

    case 'profile_main_contents':
      // User profile page: contents frame
      require_once('./inc/profile_main_contents.inc.php');
    break;

    case 'profile_public':
      // User public profile page
      require_once('./inc/profile_public.inc.php');
    break;

    case 'room_selection':
      // Room selection page
      require_once('./inc/room_selection.inc.php');
    break;

    case 'show_image':
      // Image window
      require_once('./inc/show_image.inc.php');
    break;

    case 'upload':
      // File upload window
      require_once('./inc/file_upload.inc.php');
    break;

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

// Add title
$template->addVar('main', 'title', htmlspecialchars($_window_title));

// Add global vars to template
foreach ($global_tpl_vars as $key=>$val) {
  $template->addGlobalVar($key, htmlspecialchars($val));
}

// Load colorbox JavaScript code and template
if ($_load_colorbox) {
  $_js_files[]='./js/base/colorbox.js';
  $template->addVar('colorbox', 'display', true);
}

// Load smiliebox JavaScript code and template
if ($_load_smiliebox) {
  $_js_files[]='./js/base/smiliebox.js';
  $template->addVar('smiliebox_tpl', 'display', true);
}

// Load context menu user options template
if ($_load_cm_user_options) {
  $template->addVar('context_menu_user_options', 'display', true);
  $_js_files[]='./js/context_menu_user_options.js';
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

// Add "oncontextmenu" handler
$template->addVar('main', 'body_oncontextmenu', PCPIN_DEBUGMODE? 'return true' : 'return false');

// Add mp3 player template
if (!empty($session->_conf_all['allow_sounds'])) {
  $template->addVar('mp3_player', 'player', './sounds/pcpin_mp3.swf?'.filemtime('./sounds/pcpin_mp3.swf'));
}

// Get timers
if (PCPIN_DEBUGMODE && PCPIN_LOG_TIMER) {
  $end_times=explode(' ', microtime());
  $start_times=explode(' ', $_pcpin_log_timer_start);
  $start=1*(substr($start_times[1], -5).substr($start_times[0], 1, 5));
  $end=1*(substr($end_times[1], -5).substr($end_times[0], 1, 5));
  $diff=$end-$start;
  $mysql_usage=$_GET['_pcpin_log_mysql_usage'];
  $timers="\n<!--\n"
         ."===========================================================================\n"
         ."\tThe page has been generated in total ".number_format($diff, 3, '.', '')." seconds\n"
         ."\t\tPHP time:\t".number_format($diff-$mysql_usage, 3, '.', '')." seconds\n"
         ."\t\tMySQL time:\t".number_format($mysql_usage, 3, '.', '')." seconds\n"
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
// Hide "<!DOCTYPE>" declaration, if needed (IE6 bug)
$template->addVar('doctype', 'hide', empty($_force_buggy_doctype) && PCPIN_CLIENT_AGENT_NAME=='IE' && (PCPIN_CLIENT_AGENT_VERSION*1)<7);

// Parse and display results
echo ltrim($template->getParsedTemplate());

// Terminate script.
// Warning: do not remove next line!
die();
?>