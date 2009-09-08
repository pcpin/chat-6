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
 * Class PCPIN_User
 * Manage users
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2006, Konstantin Reznichak
 */
class PCPIN_User extends PCPIN_Session {

  /**
   * User ID
   * @var   int
   */
  var $id=0;

  /**
   * Login name
   * @var   string
   */
  var $login='';

  /**
   * Password (MD5 encoded)
   * @var   string
   */
  var $password='';

  /**
   * New password (MD5 encoded)
   * @var   string
   */
  var $password_new='';

  /**
   * Email address
   * @var   string
   */
  var $email='';

  /**
   * New Email address (not not activated)
   * @var   string
   */
  var $email_new='';

  /**
   * Date the new Email address has been requested (MySQL DATETIME)
   * @var   string
   */
  var $email_new_date='';

  /**
   * Activation code for new email address
   * @var   string
   */
  var $email_new_activation_code='';

  /**
   * Hide email address from other users? (0: No, 1: Yes)
   * @var   int
   */
  var $hide_email=0;

  /**
   * Previous time user have logged in (MySQL DATETIME)
   * @var   string
   */
  var $previous_login='';

  /**
   * Last time user have logged in (MySQL DATETIME)
   * @var   string
   */
  var $last_login='';

  /**
   * Join date (MySQL DATETIME)
   * @var   string
   */
  var $joined='';

  /**
   * Flag: 'y' if account has been activated, 'n' if not
   * @var   string
   */
  var $activated='';

  /**
   * Account activation code (MD5-encoded)
   * @var   string
   */
  var $activation_code='';

  /**
   * Total time spent online, in seconds
   * @var   int
   */
  var $time_online=0;

  /**
   * Date format. Date Format. The syntax used is identical to the PHP date() function.
   * @var   string
   */
  var $date_format='';

  /**
   * ID of last message received by user (this field will be chenged after user log out)
   * @var   int
   */
  var $last_message_id=0;

  /**
   * IDs of rooms which are moderated by the user (as comma-separated list)
   * @var   string
   */
  var $moderated_rooms='';

  /**
   * IDs of categories which are moderated by the user (as comma-separated list)
   * @var   string
   */
  var $moderated_categories='';

  /**
   * Flag: SUPERUSER ("y"/"n")
   * @var   string
   */
  var $is_admin='';

  /**
   * If user is banned: Who banned him? (User ID)
   * @var   int
   */
  var $banned_by=0;

  /**
   * If user is banned: Who banned him? (Username)
   * @var   string
   */
  var $banned_by_username='';

  /**
   * Ban date (MySQL DATETIME). Empty value or "0000-00-00 00:00:00" means user is not banned
   * @var   string
   */
  var $banned_until='';

  /**
   * Flag: If set to "y", then user is permanently banned
   * @var   string
   */
  var $banned_permanently='';

  /**
   * If user is banned: ban reason
   * @var   string
   */
  var $ban_reason='';

  /**
   * IDs of users which are ignored by this user (comma-separated list)
   * @var   string
   */
  var $muted_users='';

  /**
   * If user is global muted: Who muted him? (User ID)
   * @var   int
   */
  var $global_muted_by=0;

  /**
   * If user is global muted: Who muted him? (Username)
   * @var   string
   */
  var $global_muted_by_username='';

  /**
   * If user is global muted: mute expiration date as MySQL DATETIME
   * @var   string
   */
  var $global_muted_until='';

  /**
   * Flag: If set to "y", then user is permanently muted
   * @var   string
   */
  var $global_muted_permanently='';

  /**
   * If user is global muted: mute reason
   * @var   string
   */
  var $global_muted_reason='';

  /**
   * Time zone offset in seconds
   * @var   int
   */
  var $time_zone_offset=0;

  /**
   * Flag: "y" if user is a guest, "n" if user was registered
   * @var   string
   */
  var $is_guest='';

