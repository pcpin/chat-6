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
 * This file contains user records and functions
 */

/**
 * Initialize userlist object
 */
var UserList=new UserList();
UserList.initialize();


/**
 * Chat user object
 * @param   int       id                        User ID
 * @param   string    nickname                  Current nickname
 * @param   int       online_status             Online status
 * @param   int       online_status_message     Online status message
 * @param   boolean   muted_locally             TRUE, if user is muted locally
 * @param   boolean   global_muted              TRUE, if user is global muted
 * @param   int       global_muted_until        If user is global muted - mute expiration date (UNIX timestamp). Empty value means PERMANENT mute.
 * @param   string    ip_address                IP address
 * @param   string    gender                    Gender
 * @param   int       avatar_bid                Binaryfile ID of the first user's avatar
 * @param   boolean   is_admin                  TRUE, if user is chat Admin
 * @param   boolean   is_moderator              TRUE, if user is moderator of current room
 * @param   int       joined                    Join date (UNIX timestamp)
 * @param   int       last_visit                Last visit date (UNIX timestamp)
 * @param   int       time_online               Time spent online (seconds)
 * @param   boolean   banned                    TRUE, if user is banned
 * @param   int       banned_until              If user is global banned - mute expiration date (UNIX timestamp). Empty value means PERMANENT ban.
 * @param   string    ban_reason                If user banned: reason
 * @param   int       banned_by                 If user is banned - Who banned him? (User ID)
 * @param   int       banned_by_username        If user is banned - Who banned him? (Username)
 * @param   int       global_muted_by           If user is global muted - Who muted him? (User ID)
 * @param   int       global_muted_by_username  If user is global muted - Who muted him? (Username)
 * @param   string    global_muted_reason       If user global muted: reason
 * @param   boolean   is_guest                  TRUE, if user is guest
 */
