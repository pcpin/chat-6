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


if (!file_exists('../extension.inc')) {
  PCPIN_Common::dieWithError(1, 'Slave mode: No phpBB2 installation found');
}

if (empty($_pcpin_init_session->_s_user_id)) {

  // Get parent directory name
  $master_to_chat_path_parts=explode('/', !empty($_SERVER['SCRIPT_FILENAME'])? $_SERVER['SCRIPT_FILENAME'] : (!empty($_SERVER['SCRIPT_NAME'])? $_SERVER['SCRIPT_NAME'] : $_SERVER['PHP_SELF']));
  $chat_dir=$master_to_chat_path_parts[count($master_to_chat_path_parts)-2];
  unset($master_to_chat_path_parts);

  /**
   * phpBB stuff
   */
  define('IN_PHPBB', true);

  // Load master base
  chdir('..');
  $_pcpin_init_session->_db_restoreCharsets();
  require('extension.inc');
  require('common.'.$phpEx);
  chdir($chat_dir);
  $_pcpin_init_session->_db_setCharsets();

  /**
   * phpBB root page
   */
  if (!defined('PCPIN_SLAVE_MASTER_PATH')) define('PCPIN_SLAVE_MASTER_PATH', '..');

  /**
   * phpBB LogIn page
   */
  if (!defined('PCPIN_SLAVE_LOGIN_PATH')) define('PCPIN_SLAVE_LOGIN_PATH', '../login.'.$phpEx);

  /**
   * phpBB LogIn page HTTP method
   */
  if (!defined('PCPIN_SLAVE_LOGIN_METHOD')) define('PCPIN_SLAVE_LOGIN_METHOD', 'get');

  /**
   * phpBB LogIn page additional variable name and value pairs, pairs are separated using "&" character
   */
  if (!defined('PCPIN_SLAVE_LOGIN_VARS')) define('PCPIN_SLAVE_LOGIN_VARS', 'redirect='.$chat_dir);


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
                               'language'         =>  strtolower($phpbb_userdata['default_lang']),
                               'homepage'         =>  null,
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
    $_pcpin_slave_userdata['language']          = strtolower($board_config['default_lang']);
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
    // Recode data
    if (extension_loaded('mbstring')) {
      // "mbstring" extension is loaded, we have best chances to recode the data correctly
      $_pcpin_slave_available_encodings=array('arabic'=>'windows-1256','asturian'=>'iso-8859-1','azerbaijani'=>'UTF-8','belarusian'=>'windows-1251','breton'=>'iso-8859-1','bulgarian'=>'windows-1251','catalan'=>'iso-8859-1','chinese_simplified'=>'gb2312','chinese_traditional'=>'utf-8','croatian'=>'iso-8859-2','czech'=>'Windows-1250','danish'=>'iso-8859-1','dutch'=>'iso-8859-1','english'=>'iso-8859-1','estonian'=>'iso-8859-4','finnish'=>'iso-8859-1','french'=>'ISO-8859-1','galician'=>'iso-8859-1','german'=>'iso-8859-1','german_formal'=>'iso-8859-1','greek'=>'iso-8859-7','hebrew'=>'iso-8859-8-I','hungarian'=>'ISO-8859-2','icelandic'=>'iso-8859-1','italian'=>'iso-8859-1','latvian'=>'windows-1257','lithuanian'=>'windows-1257','macedonian'=>'windows-1251','marathi'=>'UTF-8','mongolian'=>'UTF-8','norwegian'=>'iso-8859-1','norwegian_nynorsk'=>'iso-8859-1','polish'=>'iso-8859-2','romanian'=>'iso-8859-2','russian'=>'windows-1251','serbian'=>'windows-1250','slovak'=>'Windows-1250','slovenian'=>'windows-1250','spanish'=>'iso-8859-1','swedish'=>'iso-8859-1','thai'=>'UTF-8','turkish'=>'iso-8859-9','uighur'=>'iso-8859-1','ukrainian'=>'windows-1251','uzbek'=>'utf-8','valencian'=>'iso-8859-1','vietnamese'=>'utf-8');
      $_pcpin_slave_used_encodings='';
      if (!empty($_pcpin_slave_userdata['language']) && isset($_pcpin_slave_available_encodings[$_pcpin_slave_userdata['language']])) {
        $_pcpin_slave_used_encodings=$_pcpin_slave_available_encodings[strtolower($_pcpin_slave_userdata['language'])];
      }
      if ($_pcpin_slave_used_encodings!='') {
        foreach ($_pcpin_slave_userdata as $_pcpin_slave_userdata_key=>$_pcpin_slave_userdata_val) {
          if ($_pcpin_slave_userdata_val!='' && ''!==$_pcpin_slave_tmp=mb_convert_encoding(utf8_decode($_pcpin_slave_userdata_val), 'UTF-8', $_pcpin_slave_used_encodings)) {
            $_pcpin_slave_userdata[$_pcpin_slave_userdata_key]=$_pcpin_slave_tmp;
          }
        }
      }
      unset($_pcpin_slave_available_encodings);
      unset($_pcpin_slave_used_encodings);
      unset($_pcpin_slave_tmp);
    }
  } else {
    // Guest
    $_pcpin_slave_userdata['is_guest']='y';
  }
  // Define language
  if ($_pcpin_slave_userdata['language']!='') {
    $_pcpin_slave_tmp=get_defined_constants();
    foreach ($_pcpin_slave_tmp as $_pcpin_slave_tmp2=>$_pcpin_slave_tmp3) {
      if (substr($_pcpin_slave_tmp2, 0, 14)=='PCPIN_ISO_LNG_') {
        if ($_pcpin_slave_userdata['language']==strtolower(substr($_pcpin_slave_tmp3, 3))) {
          $_pcpin_slave_userdata['language']=substr($_pcpin_slave_tmp3, 0, 2);
          break;
        }
      }
    }
    unset($_pcpin_slave_tmp);
    unset($_pcpin_slave_tmp2);
    unset($_pcpin_slave_tmp3);
  }
}

?>