  /**
   * Flag: "y", if user can see message timestamp
   * @var   string
   */
  var $show_message_time='';

  /**
   * Currently used messages color
   * @var   string
   */
  var $outgoing_message_color='';

  /**
   * Language ID
   * @var   int
   */
  var $language_id=0;

  /** 
   * Flag: "y", if user can hear sounds
   * @var   string
   */
  var $allow_sounds='';

  /** 
   * Preferred room selection view, "s" : simplified or "a" advanced
   * @var   string
   */
  var $room_selection_view='';



  /**
   * Constructor. Initialize User class.
   * @param   object  &$sessionhandler  Session handler
   */
  function PCPIN_User(&$sessionhandler) {
    // Init object
    $this->_s_init($sessionhandler, $this);
  }


  /**
   * Insert new user into database
   * @param   string    $login            Login name
   * @param   string    $password         Password (NOT encoded!!!)
   * @param   string    $email            E-Mail address
   * @param   int       $hide_email       Hide E-Mail address? (0: No, 1: Yes)
   * @param   string    $guest            Flag: "y" if user is a guest, "n" if user was registered
   * @param   string    $activation_code  If new account activation enabled: Activation code (MD5-encoded)
   * @param   int       $language_id      Language ID. If empty: language ID from current session will be used
   * @return  boolean TRUE on success or FALSE on error
   */
  function newUser($login, $password='', $email='', $hide_email=0, $guest='n', $activation_code='', $language_id=0) {
    $result=false;
    $this->id=0;
    $login=trim($login);
    $email=trim($email);
    if ($login!='' && $password!='') {
      $this->id=0;
      $this->login=$login;
      $this->password=md5($password);
      $this->password_new=md5(PCPIN_Common::randomString(mt_rand(100, 255)));
      $this->email=$email;
      $this->email_new='';
      $this->email_new_date='';
      $this->email_new_activation_code='';
      $this->hide_email=$hide_email;
      $this->joined=date('Y-m-d H:i:s');
      $this->activated=($activation_code=='')? 'y' : 'n';
      $this->activation_code=$activation_code;
      $this->last_login='';
      $this->previous_login='';
      $this->time_online=0;
      $this->date_format=$this->_conf_all['date_format'];
      $this->last_message_id=0;
      $this->moderated_rooms='';
      $this->moderated_categories='';
      $this->is_admin='n';
      $this->banned_by=0;
      $this->banned_by_username='';
      $this->banned_until='';
      $this->banned_permanently='n';
      $this->ban_reason='';
      $this->muted_users='';
      $this->global_muted_by=0;
      $this->global_muted_by_username='';
      $this->global_muted_until='';
      $this->global_muted_permanently='n';
      $this->global_muted_reason='';
      $this->time_zone_offset=0;
      $this->is_guest=$guest;
      $this->show_message_time='';
      $this->outgoing_message_color='';
      $this->language_id=!empty($language_id)? $language_id : $this->_s_language_id;
      $this->allow_sounds='';
      $this->room_selection_view=$this->_conf_all['room_selection_display_type'];
      // Insert row
      if ($this->_db_insertObj()) {
        $result=true;
        $this->id=$this->_db_lastInsertID();
        $this_id=$this->id;
        // Add new nickname
        _pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($this);
        if (!$nickname->_db_getList('id', 'nickname_plain = '.$login, 1)) {
          $nickname->addNickname($this_id, '^'.$this->_conf_all['default_nickname_color'].$login);
        }
        $this->id=$this_id;
      }
    }
    return $result;
  }


