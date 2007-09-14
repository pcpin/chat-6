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
 * XmlHttpRequest handler for member data requests
 * @var object
 */
var ajaxMember=new PCPIN_XmlHttpRequest();

/**
 * ID of profile user
 * @var int
 */
var profileUserId=currentUserId;

/**
 * Login of profile user
 * @var string
 */
var profileUserLogin='';

/**
 * Opened child window handlers
 * @var object
 */
var openedWindows=new Array();

/**
 * Minimum allowed nickname length, chars
 * @var int
 */
var nickname_length_min=0;

/**
 * Maximum allowed nickname length, chars
 * @var int
 */
var nickname_length_max=0;

/**
 * Last entered nickname (if not saved)
 * @var string
 */
var last_nickname='';

/**
 * ID of currently active nickname
 * @var int
 */
var CurrentNicknameID=0;

/**
 * Last entered email address (if not saved)
 * @var string
 */
var last_email='';

/**
 * Default nickname color
 * @var string
 */
var defaultNicknameColor='';

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
 * Current profile homepage
 * @var string
 */
var currentProfileHomepage='';

/**
 * Current profile gender
 * @var string
 */
var currentProfileGender='-';

/**
 * Received abuses
 * @var object
 */
var receivedAbuses=new Array();

/**
 * Flag: TRUE if email address is hidden
 * @var boolean
 */
var hideEmail=false;

/**
 * How many avatars are allowed?
 * @var int
 */
var avatarsMaxCount=0;

/**
 * How many nicknames are allowed?
 * @var int
 */
var nicknamesMaxCount=0;

/**
 * Room selection area default display type (0: Tree, 1: Simplified)
 * @var int
 */
var roomSelectionDisplayType=0;

/**
 * Flag: if TRUE, then avatar gallery is allowed
 * @var boolean
 */
var avatarGalleryAllowed=false;



/**
 * Initialize profile data
 * @param   int       nickname_length_min_          Minimum allowed nickname length, chars
 * @param   int       nickname_length_max_          Maximum allowed nickname length, chars
 * @param   string    homepage                      Homepage
 * @param   string    gender                        Gender: 'm' (male), 'f' (female) or '-' (not specified)
 * @param   boolean   updater_interval              Updater interval in seconds
 * @param   string    default_nickname_color        Default nickname color
 * @param   boolean   hide_email                    Hide email address?
 * @param   int       avatars_max_count             How many avatars are allowed?
 * @param   int       nicknames_max_count           How many nicknames are allowed?
 * @param   int       room_selection_display_type   Room selection area default display type (0: Tree, 1: Simplified)
 * @param   boolean   userlist_gender               Flag: if TRUE, then gender icons will be displayed in userlist
 * @param   boolean   userlist_avatar               Flag: if TRUE, then avatar thumbs will be displayed in userlist
 * @param   boolean   userlist_privileged           Flag: if TRUE, then "Admin" and "Moderator" flags will be displayed in userlist
 * @param   boolean   edit_by_admin                 Optional. Flag: if TRUE, no room structure will be loaded and user data will be displayed
 * @param   boolean   profile_user_id               User ID
 * @param   boolean   skip_rooms                    Optional. Flag: if TRUE, then no room structure will be loaded
 * @param   boolean   avatar_gallery_allowed        Optional. Flag: if TRUE, then avatar gallery is allowed
 */
function initProfile(nickname_length_min_, nickname_length_max_, homepage, gender, updater_interval, default_nickname_color, hide_email, avatars_max_count, nicknames_max_count, room_selection_display_type, userlist_gender, userlist_avatar, userlist_privileged, edit_by_admin, profile_user_id, skip_rooms, avatar_gallery_allowed) {
  profileUserId=profile_user_id;
  if (isAdmin && edit_by_admin) {
    // Get member data
    getMemberData();
  }
  $$('body')[0].onunload=function() {
    try {
      if (uploadWindow) {
        uploadWindow.close();
      }
    } catch (e) {}
    try {
      if (newUserRoomWindow) {
        newUserRoomWindow.close();
      }
    } catch (e) {}
  }
  window.onfocus=function() {
    try {
      if (uploadWindow) {
        uploadWindow.focus();
      }
    } catch (e) {}
    try {
      if (newUserRoomWindow) {
        newUserRoomWindow.focus();
      }
    } catch (e) {}
  }
  nickname_length_min=nickname_length_min_;
  nickname_length_max=nickname_length_max_;
  defaultNicknameColor=default_nickname_color;
  hideEmail=hide_email;
  updaterInterval=updater_interval;
  currentProfileHomepage=homepage;
  currentProfileGender=gender;
  avatarsMaxCount=avatars_max_count;
  nicknamesMaxCount=nicknames_max_count;
  roomSelectionDisplayType=room_selection_display_type;
  userlistGender=userlist_gender;
  userlistAvatar=userlist_avatar;
  userlistPrivileged=userlist_privileged;
  profileUserLogin=$('profile_username_hidden').value;
  avatarGalleryAllowed=avatar_gallery_allowed;
  document.onkeyup=function(e) {
    switch (getKC(e)) {
      case 27:
        flushDisplay();
        break;
    }
  };
  if (avatarsMaxCount==0) {
    avatars_tbl.style.display='none';
  }
  // Define callback function for user options context menu
  CallBackContextMenuFunc='getRoomStructure()';
  // Load nicknames from server
  getNickNames();
  // Get avatars
  getAvatars();
  // Display gender image
  showGenderImage();
  if ((typeof(edit_by_admin)!='boolean' || edit_by_admin==false) && (typeof(skip_rooms)!='boolean' || skip_rooms==false)) {
    $('profile_header_tbl').style.display='';
    $('room_selection_tbl').style.display='';
    $('other_profile_header_tbl').style.display='none';
    $('user_profile_data_header').innerHTML=htmlspecialchars(getLng('your_profile'));
    $('user_nicknames_data_header').innerHTML=htmlspecialchars(getLng('your_nicknames'));
    $('user_avatars_data_header').innerHTML=htmlspecialchars(getLng('your_avatars'));
    // Get and display room tree
    getRoomStructure();
    // Start updaters
    profile_start_update();
  } else {
    $('profile_header_tbl').style.display='none';
    $('room_selection_tbl').style.display='none';
    if (typeof(edit_by_admin)=='boolean' && edit_by_admin==true) {
      $('other_profile_header_tbl').style.display='';
      $('other_profile_header_tbl_title').innerHTML=htmlspecialchars(getLng('users_profile').split('[USER]').join(profileUserLogin));
      $('user_profile_data_header').innerHTML=htmlspecialchars(getLng('profile'));
      $('user_nicknames_data_header').innerHTML=htmlspecialchars(getLng('nicknames'));
      $('user_avatars_data_header').innerHTML=htmlspecialchars(getLng('avatars'));
    } else {
      $('user_profile_data_header').innerHTML=htmlspecialchars(getLng('your_profile'));
      $('user_nicknames_data_header').innerHTML=htmlspecialchars(getLng('your_nicknames'));
      $('user_avatars_data_header').innerHTML=htmlspecialchars(getLng('your_avatars'));
    }
  }
  $('profile_data_table').style.display=SlaveMode? 'none' : '';
  $('other_profile_header_tbl').style.display=SlaveMode? 'none' : $('other_profile_header_tbl').style.display;
  // Get focus
  window.focus();
}


