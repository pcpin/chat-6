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
 * Class PCPIN_Session
 * Manage sessions.
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Session extends PCPIN_Config {

  /**
   * Session ID
   * @var   string
   */
  var $_s_id='';

  /**
   * Client IP address
   * @var   string
   */
  var $_s_ip='';

  /**
   * Session createion time (MySQL DATETIME).
   * The time when the session has been created.
   * @var   string
   */
  var $_s_created='';

  /**
   * Time the session was last time renewed (MySQL DATETIME)
   * @var   string
   */
  var $_s_last_ping='';

  /**
   * Session language ID
   * @var   int
   */
  var $_s_language_id=0;

  /**
   * Session owner's user ID (if logged in)
   * @var   int
   */
  var $_s_user_id=0;

  /**
   * Security code
   * @var   string
   */
  var $_s_security_code='';

  /**
   * Security code image (BINARY)
   * @var   string
   */
  var $_s_security_code_img='';

  /**
   * Client's browser name
   * @var   string
   */
  var $_s_client_agent_name='';

  /**
   * Client's browser version
   * @var   string
   */
  var $_s_client_agent_version='';

  /**
   * Client's OS version
   * @var   string
   */
  var $_s_client_os='';

  /**
   * Current room ID
   * @var   int
   */
  var $_s_room_id=0;

  /**
   * Date when session owner entered a room (in MySQL DATETIME format)
   * @var   string
   */
  var $_s_room_date=0;

  /**
   * ID of last message received by session owner
   * @var   int
   */
  var $_s_last_message_id=0;

  /**
   * Time of last message sent by session owner (MySQL DATETIME)
   * @var   string
   */
  var $_s_last_sent_message_time='';

  /**
   * MD5 hash of last message sent by session owner
   * @var   string
   */
  var $_s_last_sent_message_hash='';

  /**
   * If last message sent by session owner was repeated: repeats count
   * @var   int
   */
  var $_s_last_sent_message_repeats_count=0;

  /**
   * Flag: 'y' if session owner was kicked, 'n' otherwise
   * @var   string
   */
  var $_s_kicked='';

  /**
   * Online status
   * Possible values are:
   *    1: Available
   *    2: Busy
   *    3: Away
   * @var   int
   */
  var $_s_online_status=0;

  /**
   * Online status message
   * @var   string
   */
  var $_s_online_status_message='';

  /**
   * Flag: 'y' if session owner is in "stealth" mode, 'n' otherwise
   * @var   string
   */
  var $_s_stealth_mode='';

  /**
   * Flag: 'y' if session owner has logged himself directly into Admin Backend
   * @var   string
   */
  var $_s_backend='';

  /**
   * Flag: 'y' if session owner has unloaded the chat without logging out properly
   * @var   string
   */
  var $_s_page_unloaded='';



  /**
   * Constructor. Initialize _Session class.
   * Check and update session, or create new one.
   * @param   object    &$config        Configuration and database connection handler
   * @param   string    $s_id           Session ID
   * @param   boolean   $skip_cleanup   Do not delete timed out sessions
   */
  function PCPIN_Session(&$config, $s_id='', $skip_cleanup=false) {
    // Initialize database layer and load configuration
    if (!is_object($config) || empty($config->_conf_all) || empty($config->_db_conn) || !is_resource($config->_db_conn)) {
      PCPIN_Common::dieWithError(1, '<b>Fatal error:</b> not allowed call of "'.__CLASS__.'" class');
    } else {
      $this->_db_pass_vars($config, $this, true);
    }
    if (true!==$skip_cleanup) {
      // Delete old sessions
      $this->_s_cleanUp();
    }
    // Look for session in database
    if ($s_id!='' && $this->_db_getList('_s_id =# '.$s_id, 1)) {
      if (   $this->_db_list[0]['_s_ip']==PCPIN_CLIENT_IP
          && $this->_db_list[0]['_s_client_agent_name']==PCPIN_CLIENT_AGENT_NAME
          && $this->_db_list[0]['_s_client_agent_version']==PCPIN_CLIENT_AGENT_VERSION
          && $this->_db_list[0]['_s_client_os']==PCPIN_CLIENT_OS
          && $this->_db_list[0]['_s_kicked']=='n'
          ) {
        // Session exists in database and belongs to client
        $this->_db_setObject($this->_db_list[0]);
        // Update last_ping
        $this->_s_updateSession($this->_s_id, true, true, null, date('Y-m-d H:i:s'), null, null, null, null, null, null, null, null, null, null, 'n');
      }
      $this->_db_freeList();
    }
  }

  /**
   * Pass session vars to a child object
   */
  function _s_init(&$session, &$child) {
    if (!is_object($session) || empty($session->_conf_all) || empty($session->_db_conn) || !is_resource($session->_db_conn) && isset($session->_s_id)) {
      PCPIN_Common::dieWithError(1, '<b>Fatal error:</b> invalid call of "'.__CLASS__.'" class');
    } else {
      $this->_db_pass_vars($session, $child);
    }
  }

  /**
   * Log the session in
   * @param   int       $user_id            User ID
   * @param   int       $last_message_id    ID of last message received by session owner
   * @param   int       $language_id        Optional. Selected language. If empty, then default language will be assigned.
   * @param   string    $backend_login      Optional. 'y', if user is Administrator and logged directly into Admin Backend.
   */
  function _s_logIn($user_id=0, $last_message_id=0, $language_id=0, $backend_login='n') {
    if (!empty($user_id)) {
      // Delete concurrent sessions of the same user (if any)
      if ($this->_db_getList('_s_user_id = '.$user_id)) {
        _pcpin_loadClass('session'); $session=new PCPIN_Session($this, '', true);
        $sessions=$this->_db_list;
        $this->_db_freeList();
        foreach ($sessions as $sessiondata) {
          $session->_db_setObject($sessiondata);
          $session->_s_logOut();
        }
      }
      // Create new session
      $this->_s_newSession($user_id, $last_message_id, $language_id, $backend_login);
    }
  }

  /**
   * "Kill" timed out sessions, set "Away" online status for sessions with ping older than (updater_interval+N) seconds
   */
  function _s_cleanUp() {
    // Store current state
    $this_vars=$this->_db_getFromObject();
    // Get sessions
    $sessions=array();
    $query=$this->_db_makeQuery(2100,
                                date('Y-m-d H:i:s', time()-1800),
                                date('Y-m-d H:i:s', time()-$this->_conf_all['session_timeout']),
                                date('Y-m-d H:i:s', time()-5)
                                );
    $result=$this->_db_query($query);
    while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
      $sessions[]=$data;
    }
    $this->_db_freeResult($result);
    $this->_db_freeList();
    if (!empty($sessions)) {
      _pcpin_loadClass('session'); $session=new PCPIN_Session($this, '', true);
      foreach ($sessions as $sessiondata) {
        $session->_db_setObject($sessiondata);
        $session->_s_logOut();
      }
    }
    // Delete old messages
    if (!empty($this->_conf_all['message_lifetime'])) {
      _pcpin_loadClass('message'); $message=new PCPIN_Message($this);
      $message->deleteMessages(null, time()-$this->_conf_all['message_lifetime']);
    }
    // Unmute users
    _pcpin_loadClass('user'); $user=new PCPIN_User($this);
    if ($user->_db_getList('id', 'global_muted_until > 0000-00-00 00:00:00', 'global_muted_until < '.date('Y-m-d H:i:s'))) {
      $user_ids=$user->_db_list;
      $user->_db_freeList();
      foreach ($user_ids as $data) {
        // Unmute user
        $user->globalMuteUnmute($data['id'], 0);
      }
      // Add system messages
      _pcpin_loadClass('message'); $message=new PCPIN_Message($this);
      foreach ($user_ids as $data) {
        if ($this->_db_getList('_s_room_id', '_s_user_id = '.$data['id'], 1)) {
          $message->addMessage(10111, 'n', 0, '', 0, 0, $data['id'].'/0', date('Y-m-d H:i:s'), 0, '');
        }
      }
    }
    // Clean unbanned users
    if ($result=$this->_db_query($this->_db_makeQuery(2080, date('Y-m-d H:i:s')))) {
      $this->_db_freeResult($result);
    }
    // Clean not activated email addresses
    if (!empty($this->_conf_all['activate_new_emails'])) {
      _pcpin_loadClass('user'); $user=new PCPIN_User($this);
      if ($user->_db_getList('id', 'email_new != ', 'email_new_date < '.date('Y-m-d H:i:s', time()-3600*$this->_conf_all['new_email_activation_timeout']))) {
        $user_ids=$user->_db_list;
        $user->_db_freeList();
        foreach ($user_ids as $data) {
          $user->_db_updateRow($data['id'], 'id', array('email_new'=>'', 'email_new_date'=>'', 'email_new_activation_code'=>''));
        }
      }
    }
    // Delete idle and/or not activated user accounts
    if (!PCPIN_SLAVE_MODE && $this->_conf_all['activate_new_accounts']==1 || !empty($this->_conf_all['account_pruning'])) {
      _pcpin_loadClass('user'); $user=new PCPIN_User($this);
      $query=$this->_db_makeQuery(2060,
                                  $this->_conf_all['activate_new_accounts']==1? date('Y-m-d H:i:s', time()-3600*$this->_conf_all['new_account_activation_timeout']) : '',
                                  !empty($this->_conf_all['account_pruning'])? date('Y-m-d H:i:s', time()-$this->_conf_all['account_pruning']*86400) : ''
                                  );
      $user_ids=array();
      if ($result=$this->_db_query($query)) {
        while ($data=$this->_db_fetch($result, MYSQL_ASSOC)) {
          $user_ids[]=$data['id'];
        }
        $this->_db_freeResult($result);
      }
      if (!empty($user_ids)) {
        foreach ($user_ids as $id) {
          $user->deleteUser($id);
        }
      }
    }
    // Delete empty and timed out user rooms
    _pcpin_loadClass('room'); $room=new PCPIN_Room($this);
    if ($room->_db_getList('id', 'type = u', 'users_count <= 0', 'last_ping < '.date('Y-m-d H:i:s', time()-$this->_conf_all['empty_userroom_lifetime']))) {
      $rooms=$room->_db_list;
      $room->_db_freeList();
      foreach ($rooms as $data) {
        $room->deleteRoom($data['id']);
      }
      // Add system message
      _pcpin_loadClass('message'); $message=new PCPIN_Message($this);
      $message->addMessage(1100, 'n', 0, '', 0, 0, '-', date('Y-m-d H:i:s'), 0, '');
    }
    // Delete old logs
    if (!empty($this->_conf_all['logging_period'])) {
      _pcpin_loadClass('message_log'); $message_log=new PCPIN_Message_Log($this);
      $message_log->cleanUp();
    }
    // Restore current state
    $this->_db_setObject($this_vars);
  }

  /**
   * Log the session out and deactivate it
   * @param   boolean   $skip_msg   If TRUE, then system messages 105 and 115 will be NOT inserted
   */
  function _s_logOut($skip_msg=false) {
    if ($this->_s_id!='') {
      if (!empty($this->_s_user_id)) {
        _pcpin_loadClass('message'); $msg=new PCPIN_Message($this);
        if (!empty($this->_s_room_id)) {
          // Session owner was in a room
         _pcpin_loadClass('room'); $room=new PCPIN_Room($this);
           $room->putUser($this->_s_user_id, 0, $skip_msg);
        }
        // Delete invitations
       _pcpin_loadClass('invitation'); $invitation=new PCPIN_Invitation($this);
         $invitation->deleteUserInvitations($this->_s_user_id);
        // Delete temporary data
        _pcpin_loadClass('tmpdata'); $tmpdata=new PCPIN_TmpData($this);
        $tmpdata->deleteUserRecords($this->_s_user_id);
        // Update session owner stats
        _pcpin_loadClass('user'); $user=new PCPIN_User($this);
        if ($user->_db_loadObj($this->_s_user_id)) {
          if ($user->is_guest=='y') {
            // User was a guest. Delete record.
            $user->deleteUser($this->_s_user_id);
          } else {
            // Update registered user stats
            $user->time_online=$user->calculateOnlineTime($user->id);
            $user->last_message_id=($user->last_message_id<$this->_s_last_message_id)? $this->_s_last_message_id : $user->last_message_id;
            $user->_db_updateObj($user->id);
          }
        }
        if (true!==$skip_msg) {
          $msg->addMessage(105, 'n', 0, '', 0, 0, $this->_s_user_id);
        }
      }
      // Delete session from database
      $this->_db_deleteRow($this->_s_id, '_s_id');
    }
  }

  /**
   * Create new session
   * @param   int       $user_id            Optional ID of session owner user
   * @param   int       $last_message_id    ID of last message received by session owner
   * @param   int       $language_id        Optional. Selected language. If empty, then default language will be used.
   * @param   string    $backend_login      Optional. 'y', if user is Administrator and logged directly into Admin Backend.
   */
  function _s_newSession($user_id=0, $last_message_id=0, $language_id=0, $backend_login='n') {
    $ok=false;
    if ($backend_login!=='y' && $backend_login!=='n') {
      $backend_login='n';
    }
    $max_attempts=100;
    do {
      // Generate new session ID
      $this->_s_id=PCPIN_Common::randomString(PCPIN_SID_LENGTH, 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789');
      // Check new session ID
      if (!$this->_db_getList('_s_id', '_s_id = '.$this->_s_id, 1)) {
        // New session ID is unique
        // Check language
        _pcpin_loadClass('language'); $language=new PCPIN_Language($this);
        if (empty($this->_conf_all['allow_language_selection']) || 0==$language_id=$language->checkLanguage($language_id)) {
          $language_id=$this->_conf_all['default_language'];
        }
        // Set all object properties up
        $this->_s_ip=PCPIN_CLIENT_IP;
        $this->_s_client_agent_name=PCPIN_CLIENT_AGENT_NAME;
        $this->_s_client_agent_version=PCPIN_CLIENT_AGENT_VERSION;
        $this->_s_client_os=PCPIN_CLIENT_OS;
        $this->_s_created=date('Y-m-d H:i:s');
        $this->_s_last_ping=date('Y-m-d H:i:s');
        $this->_s_language_id=$language_id;
        $this->_s_user_id=$user_id;
        $this->_s_security_code=md5(PCPIN_Common::randomString(mt_rand(100, 255)));
        $this->_s_security_code_img='';
        $this->_s_room_id=0;
        $this->_s_room_date='';
        $this->_s_last_message_id=$last_message_id;
        $this->_s_last_sent_message_time='0000-00-00 00:00:00';
        $this->_s_last_sent_message_hash='';
        $this->_s_last_sent_message_repeats_count=0;
        $this->_s_online_status=1;
        $this->_s_online_status_message='';
        $this->_s_kicked='n';
        $this->_s_stealth_mode='n';
        $this->_s_backend=$backend_login;
        $this->_s_page_unloaded='n';
        // Save session into database
        $ok=$this->_db_insertObj();
      }
      $max_attempts--;
    } while($ok!==true && $max_attempts>0);
    $this->_db_freeList();
    if (!$ok) {
      PCPIN_Common::dieWithError(-1, '<b>Fatal error</b>: Failed to create new session');
    }
  }


  /**
   * Update session data in object and/or database
   * @param   string    $s_id                     Session ID
   * @param   boolean   $obj                      If TRUE, then object properties will be updated
   * @param   boolean   $db                       If TRUE, then database table will be updated
   * @param   int       $language_id              Language ID. NULL: do not change.
   * @param   string    $last_ping                Last ping time. NULL: do not change.
   * @param   int       $room_id                  Room ID. NULL: do not change.
   * @param   string    $security_code            Security code. NULL: do not change.
   * @param   string    $security_code_img        Security code image. NULL: do not change.
   * @param   int       $user_id                  User ID. NULL: do not change.
   * @param   int       $last_message_id          ID of last message received by session owner. NULL: do not change.
   * @param   string    $room_date                Date when session owner entered a room (in MySQL DATETIME format). NULL: do not change.
   * @param   string    $kicked                   "Kicked" flag. NULL: do not change.
   * @param   string    $online_status            Online status. NULL: do not change.
   * @param   string    $online_status_message    Online status message. NULL: do not change.
   * @param   string    $stealth_mode             "Stealth" flag. NULL: do not change.
   * @param   string    $page_unloaded            "Page unloaded" flag. NULL: do not change.
   * @param   string    $last_sent_message_time   Time of the last message sent by session owner in MySQL DATETIME format. NULL: do not change.
   * @param   string    $last_sent_message_hash   MD5 hash of the last message sent by session owner. NULL: do not change.
   * @return  boolean TRUE on success or FALSE on error
   */
  function _s_updateSession($s_id, $obj=false, $db=false,
                            $language_id=null,
                            $last_ping=null,
                            $room_id=null,
                            $security_code=null,
                            $security_code_img=null,
                            $user_id=null,
                            $last_message_id=null,
                            $room_date=null,
                            $kicked=null,
                            $online_status=null,
                            $online_status_message=null,
                            $stealth_mode=null,
                            $page_unloaded=null,
                            $last_sent_message_time=null,
                            $last_sent_message_hash=null,
                            $last_sent_message_repeats_count=null
                            ) {
    $result=false;
    if ($s_id!='') {
      if (true===$obj && $s_id==$this->_s_id) {
        $result=true;
        if (!is_null($language_id)) $this->_s_language_id=$language_id;
        if (!is_null($last_ping)) $this->_s_last_ping=$last_ping;
        if (!is_null($room_id)) $this->_s_room_id=$room_id;
        if (!is_null($security_code)) $this->_s_security_code=$security_code;
        if (!is_null($security_code_img)) $this->_s_security_code_img=$security_code_img;
        if (!is_null($user_id)) $this->_s_user_id=$user_id;
        if (!is_null($last_message_id)) $this->_s_last_message_id=$last_message_id;
        if (!is_null($room_date)) $this->_s_room_date=$room_date;
        if (!is_null($kicked)) $this->_s_kicked=$kicked;
        if (!is_null($online_status)) $this->_s_online_status=$online_status;
        if (!is_null($online_status_message)) $this->_s_online_status_message=$online_status_message;
        if (!is_null($stealth_mode)) $this->_s_stealth_mode=$stealth_mode;
        if (!is_null($page_unloaded)) $this->_s_page_unloaded=$page_unloaded;
        if (!is_null($last_sent_message_time)) $this->_s_last_sent_message_time=$last_sent_message_time;
        if (!is_null($last_sent_message_hash)) $this->_s_last_sent_message_hash=$last_sent_message_hash;
        if (!is_null($last_sent_message_repeats_count)) $this->_s_last_sent_message_repeats_count=$last_sent_message_repeats_count;
      }
      if (true===$db) {
        $param=array();
        if (!is_null($language_id)) $param['_s_language_id']=$language_id;
        if (!is_null($last_ping)) $param['_s_last_ping']=$last_ping;
        if (!is_null($room_id)) $param['_s_room_id']=$room_id;
        if (!is_null($security_code)) $param['_s_security_code']=$security_code;
        if (!is_null($security_code_img)) $param['_s_security_code_img']=$security_code_img;
        if (!is_null($user_id)) $param['_s_user_id']=$user_id;
        if (!is_null($last_message_id)) $param['_s_last_message_id']=$last_message_id;
        if (!is_null($room_date)) $param['_s_room_date']=$room_date;
        if (!is_null($kicked)) $param['_s_kicked']=$kicked;
        if (!is_null($online_status)) $param['_s_online_status']=$online_status;
        if (!is_null($online_status_message)) $param['_s_online_status_message']=$online_status_message;
        if (!is_null($stealth_mode)) $param['_s_stealth_mode']=$stealth_mode;
        if (!is_null($page_unloaded)) $param['_s_page_unloaded']=$page_unloaded;
        if (!is_null($last_sent_message_time)) $param['_s_last_sent_message_time']=$last_sent_message_time;
        if (!is_null($last_sent_message_hash)) $param['_s_last_sent_message_hash']=$last_sent_message_hash;
        if (!is_null($last_sent_message_repeats_count)) $param['_s_last_sent_message_repeats_count']=$last_sent_message_repeats_count;
        $result=$this->_db_updateRow($s_id, '_s_id', $param);
      }
    }
    return $result;
  }


  /**
   * Set new online status
   * @param   int     $status       New online status
   * @param   string  $status_msg   New online status message
   * @return  boolean TRUE on success or FALSE on error
   */
  function _s_setOnlineStatus($status=0, $status_msg='') {
    if (!empty($this->_s_id) && !empty($status) && is_scalar($status)) {
      $status_msg=trim($status_msg);
      // Update session
      $this->_s_updateSession($this->_s_id, true, true,
                              null,
                              null,
                              null,
                              null,
                              null,
                              null,
                              null,
                              null,
                              null,
                              $status,
                              $status_msg);
      // Insert new system message (only if user in a room)
      if (!empty($this->_s_room_id)) {
        _pcpin_loadClass('message'); $message=new PCPIN_Message($this);
        $message->addMessage(102, 'n', 0, '', $this->_s_room_id, 0, $this->_s_user_id.'/'.$status.'/'.$status_msg);
      }
    }
  }

}
?>