  /**
   * Delete user
   * @param   int   $user_id    User ID
   * @return  boolean   TRUE on success or FALSE on error
   */
  function deleteUser($user_id) {
    $result=false;
    if (!empty($user_id) && $this->_db_getList('id', 'id = '.$user_id, 1)) {
      // Delete user
      if ($result=$this->_db_deleteRow($user_id)) {
        // Delete all avatars owned by user
        _pcpin_loadClass('avatar'); $avatar=new PCPIN_Avatar($this);
        $avatar->deleteAvatar($user_id);
        // Delete all nicknames owned by user
        _pcpin_loadClass('nickname'); $nickname=new PCPIN_Nickname($this);
        $nickname->deleteAllNickname($user_id);
        // Delete all messages sent TO this user
        _pcpin_loadClass('message'); $message=new PCPIN_Message($this);
        if ($message->_db_getList('id', 'target_user_id = '.$user_id)) {
          $message_ids=array();
          foreach ($message->_db_list as $data) {
            $message_ids[]=$data['id'];
          }
          $message->_db_freeList();
          $message->deleteMessages($message_ids);
        }
        // Delete userdata
        _pcpin_loadClass('userdata'); $userdata=new PCPIN_UserData($this);
        $userdata->deleteUserData($user_id);
        // Update all users who ignored deleted user
        if ($res=$this->_db_query($this->_db_makeQuery(2050, $user_id))) {
          $this->_db_freeResult($res);
        }
      }
    }
    return $result;
  }


  /**
   * Activate user account
   * @param   int   $user_id    User ID
   * @return  boolean   TRUE on success or FALSE on error
   */
  function activateUser($user_id) {
    $result=false;
    if (!empty($user_id) && $this->_db_getList('id', 'id = '.$user_id, 'activated = n', 1)) {
      $result=$this->_db_updateRow($user_id, 'id', array('activated'=>'y',
                                                         'activation_code'=>'',
                                                         'joined'=>date('Y-m-d H:i:s'),
                                                         ));
    }
    return $result;
  }


  /**
   * Show date depending on user's date format and local time zone
   * @param   int   $timestamp    UNIX timestamp
   * @return  string
   */
  function makeDate($timestamp) {
    $date='';
    if (!empty($timestamp)) {
      if ($this->date_format!='') {
        $date=date($this->date_format, $timestamp);
      } else {
        $date=date($this->_conf_all['date_format'], $timestamp+$this->time_zone_offset-date('Z'));
      }
    }
    return $date;
  }


  /**
   * Check wether email address unique or not
   * @param   int     $user_id    User ID
   * @param   string  $email      Email address
   * @return  boolean   TRUE on if email address is unique or FALSE if not
   */
  function checkEmailUnique($user_id, $email='') {
    $unique=false;
    $email=trim($email);
    if ($email!='') {
      $query=$this->_db_makeQuery(1100, $user_id, $email);
      if ($result=$this->_db_query($query)) {
        if ($this->_db_fetch($result, MYSQL_NUM)) {
          $unique=false;
        } else {
          $unique=true;
        }
        $this->_db_freeResult($result);
      }
    }
    return $unique;
  }


  /**
   * Check wether username unique or not
   * @param   string  $username      Username
   * @return  boolean   TRUE on if username is unique or FALSE if not
   */
  function checkUsernameUnique($username) {
    $unique=false;
    if ($username!='' && !$this->_db_getList('id', 'login LIKE '.$username)) {
      $unique=true;
    }
    return $unique;
  }