function User(id, nickname, online_status, online_status_message, muted_locally, global_muted, global_muted_until, ip_address, gender, avatar_bid, is_admin, is_moderator, joined, last_visit, time_online, banned, banned_until, ban_reason, banned_by, banned_by_username, global_muted_by, global_muted_by_username, global_muted_reason, is_guest) {

  /**
   * User ID
   * @var   int
   */
  this.ID=stringToNumber(id);

  /**
   * Current nickname
   * @var   string
   */
  this.Nickname=nickname;

  /**
   * Current online status
   * @var   int
   */
  this.OnlineStatus=stringToNumber(online_status);

  /**
   * Current online status message
   * @var   int
   */
  this.OnlineStatusMessage=online_status_message;

  /**
   * Current "muted locally" status
   * @var   boolean
   */
  this.MutedLocally=muted_locally==true || muted_locally==1 || muted_locally=='1';

  /**
   * Current "global muted" status
   * @var   boolean
   */
  this.GlobalMuted=global_muted==true || global_muted==1 || global_muted=='1';

  /**
   * Global mute expiration date (UNIX timestamp)
   * @var   int
   */
  this.GlobalMutedUntil=stringToNumber(global_muted_until);

  /**
   * Global mute reason
   * @var   string
   */
  this.GlobalMutedReason=global_muted_reason;

  /**
   * IP address
   * @var   string
   */
  this.IP=ip_address;

  /**
   * Gender
   * @var   string
   */
  this.Gender=gender;

  /**
   * Binaryfile ID of the first user's avatar
   * @var   int
   */
  this.AvatarBID=avatar_bid;

  /**
   * Flag: TRUE if user is chat Admin
   * @var   boolean
   */
  this.IsAdmin=is_admin;

  /**
   * Flag: TRUE if user is moderator of current room
   * @var   boolean
   */
  this.IsModerator=is_moderator;

  /**
   * Join date (UNIX timestamp)
   * @var   int
   */
  this.Joined=joined;

  /**
   * Last visit date (UNIX timestamp)
   * @var   int
   */
  this.LastVisit=last_visit;

  /**
   * Time spent online in seconds
   * @var   int
   */
  this.TimeOnline=time_online;

  /**
   * Current "banned" status
   * @var   boolean
   */
  this.Banned=banned==true || banned==1 || banned=='1';

  /**
   * Ban expiration date (UNIX timestamp)
   * @var   int
   */
  this.BannedUntil=stringToNumber(banned_until);

  /**
   * If user banned: ban reason
   * @var   string
   */
  this.BanReason=ban_reason;

  /**
   * If user banned: who banned him (user ID)
   * @var   int
   */
  this.BannedBy=banned_by;

  /**
   * If user banned: who banned him (username)
   * @var   string
   */
  this.BannedByUsername=banned_by_username;

  /**
   * If user global muted: who muted him (user ID)
   * @var   int
   */
  this.GlobalMutedBy=global_muted_by;

  /**
   * If user global muted: who muted him (username)
   * @var   string
   */
  this.GlobalMutedByUsername=global_muted_by_username;

  /**
   * TRUE, if user is guest
   * @var   string
   */
  this.IsGuest=is_guest;



  /**
   * Get nickname (with color codes)
   * @return  string
   */
  this.getNickname=function() {
    return this.Nickname;
  }

  /**
   * Get online status code
   * @return  int
   */
  this.getOnlineStatus=function() {
    return this.OnlineStatus;
  }

  /**
   * Get online status message
   * @return  string
   */
  this.getOnlineStatusMessage=function() {
    return this.OnlineStatusMessage;
  }

  /**
   * Get local mute status
   * @return  boolean
   */
  this.getMutedLocally=function() {
    return this.MutedLocally;
  }

  /**
   * Get global mute status
   * @return  boolean
   */
  this.getGlobalMuted=function() {
    return this.GlobalMuted;
  }

  /**
   * Get global mute expiration time
   * @return  int
   */
  this.getGlobalMutedUntil=function() {
    return this.GlobalMutedUntil;
  }

  /**
   * Get IP address
   * @return  string
   */
  this.getIP=function() {
    return this.IP;
  }

  /**
   * Get gender
   * @return  string
   */
  this.getGender=function() {
    return this.Gender;
  }

  /**
   * Get binaryfile ID of the first user's avatar
   * @return  int
   */
  this.getAvatarBID=function() {
    return this.AvatarBID;
  }

  /**
   * Get admin flag
   * @return  boolean
   */
  this.getIsAdmin=function() {
    return this.IsAdmin;
  }

  /**
   * Get moderator flag
   * @return  boolean
   */
  this.getIsModerator=function() {
    return this.IsModerator;
  }

  /**
   * Get ban status
   * @return  boolean
   */
  this.getBanned=function() {
    return this.Banned;
  }




  /**
   * Set new online status code
   * @param   int   online_status   New online status code
   */
  this.setOnlineStatus=function(online_status) {
    this.OnlineStatus=online_status;
  }

  /**
   * Set new online status message
   * @param   string   online_status_message   New online status code
   */
  this.setOnlineStatusMessage=function(online_status_message) {
    this.OnlineStatusMessage=online_status_message;
  }

  /**
   * Set new local muted status
   * @param   int   muted   "1" if user muted, "0" otherwise
   */
  this.setMutedLocally=function(muted) {
    this.MutedLocally=muted==1 || muted=='1' || muted==true;
  }

  /**
   * Set new global muted status
   * @param   int     muted               "1" if user muted, "0" otherwise
   * @param   int     muted_until         Mute expiration time (UNIX timestamp)
   */
  this.setGlobalMuted=function(muted, muted_until) {
    if (typeof(muted_until)=='undefined') {
      muted_until=0;
    } else if (typeof(muted_until)!='number') {
      muted_until=stringToNumber(muted_until);
    }
    this.GlobalMuted=muted==1 || muted=='1' || muted==true;
    if (true==this.GlobalMuted) {
      if (typeof(muted_until)=='undefined') {
        muted_until=0;
      }
      this.GlobalMutedUntil=muted_until;
    } else {
      this.GlobalMutedUntil=0;
    }
  }


}


/**
 * Userlist object
 */