/**
 * Get avatars
 */
function getAvatars() {
  if (avatarsMaxCount>0) {
    sendData('_CALLBACK_getAvatars()', formlink, 'POST', 'ajax='+urlencode('get_avatars')+'&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId));
  }
}
function _CALLBACK_getAvatars() {
//debug(actionHandler.getResponseString()); return false;
  var avatars_tbl=$('avatars_tbl');
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  var avatar=null;
  var avatar_nr=0;
  var avatar_id=0;
  var avatar_binaryfile_id=0;
  var avatars_count=0;

  var tr=null;
  var td=null;

  if (status=='-1') {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else if (avatars_tbl) {
    avatars_tbl.style.display='';
    // Empty avatars table
    for (var i=avatars_tbl.rows.length-3; i>0; i--) {
      avatars_tbl.deleteRow(i);
    }
    avatars_count=actionHandler.countElements('avatar');
    while (null!=(avatar=actionHandler.getElement('avatar', avatar_nr++))) {
      if (1==(avatar_nr%2)) {
        tr=avatars_tbl.insertRow(avatars_tbl.rows.length-2);
      }
      avatar_id=stringToNumber(actionHandler.getCdata('id', 0, avatar));
      avatar_binaryfile_id=stringToNumber(actionHandler.getCdata('binaryfile_id', 0, avatar));
      td=tr.insertCell(-1);
      td.innerHTML='<img id="avatar_img_'+htmlspecialchars(avatar_id)+'" src="'+htmlspecialchars(formlink)+'?b_id='+htmlspecialchars(avatar_binaryfile_id)+'&amp;s_id='+htmlspecialchars(s_id)+'&amp;b_x=100&amp;b_y=85" border="0" alt="'+htmlspecialchars(getLng('avatar'))+'" title="'+htmlspecialchars(getLng('avatar'))+'" style="cursor:pointer" />';
      if (avatar_id>0) {
        td.innerHTML+='<br />'
                      +'<label for="avatar_primary_'+htmlspecialchars(avatar_id)+'" title="'+htmlspecialchars(getLng('primary'))+'">'
                      +'<input type="radio" name="avatar_primary" id="avatar_primary_'+htmlspecialchars(avatar_id)+'" onclick="setPrimaryAvatar('+htmlspecialchars(avatar_id)+')"; return false;" '+(actionHandler.getCdata('primary', 0, avatar, 'n')=='y'? 'checked="checked"' : '')+'>'
                      +'&nbsp;'+htmlspecialchars(getLng('primary'))
                      +'</label>'
                      +'<br />'
                      +'<a href="." title="'+htmlspecialchars(getLng('delete_avatar'))+'" onclick="deleteAvatar('+htmlspecialchars(avatar_id)+'); return false;">'
                      +htmlspecialchars(getLng('delete_avatar'))
                      +'</a>'
                      ;
      }
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';
      $('avatar_img_'+avatar_id).binaryfile_id=avatar_binaryfile_id;
      $('avatar_img_'+avatar_id).ow_width=stringToNumber(actionHandler.getCdata('width', 0, avatar))+10;
      $('avatar_img_'+avatar_id).ow_height=stringToNumber(actionHandler.getCdata('height', 0, avatar))+10;
      $('avatar_img_'+avatar_id).onclick=function() {
        openWindow(formlink+'?inc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
        return false;
      };
    }
    if (0==(avatar_nr%2)) {
      td.colSpan=2;
    }
    if (avatars_count<avatarsMaxCount || avatars_count==1 && avatarsMaxCount>0 && avatar_id==0) {
      $('upload_avatar_row').style.display='';
      if (avatarGalleryAllowed) {
        $('avatar_gallery_row').style.display='';
      } else {
        $('avatar_gallery_row').style.display='none';
      }
    } else {
      $('upload_avatar_row').style.display='none';
      $('avatar_gallery_row').style.display='none';
    }
  }
  toggleProgressBar(false);
  // Reset window status resolution
  setMouseoverStatus();
}


/**
 * Update profile
 * @param   boolean   now   If TRUE, then request will be sent immediately
 */
function profile_start_update(now) {
  clearInterval(updaterIntervalHandle);
  // Request new room structure
  if (typeof(now)=='boolean' && true==now) {
    getRoomStructure();
  }
  // Set new interval
  updaterIntervalHandle=setInterval('getRoomStructure()', updaterInterval*1000);
}


/**
 * This function will be triggered after new invitation has been arrived
 */
function getNewInvitations() {
  sendData('_CALLBACK_getNewInvitations()', formlink, 'POST', 'ajax='+urlencode('get_invitations')+'&s_id='+urlencode(s_id));
}
function _CALLBACK_getNewInvitations() {
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  var i=0;
  var invitation=null;
  var invitation_msg='';
  if (status=='-1') {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    i=0;
    while (null!=(invitation=actionHandler.getElement('invitation', i++))) {
      invitation_msg=getLng('user_invited_you');
      invitation_msg=invitation_msg.split('[USER]').join(coloredToPlain(actionHandler.getCdata('author_nickname', 0, invitation), false));
      invitation_msg=invitation_msg.split('[ROOM]').join(actionHandler.getCdata('room_name', 0, invitation));
      if (confirm(invitation_msg)) {
        ActiveRoomId=stringToNumber(actionHandler.getCdata('room_id', 0, invitation));
        enterChatRoom();
        return false;
      }
    }
  }
}


/**
 * This function will be triggered after new messages arrived
 */
function getNewMessages() {
  sendData('_CALLBACK_getNewMessages()', formlink, 'POST', 'ajax='+urlencode('get_new_messages')+'&s_id='+urlencode(s_id));
}
function _CALLBACK_getNewMessages() {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  var i=0;
  var abuses=null;
  var abuse=null;
  var abuse_nr=0;
  var abuse_id=0;
  var abuse_data=null;

  if (status=='-1') {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    if (null!=(abuses=actionHandler.getElement('abuses'))) {
      while (null!=(abuse=actionHandler.getElement('abuse', abuse_nr++, abuses))) {
        abuse_data=new Array();
        abuse_data['id']=stringToNumber(actionHandler.getCdata('id', 0, abuse));
        abuse_data['date']=actionHandler.getCdata('date', 0, abuse);
        abuse_data['author_id']=stringToNumber(actionHandler.getCdata('author_id', 0, abuse));
        abuse_data['author_nickname']=actionHandler.getCdata('author_nickname', 0, abuse);
        abuse_data['category']=actionHandler.getCdata('category', 0, abuse);
        abuse_data['room_id']=stringToNumber(actionHandler.getCdata('room_id', 0, abuse));
        abuse_data['room_name']=actionHandler.getCdata('room_name', 0, abuse);
        abuse_data['abuser_nickname']=actionHandler.getCdata('abuser_nickname', 0, abuse);
        abuse_data['description']=actionHandler.getCdata('description', 0, abuse);
        receivedAbuses[abuse_data['id']]=abuse_data;

        openWindow(formlink+'?s_id='+s_id+'&inc=abuse', 'abuse_'+abuse_data['id'], 600, 450, false, false, false, false, true);
      }
    }
    
  }
}


/**
 * Delete avatar
 */
function deleteAvatar(avatar_id) {
  flushDisplay();
  if (typeof(avatar_id)=='number' && avatar_id>0 && confirm(getLng('confirm_delete_avatar'))) {
    sendData('_CALLBACK_deleteAvatar()', formlink, 'POST', 'ajax='+urlencode('delete_avatar')+'&s_id='+urlencode(s_id)+'&avatar_id='+urlencode(avatar_id)+'&profile_user_id='+urlencode(profileUserId));
  }
  return false;
}
function _CALLBACK_deleteAvatar() {
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  if (status=='-1') {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    if (message!=null) {
      alert(message);
    }
    if (status=='0') {
      // Avatar deleted
      // Reload avatars
      getAvatars();
    }
  }
  toggleProgressBar(false);
}


/**
 * Open "Upload avatar" window
 */
function showNewAvatarForm() {
  flushDisplay();
  try {
    if (uploadWindow && !uploadWindow.closed) {
      uploadWindow.close();
    }
  } catch (e) {}
  setTimeout("openWindow(formlink+'?s_id='+s_id+'&inc=upload&f_target=avatar&profile_user_id="+urlencode(profileUserId)+"', 'file_upload', 400, 80, false, false, false, false, true);", 150);
}


/**
 * Parse response from "Avatar upload" window
 * @param   int       code            Response code
 * @param   string    message         Response message
 * @param   int       binaryfile_id   Optional: Binaryfile ID
 * @param   int       width           Optional: If file was an image: width
 * @param   int       height          Optional: If file was an image: height
 */
function parseUploadResponse(code, message, binaryfile_id, width, height) {
  if (typeof(code)!='undefined' && typeof(message)!='undefined') {
    switch (code) {

      case 0:
        // Success
        getAvatars();
        setTimeout('alert(\''+message.split('\'').join('\\\'')+'\')', 200);
      break;

      case -1:
        // No file uploaded
        // do nothing ;)
      break;

      default:
        alert(message);
      break;

    }
  }
  // Reset window status resolution
  setMouseoverStatus();
}


/**
 * Delete nickname
 * @param   int   nickname_id   Nickname ID
 */
function deleteNickname(nickname_id) {
  var msg=getLng('confirm_delete_nickname').split('[NICKNAME]').join($('nickname_span_'+nickname_id).nickname_plain);
  if (confirm(msg)) {
    sendData('_CALLBACK_deleteNickname('+nickname_id+')', formlink, 'POST', 'ajax='+urlencode('delete_nickname')+'&s_id='+urlencode(s_id)+'&nickname_id='+urlencode(nickname_id)+'&profile_user_id='+urlencode(profileUserId));
  }
  return false;
}
function _CALLBACK_deleteNickname(nickname_id) {
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  var default_nickname_id=actionHandler.getCdata('default_nickname_id');
  if (status=='-1') {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    if (message=='OK') {
      // Nickname deleted
      // Redraw nicknames table
      flushNickNamesTable();
      if (status!=null) {
        alert(status);
      }
    } else {
      // Nickname not deleted
      if (message!=null) {
        alert(message);
      }
    }
  }
  toggleProgressBar(false);
}


/**
 * Add new nickname or update existing one
 * @param   string    callBack      Callback function (optional) which will be executed on success
 * @param   int       nickname_id   Nickname ID (if update nickname)
 * @param   string    nickname      Nickname
 */
function manageNickname(callBack, nickname_id, nickname) {
  flushDisplay();
  if (typeof(nickname)!='string') {
    nickname=prompt(getLng('enter_new_nickname')+':', last_nickname);
  }
  if (typeof(nickname_id)!='number') {
    nickname_id=0;
  }
  var nickname_plain='';
  if (nickname!=null) {
    nickname=optimizeColored(trimString(nickname));
    last_nickname=nickname;
    nickname_plain=coloredToPlain(nickname, false);
    if (nickname_plain=='') {
      alert(getLng('nickname_empty_error'));
    } else if (nickname_plain.length<nickname_length_min) {
      alert(getLng('nickname_too_short_error').split('[LENGTH]').join(nickname_length_min));
    } else if (nickname_plain.length>nickname_length_max) {
      alert(getLng('nickname_too_long_error').split('[LENGTH]').join(nickname_length_max));
    } else {
      if (nickname_id>0) {
        // Update nickname
        sendData('_CALLBACK_manageNickname(\''+(typeof(callBack)=='string'? callBack : '')+'\')', formlink, 'POST', 'ajax='+urlencode('update_nickname')
                                                                                                                   +'&s_id='+urlencode(s_id)
                                                                                                                   +'&new_nickname='+urlencode(nickname)
                                                                                                                   +'&nickname_id='+urlencode(nickname_id)
                                                                                                                   +'&profile_user_id='+urlencode(profileUserId)
                                                                                                                   );
      } else {
        // Add new nickname
        sendData('_CALLBACK_manageNickname(\''+(typeof(callBack)=='string'? callBack : '')+'\')', formlink, 'POST', 'ajax='+urlencode('add_nickname')
                                                                                                                   +'&s_id='+urlencode(s_id)
                                                                                                                   +'&new_nickname='+urlencode(nickname)
                                                                                                                   +'&profile_user_id='+urlencode(profileUserId)
                                                                                                                   );
      }
    }
  }
  return false;
}
function _CALLBACK_manageNickname(callBack) {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  var nickname_id=actionHandler.getCdata('new_nickname_id');
  toggleProgressBar(false);
  if (status=='-1') {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    if (status=='0') {
      // Nickname added
      last_nickname='';
      // Redraw nicknames table
      flushNickNamesTable();
      if (callBack!='') {
        if (callBack=='enterChatRoom()') {
          setTimeout('enterChatRoom('+nickname_id+')', 100);
        } else {
          setTimeout(callBack, 100);
        }
      } else {
        alert(message);
      }
    } else {
      // Nickname not added
      if (message!=null) {
        alert(message);
      }
    }
  }
}


/**
 * Get nicknames list from server
 */
function getNickNames() {
  sendData('_CALLBACK_getNickNames()', formlink, 'POST', 'ajax='+urlencode('get_nicknames')+'&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId));
}
function _CALLBACK_getNickNames() {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var nickname_nr=0;
  var nickname=null;
  var status=actionHandler.getCdata('status');
  if (status=='-1') {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    if (message=='OK') {
      // Redraw nicknames table
      flushNickNamesTable();
    } else if (message!=null) {
      // An error
      alert(message);
    }
  }
  toggleProgressBar(false);
}


/**
 * Flush/redraw nicknames table
 */
function flushNickNamesTable() {
  var i=0;
  var nickname=null;
  var nick='';
  var nick_plain='';
  var nick_id=0;
  var is_default='n';
  var nickNamesTbl=$('nicknames_table');

  CurrentNicknameID=0;
  for (var ii=nickNamesTbl.rows.length-1; ii>0; ii--) {
    if (nickNamesTbl.rows[ii] && nickNamesTbl.rows[ii].id.indexOf('nickname_row_')==0) {
      nickNamesTbl.deleteRow(ii);
    }
  }

  // IE6 behavior
  $('nicknames_area').innerHTML=$('nicknames_area').innerHTML;

  while (nickname=actionHandler.getElement('nickname', i)) {
    nick_id=actionHandler.getCdata('id', 0, nickname);
    nick=actionHandler.getCdata('nickname', 0, nickname);
    nick_plain=actionHandler.getCdata('nickname_plain', 0, nickname);
    is_default=actionHandler.getCdata('default', 0, nickname);
    showNickNameRow(nick_id, nick, is_default=='y');
    if (is_default=='y') {
      CurrentNicknameID=nick_id;
    }
    i++;
  }
  if (i>0) {
    // There are nicknames
    $('no_nicknames').style.display='none';
  } else {
    // There are no nicknames
    $('no_nicknames').style.display='';
  }
  if (i<nicknamesMaxCount) {
    $('new_nickname_link_row').style.display='';
  } else {
    $('new_nickname_link_row').style.display='none';
  }
  // Reset window status resolution
  setMouseoverStatus();
}


/**
 * Show nickname table row
 * @param   int       nickname_id       Nickname ID
 * @param   string    nickname          Nickname
 * @param   boolean   is_default        Flag: if TRUE, then nickname will be displayed as default
 */
function showNickNameRow(nickname_id, nickname, is_default) {
  var nickNamesTbl=$('nicknames_table');
  var newRow=null;
  var newCol=null;

  newRow=nickNamesTbl.insertRow(nickNamesTbl.rows.length-1);
  newRow.id='nickname_row_'+nickname_id;
  newCol=newRow.insertCell(-1);
  newCol.innerHTML='<label for="nickname_selector_'+nickname_id+'" title="'+getLng('active')+'">'
                  +'<input type="radio" name="nickname_selector" id="nickname_selector_'+nickname_id+'" value="'+nickname_id+'" onclick="setDefaultNickname('+nickname_id+')" '+(is_default? 'checked="checked"' : '')+' />'
                  +'&nbsp;<span id="nickname_span_'+nickname_id+'">'+coloredToHTML(nickname)+'</span>'
                  +'</label>'
                  ;
  setCssClass(newCol, '.tbl_row');

  newCol=newRow.insertCell(-1);
  setCssClass(newCol, '.tbl_row');
  newCol.innerHTML='<a href="#" onclick="deleteNickname('+nickname_id+')" title="'+htmlspecialchars(getLng('delete_nickname'))+'"><img src="./pic/delete_13x13.gif" alt="'+htmlspecialchars(getLng('delete_nickname'))+'" border="0"></a>'
                  +'&nbsp;<a href="#" onclick="showNicknameForm('+nickname_id+')" title="'+htmlspecialchars(getLng('edit'))+'"><img src="./pic/edit_13x13.gif" alt="'+htmlspecialchars(getLng('edit'))+'" border="0"></a>';
  $('nickname_span_'+nickname_id).nickname_plain=coloredToPlain(nickname, false);
  $('nickname_span_'+nickname_id).nickname_colored=nickname;
}


/**
 * Hide all dynamically displayed input fields and status bar
 */
function flushDisplay() {
  hideChangeGenderForm();
  hideNicknameForm();
  hidePasswordFieldBox();
}


/**
 * Validate and save new email address
 */
function changeEmailAddress() {
  flushDisplay();
  var email=prompt(getLng('enter_new_email_address')+':', last_email);
  if (email!=null) {
    email=trimString(email).substring(0, 255);
    last_email=email;
    if (email!=$('email_address_span').innerHTML) {
      if (!checkEmail(email)) {
        // New email address is invalid
        alert(getLng('email_invalid'));
      } else {
        // Email address seems to be OK
        sendData('_CALLBACK_changeEmailAddress()', formlink, 'POST', 'ajax='+urlencode('change_email')+'&s_id='+urlencode(s_id)+'&email='+urlencode(email)+'&profile_user_id='+urlencode(profileUserId));
      }
    }
  }
  return false;
}
function _CALLBACK_changeEmailAddress() {
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  switch (status) {
    case  '-1':
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case '0':
      // Email changed
      var activation_required=actionHandler.getCdata('activation_required');
      if (activation_required==1) {
        // New email address must be activated
        flushDisplay();
        alert(message);
      } else {
        // Email address has been changed
        flushDisplay();
        $('email_address_span').innerHTML=htmlspecialchars(actionHandler.getCdata('email'));
        alert(message);
      }
    break;
    default:
      // An error occured
      alert(message);
    break;
  }
  // Reset window status resolution
  toggleProgressBar(false);
}


/**
 * Change password
 */
function changePassword() {
  flushDisplay();
  var new_pass=prompt(getLng('enter_new_password')+':', '');
  if (new_pass!=null) {
    if (new_pass=='') {
      // Password is empty
      alert(getLng('password_empty'));
    } else if (new_pass.length<3) {
      // Password is too short
      alert(getLng('password_too_short'));
    } else {
      // Store new password
      sendData('_CALLBACK_changePassword()', formlink, 'POST', 'ajax='+urlencode('change_password')+'&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId)+'&password='+base64encode(urlencode(new_pass)));
    }
  }
}
function _CALLBACK_changePassword() {
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  switch (status) {
    case  '-1':
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case '0':
      // Password changed
      flushDisplay();
      alert(message);
    break;
    default:
      // An error occured
      alert(message);
    break;
  }
  // Reset window status resolution
  toggleProgressBar(false);
}


/**
 * Display "Change gender" input fields
 */
function showChangeGenderForm() {
  flushDisplay();
  $('gender_span').style.display='none';
  $('gender_input_span').style.display='';
  $('gender').value=currentProfileGender;
}


/**
 * Hide "Change gender" input fields
 */
function hideChangeGenderForm() {
  $('gender_span').style.display='';
  $('gender_input_span').style.display='none';
}


/**
 * Display gender image
 * @param   string    gender    Gender: 'm' (male), 'f' (female) or '-' (not specified)
 */
function showGenderImage() {
  $('gender_image').src='./pic/gender_'+currentProfileGender+'_10x10.gif';
  $('gender_image').title=getLng('gender_'+currentProfileGender);
}


/**
 * Enter chat room
 * @param   int       nickname_id     ID of the nickname to use in the room
 * @param   string    password        Optional. Room password
 * @param   int       room_id         Optional. Room ID.
 */
function enterChatRoom(nickname_id, password, room_id) {
  var stealthbox=$('stealth_mode_chkbox');
  var stealth_mode=(typeof(stealthbox)=='object' && stealthbox && true==stealthbox.checked)? 'y' : 'n';
  if (typeof(nickname_id)!='string' && typeof(nickname_id)!='number') {
    nickname_id=CurrentNicknameID;
  }
  nickname_id=stringToNumber(nickname_id);
  if (typeof(room_id)=='number' && room_id>0) {
    ActiveRoomId=room_id;
  }
  if (ActiveRoomId==0) {
    // No room selected
    alert(getLng('select_room'));
  } else if (nickname_id==0) {
    // User has no nicknames
    manageNickname('enterChatRoom(null, null);');
  } else {
    if (typeof(password)=='undefined' || null==password) {
      if (!isAdmin && CategoryTree[ActiveCategoryId]['rooms'][ActiveRoomId] && CategoryTree[ActiveCategoryId]['rooms'][ActiveRoomId]['password_protected'] && !CategoryTree[ActiveCategoryId]['rooms'][ActiveRoomId]['moderated_by_me']) {
        showPasswordFieldBox(mouseX, mouseY, 'enterChatRoom('+nickname_id+', \'/RESULT/\')', null, getLng('room_password'));
        return false;
      } else {
        password='';
      }
    }
    sendData('_CALLBACK_enterChatRoom()', formlink, 'POST', 'ajax='+urlencode('enter_chat_room')+'&s_id='+urlencode(s_id)+'&room_id='+urlencode(ActiveRoomId)+'&nickname_id='+urlencode(nickname_id)+'&stealth_mode='+urlencode(stealth_mode)+'&password='+urlencode(base64encode(password)));
  }
}
function _CALLBACK_enterChatRoom() {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  switch (status) {
    case  '-1':
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case '0':
      // Room changed. Load room page.
      $('dummyform').s_id.value=s_id;
      $('dummyform').inc.value='chat_room';
      $('dummyform').ts.value=unixTimeStamp();
      $('dummyform').submit();
      return false;
    break;
    case '400':
      // Error: Room does not exists
      alert(message);
      getRoomStructure();
      clearCategoryRooms();
    break;
    default:
      // Other error
      alert(message);
    break;
  }
  // Reset window status resolution
  toggleProgressBar(false);
}


/**
 * Show nickname form
 * @param   int   nickname_id   Nickname ID
 */
function showNicknameForm(nickname_id) {
  flushDisplay();
  var nickname_original='';
  if (typeof(nickname_id)=='undefined') {
    nickname_id=0;
  } else {
    nickname_id=stringToNumber(nickname_id);
  }
  if (nickname_id>0) {
    nickname_original=$('nickname_span_'+nickname_id).nickname_colored;
  }

  $('nicknames_table').style.display='none';
  $('new_nickname_link_row').style.display='none';
  $('colors_header_row').style.display='none';
  $('nickname_colorizer_table').style.display='';

  $('nickname_text_input').value=coloredToPlain(nickname_original, false);
  $('nickname_text_input').value_plain=coloredToPlain(nickname_original, false);
  $('nickname_text_input').value_colored=nickname_original;

  $('nickname_text_input').onkeyup=function(e) {
    if ($('nickname_text_input').value_plain!=this.value) {
      this.value=trimString(this.value, false, false, 1);
      $('nickname_text_input').value_plain=this.value;
      $('nickname_text_input').value_colored=$('nickname_text_input').value_plain;
      if ($('nickname_text_input').value_colored==coloredToPlain($('nickname_text_input').value_colored, false)) {
        $('nickname_text_input').value_colored='^'+defaultNicknameColor+$('nickname_text_input').value_colored;
      }
    }
    if (!e) {
      e=window.event;
    }
    if (e) {
      if (typeof(e.keyCode)=='number') {
        // DOM-compatible
        kk=e.keyCode;
      } else if(typeof(e.which)=='number') {
        // NS4
        kk=e.keyCode;
      } else if(typeof(e.charCode)=='number') {
        // Other NS and Mozilla versions
        kk=e.keyCode;
      }
      if (kk==13) {
        $('save_nickname_color_btn').click();
        return false;
      }
    }
    $('nickname_preview').innerHTML=coloredToHTML($('nickname_text_input').value_colored);
  };
  $('nickname_text_input').onchange=$('nickname_text_input').onkeydown;
  $('nickname_text_input').onclick=$('nickname_text_input').onkeydown;
  $('nickname_text_input').onblur=$('nickname_text_input').onkeydown;
  $('nickname_text_input').onfocus=$('nickname_text_input').onkeydown;
  $('nickname_preview').innerHTML=coloredToHTML($('nickname_text_input').value_colored);

  eval('$(\'save_nickname_color_btn\').onclick=function() { manageNickname(null, '+nickname_id+', $(\'nickname_text_input\').value_colored); }');

  colorbox_callback_func=function(color) {
    $('nickname_text_input').value_colored=applyColorCode('nickname_text_input', color, $('nickname_text_input').value_colored);
    $('nickname_preview').innerHTML=coloredToHTML($('nickname_text_input').value_colored);
  }
  $('nickname_text_input').focus();
}


/**
 * Hide nickname form
 */
function hideNicknameForm() {
  $('nickname_colorizer_table').style.display='none';
  $('nicknames_table').style.display='';
  $('new_nickname_link_row').style.display='';
}


/**
 * Change email visibility
 */
function changeEmailVisibility() {
  flushDisplay();
  sendData('_CALLBACK_changeEmailVisibility()', formlink, 'POST', 'ajax='+urlencode('change_email_visibility')+'&s_id='+urlencode(s_id)+'&hide_email='+urlencode(hideEmail? '0' : '1')+'&profile_user_id='+urlencode(profileUserId));
}
function _CALLBACK_changeEmailVisibility() {
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  switch (status) {
    case  '-1':
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case '0':
      // Data changed
      alert(message);
      flushDisplay();
      if (actionHandler.getCdata('hide_email')=='1') {
        hideEmail=true;
        $('hide_email_span').innerHTML=htmlspecialchars(getLng('yes'));
      } else {
        hideEmail=false;
        $('hide_email_span').innerHTML=htmlspecialchars(getLng('no'));
      }
    break;
    default:
      // An error occured
      alert(message);
    break;
  }
  // Reset window status resolution
  toggleProgressBar(false);
}


/**
 * Update userdata field
 * @param   string    field       Field name
 * @param   string    init_val    Optional default value
 */
function updateUserdataField(field, init_val) {
  flushDisplay();
  if ($(field+'_span')) {
    var new_val=prompt(getLng('enter_your_'+field)+':', typeof(init_val)=='string'? init_val : htmlspecialchars_decode($(field+'_span').innerHTML));
    if (new_val!=null) {
      // Store new age
      new_val=trimString(new_val).substring(0, 255);
      sendData('_CALLBACK_updateUserdataField()', formlink, 'POST', 'ajax='+urlencode('update_userdata')+'&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId)+'&'+field+'='+urlencode(new_val));
    }
  }
}
function _CALLBACK_updateUserdataField() {
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  var homepage='';

  switch (status) {

    case  '-1':
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;

    case '0':
      // Data updated
      flushDisplay();

      homepage=actionHandler.getCdata('homepage', null, 0, '');
      if (homepage!='') {
        $('homepage_span').innerHTML='<a href="'+formlink+'?external_url='+urlencode(homepage)+'" title="'+htmlspecialchars(homepage)+'" target="_blank">'+htmlspecialchars(homepage)+'</a>';
        currentProfileHomepage=homepage;
      } else {
        $('homepage_span').innerHTML='&nbsp;';
      }
      $('age_span').innerHTML=htmlspecialchars(actionHandler.getCdata('age', null, 0, ''));
      $('icq_span').innerHTML=htmlspecialchars(actionHandler.getCdata('icq', null, 0, ''));
      $('msn_span').innerHTML=htmlspecialchars(actionHandler.getCdata('msn', null, 0, ''));
      $('aim_span').innerHTML=htmlspecialchars(actionHandler.getCdata('aim', null, 0, ''));
      $('yim_span').innerHTML=htmlspecialchars(actionHandler.getCdata('yim', null, 0, ''));
      $('location_span').innerHTML=htmlspecialchars(actionHandler.getCdata('location', null, 0, ''));
      $('occupation_span').innerHTML=htmlspecialchars(actionHandler.getCdata('occupation', null, 0, ''));
      $('interests_span').innerHTML=htmlspecialchars(actionHandler.getCdata('interests', null, 0, ''));
      // Display gender image
      currentProfileGender=actionHandler.getCdata('gender', null, 0, '-');
      showGenderImage();
      alert(message);
    break;

    default:
      // An error occured
      if (message!=null) {
        alert(message);
      }
    break;

  }
  toggleProgressBar(false);
}


/**
 * Save gender setting
 */
function updateGender(gender) {
  flushDisplay();
  sendData('_CALLBACK_updateUserdataField()', formlink, 'POST', 'ajax='+urlencode('update_userdata')+'&s_id='+urlencode(s_id)+'&gender='+urlencode(gender)+'&profile_user_id='+urlencode(profileUserId));
  return false;
}


/**
 * Send abuse data to opened abuse window
 * @param   object    aw    Abuse window handler
 * @param   int       id    Abuse ID
 * @return object
 */
function getAbuseData(aw, id) {
  var abuse_data=null;
  if (typeof(aw)=='object' && aw) {
    if (typeof(id)=='number' && id>0 && receivedAbuses[id]) {
      abuse_data=receivedAbuses[id];
    } else {
      try {
        aw.close();
      } catch (e) {}
    }
  }
  return abuse_data;
}


/**
 * Set new default nickname
 * @param   int   id    Nickname ID
 */
function setDefaultNickname(id) {
  sendData('toggleProgressBar(false)', formlink, 'POST', 'ajax='+urlencode('set_default_nickname')+'&s_id='+urlencode(s_id)+'&nickname_id='+urlencode(id)+'&profile_user_id='+urlencode(profileUserId));
}


/**
 * Set new primary avatar
 * @param   int   id    Avatar ID
 */
function setPrimaryAvatar(id) {
  sendData('toggleProgressBar(false)', formlink, 'POST', 'ajax='+urlencode('set_primary_avatar')+'&s_id='+urlencode(s_id)+'&avatar_id='+urlencode(id)+'&profile_user_id='+urlencode(profileUserId));
}


/**
 * Get member data (if called by admin)
 */
function getMemberData() {
  if (isAdmin) {
    toggleProgressBar(true);
    ajaxMember.sendData('_CALLBACK_getMemberData()', 'POST', formlink, 'ajax='+urlencode('get_member_data')+'&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId), true);
  }
}
function _CALLBACK_getMemberData() {
//debug(ajaxMember.getResponseString()); return false;
  var message=ajaxMember.getCdata('message');
  var status=ajaxMember.getCdata('status');
  var member_data=null;
  var category_nr=0;
  var category_name='';
  var category_names=new Array();
  var room_nr=0;
  var room_name='';
  var room_names=new Array();

  switch (status) {

    case  '-1':
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;

    case '1':
      // Error
      alert(message);
      window.close();
    break;

    case '0':
      // OK
      if (null!=(member_data=ajaxMember.getElement('member_data', 0))) {
        // Username (login)
        $('member_username').innerHTML=htmlspecialchars(ajaxMember.getCdata('login', 0, member_data));
        // Level
        if ('1'==ajaxMember.getCdata('is_admin', 0, member_data)) {
          // "Admin" level
          $('member_level_name').innerHTML=htmlspecialchars(getLng('admin'));
          $('member_level_id').value='a';
        } else if ('1'==ajaxMember.getCdata('is_registered', 0, member_data)) {
          // "Registered user" level
          $('member_level_id').value='r';
          $('member_level_name').innerHTML=htmlspecialchars(getLng('registered'));
        } else {
          // Guest
          $('member_level_id').value='g';
          $('member_level_name').innerHTML=htmlspecialchars(getLng('guest'));
        }
        // Moderated categories
        while (null!=(category_name=ajaxMember.getCdata('moderated_category', category_nr++, member_data))) {
          category_names.push(category_name);
        }
        if (category_names.length) {
          $('member_moderated_categories').innerHTML=htmlspecialchars('"'+category_names.join('", "')+'"');
        } else {
          $('member_moderated_categories').innerHTML='-';
        }
        // Moderated rooms
        while (null!=(room_name=ajaxMember.getCdata('moderated_room', room_nr++, member_data))) {
          room_names.push(room_name);
        }
        if (room_names.length) {
          $('member_moderated_rooms').innerHTML=htmlspecialchars('"'+room_names.join('", "')+'"');
        } else {
          $('member_moderated_rooms').innerHTML='-';
        }
        // Activated?
        if ('1'!=ajaxMember.getCdata('activated', 0, member_data)) {
          // No
          $('member_not_activated_row').style.display='';
        }
      } else {
        window.close();
      }
    break;

  }
  toggleProgressBar(false);
}


/**
 * Display "Change member level" form
 */
function showMemberLevelForm() {
  $('member_level').style.display='none';
  $('member_level_options').style.display='';
  if ($('member_level_option_'+$('member_level_id').value)) {
    $('member_level_option_'+$('member_level_id').value).click();
  }
}


/**
 * Hide "Change member level" form
 */
function hideMemberLevelForm() {
  $('member_level').style.display='';
  $('member_level_options').style.display='none';
}


/**
 * Set new member level
 */
function setMemberLevel() {
  var new_level='';
  if (isAdmin) {
    if ($('member_level_option_r').checked) {
      new_level='r';
    } else if ($('member_level_option_a').checked) {
      new_level='a';
    }
    if (currentUserId==profileUserId) {
      alert(getLng('change_own_level_error'));
    } else if (new_level=='') {
      alert(getLng('select_new_level_or_cancel'));
      return false;
    } else if (confirm(getLng('sure_change_user_level')) && confirm(getLng('really_sure'))) {
      toggleProgressBar(true);
      ajaxMember.sendData('_CALLBACK_setMemberLevel()', 'POST', formlink, 'ajax='+urlencode('set_user_level')+'&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId)+'&level='+urlencode(new_level), true);
    }
    hideMemberLevelForm();
  }
}
function _CALLBACK_setMemberLevel() {
  var message=ajaxMember.getCdata('message');
  var status=ajaxMember.getCdata('status');
  switch (status) {
    case  '-1':
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    default:
      if (message!=null) {
        alert(message);
        getMemberData();
      }
    break;
  }
}


/**
 * Delete user
 */
function deleteUser() {
  if (currentUserId==profileUserId) {
    alert(getLng('delete_yourself_error'));
  } else if (confirm(getLng('sure_delete_user')) && confirm(getLng('really_sure'))) {
    toggleProgressBar(true);
    ajaxMember.sendData('_CALLBACK_deleteUser()', 'POST', formlink, 'ajax='+urlencode('delete_user')+'&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId), true);
  }
}
function _CALLBACK_deleteUser() {
  var message=ajaxMember.getCdata('message');
  var status=ajaxMember.getCdata('status');
  switch (status) {
    case  '-1':
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case  '0':
      // User deleted
      alert(message);
      try {
        $('memberlist_search_button', window.opener.document).click();
      } catch (e) {}
      window.close();
    break;
    default:
      if (message!=null) {
        alert(message);
        getMemberData();
      }
    break;
  }
}


/**
 * Display avatar gallery
 */
function showAvatarGallery() {
  if (avatarGalleryAllowed) {
    openWindow(formlink+'?s_id='+urlencode(s_id)+'&inc=avatar_gallery&profile_user_id='+urlencode(profileUserId),
               'avatar_gallery',
               600,
               700,
               false,
               false,
               false,
               false,
               true,
               false,
               false,
               false,
               false,
               true,
               0,
               0);

  }
}


/**
 * Activate user account manually
 */
function activateUser() {
  if (confirm(getLng('sure_activate_account')) && confirm(getLng('really_sure'))) {
    toggleProgressBar(true);
    ajaxMember.sendData('_CALLBACK_activateUser()', 'POST', formlink, 'ajax='+urlencode('activate_user')+'&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId), true);
  }
}
function _CALLBACK_activateUser() {
  var message=ajaxMember.getCdata('message');
  var status=ajaxMember.getCdata('status');
  switch (status) {
    case  '-1':
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case  '0':
      // Account activated
      alert(message);
      window.location.reload();
      toggleProgressBar(false);
    break;
    default:
      // An error occured
      toggleProgressBar(false);
    break;
  }
}
