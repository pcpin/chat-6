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

if (file_exists('./install')) {
  die('<html><body><center><br /><br /><br /><br /><h3>Chat locked</h3><br />Delete directory <b>install</b> in order to continue.</center></body></html>');
}

if (function_exists('debug_backtrace')) {
  $_pcpin_dbt=debug_backtrace();
  if (is_array($_pcpin_dbt) && (!isset($_pcpin_dbt[0]) || dirname($_pcpin_dbt[0]['file']) !== dirname(__FILE__))) {
    die('Access denied');
  }
  unset($_pcpin_dbt);
}

/**
 * PHP version
 */
$_pcpin_php_version=(defined('PHP_VERSION') && PHP_VERSION!='')? PHP_VERSION : phpversion();
define('PCPIN_PHP_VERSION', substr($_pcpin_php_version, 0, strpos($_pcpin_php_version, '.')));
unset($_pcpin_php_version);

// Chat root directory
define('PCPIN_CHAT_ROOT_DIR', str_replace('\\', '/', realpath(dirname(__FILE__))));

// Load functons
require_once('./funcs.inc.php');


// Load static configuration
require_once('./config/config.inc.php');


// Start timers
if (PCPIN_DEBUGMODE && PCPIN_LOG_TIMER) {
  $_pcpin_log_timer_start=microtime();
}


// Activate debugging
if (PCPIN_DEBUGMODE) {
  @ini_set('display_errors', 'on');
  @ini_set('html_errors', 'off');
  if (PCPIN_DEBUGMODE_STRICT && defined('E_STRICT')) {
    if (defined('E_DEPRECATED')) {
      error_reporting((E_ALL|E_STRICT) & ~E_DEPRECATED);
    } else {
      error_reporting(E_ALL|E_STRICT);
    }
  } else {
    if (defined('E_STRICT')) {
      if (defined('E_DEPRECATED')) {
        error_reporting(E_ALL & ~E_STRICT);
      } else {
        error_reporting((E_ALL & ~E_STRICT) & ~E_DEPRECATED);
      }
    } else {
      if (defined('E_DEPRECATED')) {
        error_reporting(E_ALL & ~E_DEPRECATED);
      } else {
        error_reporting(E_ALL);
      }
    }
  }
  if (PCPIN_ERRORLOG!='') {
    // Log errors into file using custom error handler function
    if (function_exists('PCPIN_ErrorHandler')) {
      set_error_handler('PCPIN_ErrorHandler');
    }
  }
} else {
  // No errors will be displayed
  error_reporting(0);
  @ini_set('display_errors', 'off');
}


// Load static classes
if (false&& PCPIN_PHP5) { // Todo
  // Load static classes for PHP5 (we want to avoid E_STRICT errors)
  _pcpin_loadClass('common5');
  _pcpin_loadClass('image5');
  _pcpin_loadClass('tcp5');
  _pcpin_loadClass('ping5');
  _pcpin_loadClass('email5');
} else {
  _pcpin_loadClass('common');
  _pcpin_loadClass('image');
  _pcpin_loadClass('tcp');
  _pcpin_loadClass('ping');
  _pcpin_loadClass('email');
}


/**
 * Get client's IP address
 */
