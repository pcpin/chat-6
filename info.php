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




/**
 * !!! DON'T EDIT THIS FILE, EDIT info_template.php instead !!!
 */

// Remember current working directory
$pcpin_old_cwd=getcwd();

// Switch to new working directory
chdir(dirname(__FILE__));

// Remember current error reporting settings
$_pcpin_old_error_reporting=error_reporting();
$_pcpin_old_display_errors=ini_get('display_errors');

// Do not create new session
if (!defined('PCPIN_NO_SESSION')) define('PCPIN_NO_SESSION', true);

// Initialize
require('./init.inc.php');

// Load required classes
_pcpin_loadClass('user'); $_pcpin_user=new PCPIN_User($_pcpin_init_session);
_pcpin_loadClass('nickname'); $_pcpin_nickname=new PCPIN_Nickname($_pcpin_init_session);
_pcpin_loadClass('room'); $_pcpin_room=new PCPIN_Room($_pcpin_init_session);

// Initialize vars
$_pcpin_online_users_count=0;
$_pcpin_online_users=array();
$_pcpin_online_users_colored=array();
$_pcpin_registered_users_count=0;
$_pcpin_registered_users=array();
$_pcpin_registered_users_colored=array();
$_pcpin_rooms_count=0;
$_pcpin_rooms=array();

// Get full memberlist
$_pcpin_memberlist=$_pcpin_user->getMemberlist(false, 0, 0, 1, 0);
foreach ($_pcpin_memberlist as $_pcpin_data) {
  if ($_pcpin_data['online_status']>0) {
    $_pcpin_online_users_count++;
    $_pcpin_online_users[]=htmlspecialchars($_pcpin_data['nickname_plain']);
    $_pcpin_online_users_colored[]=$_pcpin_nickname->coloredToHTML($_pcpin_data['nickname']);
  }
  if (empty($_pcpin_data['is_guest'])) {
    $_pcpin_registered_users_count++;
    $_pcpin_registered_users[]=htmlspecialchars($_pcpin_data['nickname_plain']);
    $_pcpin_registered_users_colored[]=$_pcpin_nickname->coloredToHTML($_pcpin_data['nickname']);
  }
}
unset($_pcpin_memberlist);
unset($_pcpin_user);
unset($_pcpin_nickname);

// Get rooms
$_pcpin_rooms_count=$_pcpin_room->_db_getList('name', 'name ASC');
foreach ($_pcpin_room->_db_list as $_pcpin_data) {
  $_pcpin_rooms[]=htmlspecialchars($_pcpin_data['name']);
}
$_pcpin_room->_db_freeList();
unset($_pcpin_room);

// Close database connection
$_pcpin_init_session->_db_close();

// Delete session handler
unset($_pcpin_init_session);

// Restore original error reporting settings
error_reporting($_pcpin_old_error_reporting); unset($_pcpin_old_error_reporting);
@ini_set('display_errors', $_pcpin_old_display_errors); unset($_pcpin_old_display_errors);

// Restore original error handler
restore_error_handler();

// Restore old working directory
chdir($pcpin_old_cwd); unset($pcpin_old_cwd);

// Clean SQL usage timer
unset($_GET['_pcpin_log_mysql_usage']);

// Load and display info template
require('./info_template.php');

?>