function UserList() {

  /**
   * User records
   * @var object
   */
  this.records=new Array();

  /**
   * User records archive
   * @var object
   */
  this.records_archive=new Array();

  /**
   * User records counter
   * @var int
   */
  this.recordsCount=0;


  /**
   * Reinitialize userlist object
   */
  this.initialize=function() {
    for (var i in this.records) {
      this.records_archive[this.records[i].ID]=this.records[i];
    }
    this.records=new Array();
    this.recordsCount=0;
    this.addRecord(0, 'SYSTEM', 1, '', false, false, 0, ''); // "SYSTEM" account
  }


  /**
   * Add new user record to the list
   * @param   int       id                        User ID
   * @param   string    nickname                  Current nickname
   * @param   int       online_status             Online status
   * @param   int       online_status_message     Online status message
   * @param   boolean   muted_locally             TRUE, if user is muted locally
   * @param   boolean   global_muted              TRUE, if user is global muted
   * @param   int       global_muted_until        If user is global muted - mute expiration date (UNIX timestamp). Empty value means PERMANENT mute.
   * @param   string    ip_address                IP address
   * @param   string    gender                    Gender
   * @param   int       avatar_bid                Binaryfile ID of the first user's avatar
   * @param   boolean   is_admin                  TRUE, if user is chat Admin
   * @param   boolean   is_moderator              TRUE, if user is moderator of current room
   * @param   int       joined                    Join date (UNIX timestamp)
   * @param   int       last_visit                Last visit date (UNIX timestamp)
   * @param   int       time_online               Time spent online (seconds)
   * @param   boolean   banned                    TRUE, if user is banned
   * @param   int       banned_until              If user is global banned - mute expiration date (UNIX timestamp). Empty value means PERMANENT ban.
   * @param   string    ban_reason                If user banned: reason
   * @param   int       banned_by                 If user is banned - Who banned him? (User ID)
   * @param   int       banned_by_username        If user is banned - Who banned him? (Username)
   * @param   int       global_muted_by           If user is global muted - Who muted him? (User ID)
   * @param   int       global_muted_by_username  If user is global muted - Who muted him? (Username)
   * @param   string    global_muted_reason       If user global muted: reason
   * @param   boolean   is_guest                  TRUE, if user is guest
   */
  this.addRecord=function(id, nickname, online_status, online_status_message, muted_locally, global_muted, global_muted_until, ip_address, gender, avatar_bid, is_admin, is_moderator, joined, last_visit, time_online, banned, banned_until, ban_reason, banned_by, banned_by_username, global_muted_by, global_muted_by_username, global_muted_reason, is_guest) {
    if (typeof(id)=='string') {
      id=stringToNumber(id);
    }
    if (typeof(online_status)=='string') {
      online_status=stringToNumber(online_status);
    }
    if (typeof(muted)!='boolean') {
      muted=false;
    }
    if (typeof(global_muted)!='boolean' || false==global_muted) {
      global_muted=false;
      global_muted_until=0;
    } else {
      if (typeof(global_muted_until)=='undefined') {
        global_muted_until=0;
      } else if (typeof(global_muted_until)!='number') {
        global_muted_until=stringToNumber(global_muted_until);
      }
    }
    if (typeof(ip_address)!='string') {
      ip_address='';
    }
    if (typeof(gender)!='string' || gender=='') {
      gender='-';
    }
    if (typeof(avatar_bid)!='number') {
      if (typeof(avatar_bid)=='string') {
        avatar_bid=stringToNumber(avatar_bid);
      } else {
        avatar_bid=0;
      }
    }
    if (typeof(is_admin)!='boolean') {
      is_admin=false;
    }
    if (typeof(is_moderator)!='boolean') {
      is_moderator=false;
    }
    if (typeof(joined)=='undefined') {
      joined=0;
    } else if (typeof(joined)!='number') {
      joined=stringToNumber(joined);
    }
    if (typeof(last_visit)=='undefined') {
      last_visit=0;
    } else if (typeof(last_visit)!='number') {
      last_visit=stringToNumber(last_visit);
    }
    if (typeof(time_online)=='undefined') {
      time_online=0;
    } else if (typeof(time_online)!='number') {
      time_online=stringToNumber(time_online);
    }
    if (typeof(banned) !='boolean') {
      banned=false;
    }
    if (typeof(banned_until)=='undefined') {
      banned_until=0;
    } else if (typeof(banned_until)!='number') {
      banned_until=stringToNumber(banned_until);
    }
    if (typeof(banned_by)=='undefined') {
      banned_by=0;
    } else if (typeof(banned_by)!='number') {
      banned_by=stringToNumber(banned_by);
    }
    if (typeof(banned_by_username)!='string') {
      banned_by_username='';
    }
    if (typeof(global_muted_by)=='undefined') {
      global_muted_by=0;
    } else if (typeof(global_muted_by)!='number') {
      global_muted_by=stringToNumber(global_muted_by);
    }
    if (typeof(global_muted_by_username)!='string') {
      global_muted_by_username='';
    }
    if (typeof(global_muted_reason)!='string') {
      global_muted_reason='';
    }
    if (typeof(is_guest)!='boolean') {
      is_guest=false;
    }
    if (typeof(this.records[id])!='object' || this.records[id]==null) {
      this.records[id]=new User(id, nickname, online_status, online_status_message, muted_locally, global_muted, global_muted_until, ip_address, gender, avatar_bid, is_admin, is_moderator, joined, last_visit, time_online, banned, banned_until, ban_reason, banned_by, banned_by_username, global_muted_by, global_muted_by_username, global_muted_reason, is_guest);
      this.recordsCount++;
    }
  }


  /**
   * Get user record referenced by ID
   * @param   int       id        User ID
   * @return  object
   */
  this.getRecord=function(id) {
    if (typeof(id)=='string') {
      id=stringToNumber(id);
    }
    var rec=null;
    if (typeof(id)=='number' && id>0) {
      if (this.records[id]) {
        // Return current record
        rec=this.records[id];
      } else if (this.records_archive[id]) {
        // Return archived record
        rec=this.records_archive[id];
      }
    }
    return rec;
  }


  /**
   * Return all records from this.records list
   * @return  array
   */
  this.getAllRecords=function() {
    var recs=new Array();
    for (var i in this.records) {
      if (i!=0) {
        recs[i]=this.records[i];
      }
    }
    return recs;
  }


  /**
   * Find exactly one user record by plain nickname
   * @param   string      nickname_plain    Plain nickname or part of it.
   * @param   boolean     match_case        DEPRECATED AND IGNORED, always FALSE.
   * @param   boolean     strict            If TRUE: nickname (plain) must be exactly equal to supplied
   *                                        nickname_plain argument. Default: FALSE.
   * @return  mixed   (object) user record if *exactly* one user found
   *                  (boolean) FALSE if more than one user found
   *                  (NULL) NULL if no users found
   */
  this.findRecordByNickname=function(nickname_plain, match_case, strict) {
    var result=null;
    var plain='';
    if (typeof(strict)!='boolean') {
      strict=false;
    }
    if (typeof(nickname_plain)=='string' && nickname_plain!='') {
      nickname_plain=nickname_plain.toLowerCase();
      for (var id in this.records) {
        plain=coloredToPlain(this.records[id].Nickname).toLowerCase();
        if (true==strict) {
          // Strict mode
          if (plain==nickname_plain) {
            // Record found
            result=this.records[id];
            break;
          }
        } else {
          if (plain.indexOf(nickname_plain)>=0) {
            // Record found
            if (result==null) {
              result=this.records[id];
            } else {
              result=false;
              break;
            }
          }
        }
      }
    }
    return result;
  }


  /**
   * Find exactly one user record by enclosed into doublequotes " plain nickname contained in a specified string
   * @param   string      search_str        String that contain a nickname
   * @param   boolean     match_case        DEPRECATED AND IGNORED, always FALSE.
   * @param   boolean     strict            If TRUE: nickname (plain) must be exactly equal to supplied
   *                                        nickname_plain argument. Default: FALSE.
   * @return  mixed   (object) user record if *exactly* one user found
   *                  (boolean) FALSE if more than one user found
   *                  (NULL) NULL if no users found
   */
  this.findRecordByNicknameInString=function(search_str, match_case, strict) {
    var result=null;
    if (typeof(strict)!='boolean') {
      strict=null;
    }
    if (typeof(search_str)=='string' && search_str!='') {
      var parts=search_str.split('"');
      if (parts.length>=3) {
        parts.pop();
        parts.shift();
        do {
          if (null==(result=this.findRecordByNickname(parts.join('"'), true, strict))) {
            parts.pop();
          }
        } while (result==null && parts.length>0);
      }
    }
    return result;
  }


}