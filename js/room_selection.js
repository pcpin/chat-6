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
 * XmlHttpRequest handler for member data queries
 * @var object
 */
var ajaxMember=new PCPIN_XmlHttpRequest();

/**
 * Updater interval in seconds
 * @var int
 */
var updaterInterval=0;

/**
 * Updater interval handle
 * @var int
 */
var updaterIntervalHandle=0;

/**
 * Room selection area default display type ("a": Tree, "s": Simplified)
 * @var string
 */
var roomSelectionDisplayType="s";

/**
 * Additional timeout handler for enterChatRoom() function
 * @var int
 */
var enterChatRoomTimeOut=0;

/**
 * Flag: if TRUE, then gender icons will be displayed in userlist
 * @var boolean
 */
var userlistGender=false;



/**
 * Initialize room list page
 * @param   boolean   updater_interval              Updater interval in seconds
 * @param   string    room_selection_display_type   Room selection area default display type ("a": Tree, "s": Simplified)
 * @param   boolean   userlist_avatar               Flag: if TRUE, then avatar thumbs will be displayed in userlist
 * @param   boolean   userlist_privileged           Flag: if TRUE, then "Admin" and "Moderator" flags will be displayed in userlist
 * @param   boolean   userlist_gender               Flag: if TRUE, then "Admin" and "Moderator" flags will be displayed in userlist
 */
function initRoomSelection(updater_interval, room_selection_display_type, userlist_avatar, userlist_privileged, userlist_gender) {
  userlistGender=userlist_gender;
  // Set "onunload" handler
  window.onunload=function() {
    // Send "Page unloaded" signal to server
    if (!SkipPageUnloadedMsg && (typeof(window.opener)=='undefined' || window.opener==null || typeof(window.opener.appName_)!='string' || window.opener.appName_!='pcpin_chat' || typeof(window.opener.initChatRoom)=='undefined')) {
      openWindow(formlink+'?inc=page_unloaded&s_id='+urlencode(s_id), '', 1, 1, false, false, false, false, false, false, false, false, false, false, 0, 0);
    }
    try {
      if (newUserRoomWindow) {
        newUserRoomWindow.close();
      }
    } catch (e) {}
  }
  window.onfocus=function() {
    try {
      if (newUserRoomWindow) {
        newUserRoomWindow.focus();
      }
    } catch (e) {}
  }
  updaterInterval=updater_interval;
  roomSelectionDisplayType=room_selection_display_type;
  userlistAvatar=userlist_avatar;
  userlistPrivileged=userlist_privileged;
  // Start updater
  roomStructureUpdaterStart(true);
}



/**
 * Start room structure updater
 * @param   boolean   now   If TRUE, then request will be sent immediately
 */
function roomStructureUpdaterStart(now) {
  clearTimeout(updaterIntervalHandle);
  // Request new room structure
  if (typeof(now)=='boolean' && true==now) {
    getRoomStructure('roomStructureUpdaterStart(), false, true');
  } else {
    // Set new interval
    updaterIntervalHandle=setTimeout('getRoomStructure("roomStructureUpdaterStart()", true)', updaterInterval*1000);
  }
}


/**
 * This function will be triggered after new invitation has been arrived
 */
function getNewInvitations() {
  sendData('_CALLBACK_getNewInvitations()', formlink, 'POST', 'ajax=get_invitations&s_id='+urlencode(s_id));
}
function _CALLBACK_getNewInvitations() {
  var invitation_msg='';
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    for (var i=0; i<actionHandler.data['invitation'].length; i++) {
      invitation_msg=getLng('user_invited_you');
      invitation_msg=invitation_msg.split('[USER]').join(coloredToPlain(actionHandler.data['invitation'][i]['author_nickname'][0], false));
      invitation_msg=invitation_msg.split('[ROOM]').join(actionHandler.data['invitation'][i]['room_name'][0]);
      confirm(invitation_msg, null, null, 'ActiveRoomId='+actionHandler.data['invitation'][i]['room_id'][0]+'; enterChatRoom()');
      return false;
    }
  }
}


/**
 * This function will be triggered after new messages arrived
 */
function getNewMessages() {
  sendData('_CALLBACK_getNewMessages()', formlink, 'POST', 'ajax=get_new_messages&s_id='+urlencode(s_id));
}
function _CALLBACK_getNewMessages() {
//debug(actionHandler.getResponseString()); return false;
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    if (actionHandler.data['abuse'].length) {
      processAbuses(actionHandler.data['abuse']);
    }
  }
}


/**
 * Enter chat room
 * @param   string    password        Optional. Room password
 * @param   int       room_id         Optional. Room ID.
 */
function enterChatRoom(password, room_id) {
  clearTimeout(enterChatRoomTimeOut);
  try {
    var stealthbox=$('stealth_mode_chkbox');
    var stealth_mode=(typeof(stealthbox)=='object' && stealthbox && true==stealthbox.checked)? 'y' : 'n';
    if (typeof(room_id)=='number' && room_id>0) {
      ActiveRoomId=room_id;
    }
    if (ActiveRoomId==0) {
      // No room selected
      alert(getLng('select_room'));
    } else {
      if (typeof(password)=='undefined' || null==password) {
        if (!isAdmin && CategoryTreeByID[ActiveCategoryId]['rooms_by_id'][ActiveRoomId] && CategoryTreeByID[ActiveCategoryId]['rooms_by_id'][ActiveRoomId]['password_protected'] && !CategoryTreeByID[ActiveCategoryId]['rooms_by_id'][ActiveRoomId]['moderated_by_me']) {
          prompt(getLng('room_password'), '', 0, 0, 'enterChatRoom(promptboxValue, '+room_id+')', true);
          return false;
        } else {
          password='';
        }
      }
      sendData('_CALLBACK_enterChatRoom()', formlink, 'POST', 'ajax=enter_chat_room&s_id='+urlencode(s_id)+'&room_id='+urlencode(ActiveRoomId)+'&stealth_mode='+urlencode(stealth_mode)+'&password='+urlencode(base64encode(password)));
    }
  } catch (e) {
    toggleProgressBar(true);
    enterChatRoomTimeOut=setTimeout('enterChatRoom('+(typeof(password)=='string'? '"'+password.split('"').join('\\"')+'"' : 'null')+', '+room_id+')', 200);
  }
}
function _CALLBACK_enterChatRoom() {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  switch (actionHandler.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case 0:
      // Room changed. Load room page.
      SkipPageUnloadedMsg=true;
      $('dummyform').s_id.value=s_id;
      $('dummyform').inc.value='chat_room';
      $('dummyform').ts.value=unixTimeStamp();
      $('dummyform').submit();
      return false;
    break;
    case 400:
      // Error: Room does not exists
      alert(actionHandler.message);
      getRoomStructure();
      clearCategoryRooms();
    break;
    default:
      // Other error
      alert(actionHandler.message);
    break;
  }
}

/**
 * Set new room structure display type
 * @param   string    room_selection_type     New room structure display type
 */
function setRoomSelectionDisplayType(room_selection_type) {
  if (typeof(room_selection_type)=='string' && room_selection_type!=roomSelectionDisplayType) {
    roomSelectionDisplayType=room_selection_type;
    sendData('roomStructureUpdaterStart(true)', formlink, 'POST', 'ajax=set_room_selection_view&s_id='+urlencode(s_id)+'&room_selection_view='+urlencode(roomSelectionDisplayType));
  }
}