  /**
   * Ban/unban user
   * @param   int       $user_id              User ID
   * @param   int       $action               Action: (1: ban, 0: unban)
   * @param   int       $ban_time             If user will be banned: For how many minutes (0 means PERMANENT ban)
   * @param   string    $ban_reason           If user will be banned: Ban reason (optional)
   * @param   int       $banned_by_user_id    If user will be banned: ID of user who banned him
   * @param   int       $banned_by_username   If user will be banned: Nickname of user who banned him
   * @return  boolean  TRUE on success or FALSE on error
   */
  function banUnban($user_id, $action=1, $ban_time=0, $ban_reason='', $banned_by_user_id=0, $banned_by_username='') {
    $result=false;
    _pcpin_loadClass('user'); $user=new PCPIN_User($this);
    if (!empty($user_id) && $user->_db_getList('id,banned_until,banned_permanently', 'id = '.$user_id, 1)) {
      if ($action==0) {
        // Unban user
        $result=true;
        if ($user->_db_list[0]['banned_permanently']=='y' || $user->_db_list[0]['banned_until']>'0000-00-00 00:00:00') {
          // User is banned
          $result=$user->_db_updateRow($user_id, 'id', array('banned_until'=>'',
                                                             'banned_permanently'=>'n',
                                                             'ban_reason'=>''
                                                             ));
        }
      } elseif ($action==1) {
        // Ban user
        $ban_reason=trim($ban_reason);
        $result=$user->_db_updateRow($user_id, 'id', array('banned_until'=>empty($ban_time)? '' : date('Y-m-d H:i:s', time()+$ban_time*60),
                                                           'banned_permanently'=>empty($ban_time)? 'y' : 'n',
                                                           'ban_reason'=>trim($ban_reason),
                                                           'banned_by'=>trim($banned_by_user_id),
                                                           'banned_by_username'=>trim($banned_by_username),
                                                           ));
      }
    }
    return $result;
  }


  /**
   * Global mute/unmute user
   * @param   int       $user_id              User ID
   * @param   int       $action               Action: (1: mute, 0: unmute)
   * @param   int       $mute_time            If user will be muted: For how many minutes (0 means permanent mute)
   * @param   string    $mute_reason          If user will be muted: Mute reason (optional)
   * @param   int       $muted_by_user_id     If user will be muted: ID of user who muted him
   * @param   int       $muted_by_username    If user will be muted: Nickname of user who muted him
   * @return  boolean  TRUE on success or FALSE on error
   */
  function globalMuteUnmute($user_id, $action=1, $mute_time=0, $mute_reason='', $muted_by_user_id=0, $muted_by_username='') {
    $result=false;
    _pcpin_loadClass('user'); $user=new PCPIN_User($this);
    if (!empty($user_id) && $user->_db_getList('id,global_muted_until,global_muted_permanently', 'id = '.$user_id, 1)) {
      if ($action==0) {
        // Unmute user
        $result=true;
        if ($user->_db_list[0]['global_muted_permanently']=='y' || $user->_db_list[0]['global_muted_until']>'0000-00-00 00:00:00') {
          // User is muted
          $result=$user->_db_updateRow($user_id, 'id', array('global_muted_until'=>'',
                                                             'global_muted_permanently'=>'n',
                                                             'global_muted_reason'=>''
                                                             ));
        }
      } elseif ($action==1) {
        // Mute user
        $mute_reason=trim($mute_reason);
        $result=$user->_db_updateRow($user_id, 'id', array('global_muted_until'=>empty($mute_time)? '' : date('Y-m-d H:i:s', time()+$mute_time*60),
                                                           'global_muted_permanently'=>empty($mute_time)? 'y' : 'n',
                                                           'global_muted_reason'=>trim($mute_reason),
                                                           'global_muted_by'=>trim($muted_by_user_id),
                                                           'global_muted_by_username'=>trim($muted_by_username),
                                                           ));
      }
    }
    return $result;
  }


  /**
   * Start/stop ignoring user
   * @param   int       $target_user_id   User to mute/unmute
   * @param   int       $action           1: mute or 0: unmute
   * @return  boolean   TRUE on success or FALSE on error
   */
  function muteUnmuteLocally($target_user_id, $action=-1) {
    $result=false;
    if (!empty($this->id) && !empty($target_user_id) && $this->_db_getList('id', 'id = '.$target_user_id, 1)) {
      $this->muted_users=','.trim(str_replace(',,', ',', $this->muted_users), ',').',';
      if ($action==0) {
        // Unmute
        $this->muted_users=trim(str_replace(','.$target_user_id.',', ',', $this->muted_users), ',');
        $result=$this->_db_updateObj($this->id);
      } elseif ($action==1) {
        // Mute
        if (false===strpos($this->muted_users, ','.$target_user_id.',')) {
          $this->muted_users=trim(str_replace(',,', ',', $this->muted_users.','.$target_user_id.','), ',');
          $result=$this->_db_updateObj($this->id);
        } else {
          // Already muted
          $result=true;
        }
      }
    }
    $this->muted_users=trim(str_replace(',,', ',', $this->muted_users), ',');
    return $result;
  }