define('PCPIN_CLIENT_IP', (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']!='')? $_SERVER['REMOTE_ADDR'] : ((isset($HTTP_SERVER_VARS['REMOTE_ADDR']) && $HTTP_SERVER_VARS['REMOTE_ADDR']!='')? $HTTP_SERVER_VARS['REMOTE_ADDR'] : 'UNKNOWN'));


/**
 * Get client info
 */
$_pcpin_os='';
$_pcpin_agent_name='';
$_pcpin_agent_ver='';
PCPIN_Common::getClientInfo($_SERVER['HTTP_USER_AGENT'], $_pcpin_os, $_pcpin_agent_name, $_pcpin_agent_ver);
define('PCPIN_CLIENT_OS', $_pcpin_os);
define('PCPIN_CLIENT_AGENT_NAME', $_pcpin_agent_name);
define('PCPIN_CLIENT_AGENT_VERSION', $_pcpin_agent_ver);
unset($_pcpin_os);
unset($_pcpin_agent_name);
unset($_pcpin_agent_ver);


// Create initial class
$__pcpin_init_class=new stdClass();
$__pcpin_init_class->_cache=array(); // Cahced data (to be used by all child objects)


// Connect to database
require('./config/db.inc.php');
_pcpin_loadClass('db'); new PCPIN_DB($__pcpin_init_class, ${$_pcpin_dbcn});

// Finish upgrade, if needed
if (file_exists('./upgrade.php')) {
  define('PCPIN_UPGRADE', true);
  require_once('./upgrade.php');
  die();
}

// Load configuration
_pcpin_loadClass('config'); new PCPIN_Config($__pcpin_init_class);

// Define "Slave mode" flag
define('PCPIN_SLAVE_MODE', !empty($__pcpin_init_class->_conf_all['slave_mode']) && trim($__pcpin_init_class->_conf_all['slave_mode_master'])!='');

// Initialize session
$s_id='';
if (isset($_GET['s_id']) && is_scalar($_GET['s_id'])) {
  $s_id=$_GET['s_id'];
} elseif (isset($_POST['s_id']) && is_scalar($_POST['s_id'])) {
  $s_id=$_POST['s_id'];
}/* elseif (isset($_COOKIE['s_id']) && is_scalar($_COOKIE['s_id'])) {
  $s_id=$_COOKIE['s_id'];
}*/
_pcpin_loadClass('session'); $_pcpin_init_session=new PCPIN_Session($__pcpin_init_class, $s_id, !empty($_GET['b_id']) || !empty($_GET['external_url']) || !empty($_GET['load_banner']) || defined('PCPIN_NO_SESSION'));

// Kill init class (session is a root class now)
unset($__pcpin_init_class);

// Slave mode?
if (PCPIN_SLAVE_MODE && ( !defined('PCPIN_ADMIN_ACCESS') || !PCPIN_ADMIN_ACCESS ) && empty($_GET['b_id']) && empty($_GET['external_url']) && empty($_GET['load_banner']) && !defined('PCPIN_NO_SESSION')) {
  // Check mod file
  $_pcpin_mod_filename='./mods/slave/'.trim($_pcpin_init_session->_conf_all['slave_mode_master']).'/'.trim($_pcpin_init_session->_conf_all['slave_mode_master']).'.php';
  if (!file_exists($_pcpin_mod_filename) || !is_file($_pcpin_mod_filename) || !is_readable($_pcpin_mod_filename)) {
    // Mod file not exists or not readable
    die('Slave mode: Master file "'.$_pcpin_mod_filename.'" does not exists or not readable');
  } else {
    // Load mod file
    require($_pcpin_mod_filename);
    // Reinit session
    $__pcpin_init_class=new stdClass();
    $__pcpin_init_class->_cache=array();
    new PCPIN_DB($__pcpin_init_class, ${$_pcpin_dbcn});
    new PCPIN_Config($__pcpin_init_class);
    $_pcpin_init_session=new PCPIN_Session($__pcpin_init_class, $s_id, !empty($_GET['b_id']) || !empty($_GET['external_url']) || !empty($_GET['load_banner']) || defined('PCPIN_NO_SESSION'));
    unset($__pcpin_init_class);
  }
}
unset(${$_pcpin_dbcn});
unset($_pcpin_dbcn);

// Get software version
if (!defined('PCPIN_NO_SESSION') && empty($_GET['external_url'])) {
  _pcpin_loadClass('version'); $_pcpin_version=new PCPIN_Version($_pcpin_init_session);
  if ($_pcpin_version->_db_getList('version', 1)) {
    define('PCPIN_VERSION', number_format($_pcpin_version->_db_list[0]['version'], 2, '.', ''));
    $_pcpin_version->_db_freeList();
    unset($_pcpin_version);
  } else {
    define('PCPIN_VERSION', '0.00');
  }
}


// Load language
if (!defined('PCPIN_NO_SESSION')) {
  if (empty($_GET['b_id']) && empty($_GET['external_url'])) {
    _pcpin_loadClass('language'); $l=new PCPIN_Language($_pcpin_init_session);
    $_pcpin_set_language=$_pcpin_init_session->_s_language_id;
    if (!empty($_pcpin_init_session->_conf_all['allow_language_selection']) && !empty($_POST['language_id'])) {
      $_pcpin_set_language=$_POST['language_id'];
    }
    if (true!==$l->setLanguage($_pcpin_set_language)) {
      PCPIN_Common::dieWithError(-1, '<b>Fatal error</b>: Failed to load language');
    }
    if (!empty($_pcpin_init_session->_s_id) && $l->id!=$_pcpin_init_session->_s_language_id) {
      $_pcpin_init_session->_s_updateSession($_pcpin_init_session->_s_id, true, true, $l->id);
    }
    unset($_pcpin_set_language);
  }
}


/**
 * Strip magic quotes from GPC vars and extract them into the global scope.
 * This software uses own security algorithm to prevent SQL injections.
 */
if (get_magic_quotes_gpc()) {
  $_pcpin_magic_quotes_sybase=ini_get('magic_quotes_sybase')=='1';
  $_GET=PCPIN_Common::stripSlashesRecursive($_GET, $_pcpin_magic_quotes_sybase);
  $_POST=PCPIN_Common::stripSlashesRecursive($_POST, $_pcpin_magic_quotes_sybase);
  $_COOKIE=PCPIN_Common::stripSlashesRecursive($_COOKIE, $_pcpin_magic_quotes_sybase);
//  $_SESSION=PCPIN_Common::stripSlashesRecursive($_SESSION, $_pcpin_magic_quotes_sybase); // <-- not needed yet
  unset($_pcpin_magic_quotes_sybase);
}



/**
 * Yes, we extract GPC+F superglobals into the global scope.
 * This software knows, how to handle them.
 */

// $_GET vars
extract($_GET);

// $_POST vars
extract($_POST);

// $_COOKIE vars
$_pcpin_cookies_found=!empty($_COOKIE);
//extract($_COOKIE); // <- not needed yet

// Posted files into the global scope
extract($_FILES);


/**
 * Clean some globals and superglobals
 */

if (!defined('PCPIN_NO_SESSION')) unset($GLOBALS);
if (!defined('PCPIN_NO_SESSION')) unset($_SESSION);
if (!defined('PCPIN_NO_SESSION')) unset($_FILES);
if (!defined('PCPIN_NO_SESSION')) unset($_COOKIE);
if (!defined('PCPIN_NO_SESSION')) unset($_POST);
if (!defined('PCPIN_NO_SESSION')) {
  if (isset($_GET['_pcpin_log_mysql_usage'])) {
    $_GET=array('_pcpin_log_mysql_usage'=>$_GET['_pcpin_log_mysql_usage']);
  } else {
    unset($_GET);
  }
}
if (!defined('PCPIN_NO_SESSION')) unset($_REQUEST);
if (!defined('PCPIN_NO_SESSION')) unset($HTTP_GET_VARS);
if (!defined('PCPIN_NO_SESSION')) unset($HTTP_POST_VARS);
if (!defined('PCPIN_NO_SESSION')) unset($HTTP_POST_FILES);
if (!defined('PCPIN_NO_SESSION')) unset($HTTP_COOKIE_VARS);
if (!defined('PCPIN_NO_SESSION')) unset($HTTP_SESSION_VARS);
?>