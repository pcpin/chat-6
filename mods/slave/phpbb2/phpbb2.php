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
 * Relative path from phpBB directory to chat root directory
 * @var   string
 */
$phpbb_to_chat_path= '../pcpin6/';

/**
 * Relative path from chat root directory to phpBB directory
 * @var   string
 */
$phpbb_root_path = '../phpbb/';





///////////////////////////////////////////////////////////////////////
// YOU DON'T NEED TO EDIT ANYTHING BELOW THIS LINE !
///////////////////////////////////////////////////////////////////////

if (!file_exists($phpbb_root_path.'extension.inc')) {
  PCPIN_Common::dieWithError(1, 'Slave mode: No phpBB2 installation found');
}

if (empty($_pcpin_init_session->_s_user_id)) {
  /**
   * phpBB stuff
   */
  define('IN_PHPBB', true);

  // Load master base
  require($phpbb_root_path.'extension.inc');
  require($phpbb_root_path.'common.'.$phpEx);

  /**
   * phpBB root page
   */
  define('PCPIN_SLAVE_MASTER_PATH', $phpbb_root_path);

  /**
   * phpBB LogIn page
   */
  define('PCPIN_SLAVE_LOGIN_PATH', $phpbb_root_path.'login.'.$phpEx.'?redirect='.$phpbb_to_chat_path);

  // Supported data
  $_pcpin_slave_userdata=array('login'            =>  null,
                               'password'         =>  null,
                               'email'            =>  null,
                               'hide_email'       =>  null,
                               'joined'           =>  null,
                               'date_format'      =>  null,
                               'is_admin'         =>  null,
                               'banned_until'     =>  null,
                               'time_zone_offset' =>  null,
                               'is_guest'         =>  null,
                               'homapage'         =>  null,
                               'gender'           =>  null,
                               'age'              =>  null,
                               'icq'              =>  null,
                               'msn'              =>  null,
                               'aim'              =>  null,
                               'yim'              =>  null,
                               'location'         =>  null,
                               'occupation'       =>  null,
                               'interests'        =>  null,
                               'avatar'           =>  null,
                               'is_moderator'     =>  null,
                               );

  // Get userdata
  $phpbb_userdata=session_pagestart($user_ip, PAGE_INDEX);

  if (!empty($phpbb_userdata['user_active'])) {
    // Logged in user
    $_pcpin_slave_userdata['login']             = $phpbb_userdata['username'];
    $_pcpin_slave_userdata['password']          = $phpbb_userdata['user_password'];
    $_pcpin_slave_userdata['email']             = $phpbb_userdata['user_email'];
    $_pcpin_slave_userdata['hide_email']        = empty($phpbb_userdata['user_viewemail'])? 'y' : 'n';
    $_pcpin_slave_userdata['joined']            = date('Y-m-d H:i:s', $phpbb_userdata['user_regdate']*1);
    $_pcpin_slave_userdata['date_format']       = $phpbb_userdata['user_dateformat'];
    $_pcpin_slave_userdata['is_admin']          = (defined('ADMIN') && $phpbb_userdata['user_level']==ADMIN)? 'y' : 'n';
    $_pcpin_slave_userdata['time_zone_offset']  = $phpbb_userdata['user_timezone']*3600;
    $_pcpin_slave_userdata['is_guest']          = 'n';
    $_pcpin_slave_userdata['homepage']          = $phpbb_userdata['user_website'];
    $_pcpin_slave_userdata['icq']               = $phpbb_userdata['user_icq'];
    $_pcpin_slave_userdata['msn']               = $phpbb_userdata['user_msnm'];
    $_pcpin_slave_userdata['aim']               = $phpbb_userdata['user_aim'];
    $_pcpin_slave_userdata['yim']               = $phpbb_userdata['user_yim'];
    $_pcpin_slave_userdata['location']          = $phpbb_userdata['user_from'];
    $_pcpin_slave_userdata['occupation']        = $phpbb_userdata['user_occ'];
    $_pcpin_slave_userdata['interests']         = $phpbb_userdata['user_interests'];
    $_pcpin_slave_userdata['is_moderator']      = (defined('MOD') && $phpbb_userdata['user_level']==MOD)? 'y' : 'n';
    // Get avatar
    if (!empty($phpbb_userdata['user_avatar'])) {
      $_pcpin_slave_userdata['avatar']=dirname(PCPIN_SLAVE_MASTER_PATH.'/dummy').'/images/avatars/'.$phpbb_userdata['user_avatar'];
    }
  } else {
    // Guest
    $_pcpin_slave_userdata['is_guest']='y';
  }
}

?>