  /**
   * Get chat admins
   * @return  array
   */
  function getAdmins() {
    $admins=array();
    $query=$this->_db_makeQuery(1620);
    if ($result=$this->_db_query($query)) {
      while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
        $admins[]=$data;
      }
      $this->_db_freeResult($result);
    }
    return $admins;
  }


  /**
   * Get memberlist
   * @param   boolean   $count_only           If TRUE, then users count will be returned, otherwise: an array
   * @param   int       $limitstart           Start argument for LIMIT
   * @param   int       $limitlength          Length argument for LIMIT
   * @param   int       $sort_by              Sort results by (0: do not sort,
   *                                                           1: Nickname,
   *                                                           2: Join date,
   *                                                           3: Last login date,
   *                                                           4: Online status,
   *                                                           5: Time spent online,
   * @param   int       $sort_dir             Sort direction (0: Ascending, 1: Descending)
   * @param   string    $nickname             Optional nickname to search for
   * @param   boolean   $banned_only          Optional. If TRUE, then only banned users will be returned
   * @param   boolean   $muted_only           Optional. If TRUE, then only muted users will be returned
   * @param   boolean   $moderators_only      Optional. If TRUE, then only moderators will be returned
   * @param   boolean   $admins_only          Optional. If TRUE, then only admins will be returned
   * @param   boolean   $not_activated_only   Optional. If TRUE, then only not activated user accounts will be returned.
   *                                                    If FALSE: only activated user accounts will be returned.
   *                                                    If NULL: filter will be ignored.
   * @param   string    $user_ids             Optional. User IDs, separated by comma
   * @return  mixed
   */
  function getMemberlist(
                         $count_only=false,
                         $limitstart=0,
                         $limitlength=0,
                         $sort_by=0,
                         $sort_dir=0,
                         $nickname='',
                         $banned_only=false,
                         $muted_only=false,
                         $moderators_only=false,
                         $admins_only=false,
                         $not_activated_only=false,
                         $user_ids=''
                         ) {
    $users=array();
    $query=$this->_db_makeQuery(1900, // 0
                                $count_only, // 1
                                $limitstart, // 2
                                $limitlength, // 3
                                $sort_by, // 4
                                $sort_dir, // 5
                                $nickname, // 6
                                $this->_s_user_id, // 7
                                $banned_only==true, // 8
                                $muted_only==true, // 9
                                $moderators_only==true, // 10
                                $admins_only==true, // 11
                                $not_activated_only, // 12
                                $user_ids // 13
                                );
    if ($result=$this->_db_query($query)) {
      if (true===$count_only) {
        $data=$this->_db_fetch($result);
        $users=$data['members'];
      } else {
        while ($data=$this->_db_fetch($result)) {
          $users[]=$data;
        }
      }
      $this->_db_freeResult($result);
    }
    return $users;
  }


  /**
   * Calculates total online time (in seconds), including current session
   * @param   int   $user_id    User ID
   * @return  int
   */
  function calculateOnlineTime($user_id) {
    $time=0;
    if (!empty($user_id)) {
      $query=$this->_db_makeQuery(2020, $user_id*1);
      if ($result=$this->_db_query($query)) {
        if ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
          $time=$data['time_online_total'];
        }
        $this->_db_freeResult($result);
      }
    }
    return $time;
  }

}
?>