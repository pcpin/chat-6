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
 * Current nicknames count
 * @var int
 */
var nicknamesCount=0;

/**
 * Additional timeout handler for enterChatRoom() function
 * @var int
 */
var enterChatRoomTimeOut=0;



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
  // Set "onunload" handler
  window.onunload=function() {
    // Send "Page unloaded" signal to server
    if (!SkipPageUnloadedMsg && (typeof(window.opener)=='undefined' || window.opener==null || typeof(window.opener.appName_)!='string' || window.opener.appName_!='pcpin_chat' || typeof(window.opener.initChatRoom)=='undefined')) {
      openWindow(formlink+'?inc=page_unloaded&s_id='+urlencode(s_id), '', 1, 1, false, false, false, false, false, false, false, false, false, false, 0, 0);
    }
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
    sendData('_CALLBACK_getAvatars()', formlink, 'POST', 'ajax=get_avatars&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId));
  }
}
function _CALLBACK_getAvatars() {
//debug(actionHandler.getResponseString()); return false;

  var avatars_tbl=$('avatars_tbl');
  var avatar_nr=0;
  var avatar_id=0;
  var avatar_binaryfile_id=0;
  var avatar_primary='';
  var avatars_count=0;

  var tr=null;
  var td=null;

  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else if (avatars_tbl) {
    avatars_tbl.style.display='';
    // Empty avatars table
    for (var i=avatars_tbl.rows.length-3; i>0; i--) {
      avatars_tbl.deleteRow(i);
    }
    avatars_count=actionHandler.data['avatar'].length;
    for (avatar_nr=0; avatar_nr<avatars_count; avatar_nr++) {
      if (0==(avatar_nr%2)) {
        tr=avatars_tbl.insertRow(avatars_tbl.rows.length-2);
      }
      avatar_id=stringToNumber(actionHandler.data['avatar'][avatar_nr]['id'][0]);
      avatar_binaryfile_id=stringToNumber(actionHandler.data['avatar'][avatar_nr]['binaryfile_id'][0]);
      avatar_primary=actionHandler.data['avatar'][avatar_nr]['primary'][0];
      td=tr.insertCell(-1);
      td.innerHTML='<img id="avatar_img_'+htmlspecialchars(avatar_id)+'" src="'+htmlspecialchars(formlink)+'?b_id='+htmlspecialchars(avatar_binaryfile_id)+'&amp;s_id='+htmlspecialchars(s_id)+'&amp;b_x=100&amp;b_y=85" border="0" alt="'+htmlspecialchars(getLng('avatar'))+'" title="'+htmlspecialchars(getLng('avatar'))+'" style="cursor:pointer" />';
      if (avatar_id>0) {
        td.innerHTML+='<br />'
                      +'<label for="avatar_primary_'+htmlspecialchars(avatar_id)+'" title="'+htmlspecialchars(getLng('primary'))+'">'
                      +'<input type="radio" name="avatar_primary" id="avatar_primary_'+htmlspecialchars(avatar_id)+'" onclick="setPrimaryAvatar('+htmlspecialchars(avatar_id)+')"; return false;" '+(avatar_primary=='y'? 'checked="checked"' : '')+'>'
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
      $('avatar_img_'+avatar_id).ow_width=stringToNumber(actionHandler.data['avatar'][avatar_nr]['width'][0])+10;
      $('avatar_img_'+avatar_id).ow_height=stringToNumber(actionHandler.data['avatar'][avatar_nr]['height'][0])+10;
      $('avatar_img_'+avatar_id).onclick=function() {
        openWindow(formlink+'?inc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
        return false;
      };
    }
    if (1==(avatar_nr%2)) {
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
}


/**
 * Update profile
 * @param   boolean   now   If TRUE, then request will be sent immediately
 */
function profile_start_update(now) {
  clearTimeout(updaterIntervalHandle);
  // Request new room structure
  if (typeof(now)=='boolean' && true==now) {
    getRoomStructure('profile_start_update(), false, true');
  } else {
    // Set new interval
    updaterIntervalHandle=setTimeout('getRoomStructure("profile_start_update()", true)', updaterInterval*1000);
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
      confirm(invitation_msg, null, null, 'ActiveRoomId='+actionHandler.data['invitation'][i]['room_id'][0]+'; enterChatRoom();');
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
 * Delete avatar
 * @param   int       avatar_id     Avatar ID
 * @param   boolean   confirmed     Optional. If TRUE: no confirmation will be displayed. Default: FALSE. 
 */
function deleteAvatar(avatar_id, confirmed) {
  if (typeof(confirmed)!='boolean' || !confirmed) {
    flushDisplay();
    if (typeof(avatar_id)=='number' && avatar_id>0) {
      confirm(getLng('confirm_delete_avatar'), null, null, 'deleteAvatar('+avatar_id+', true)');
    }
  } else {
    sendData('_CALLBACK_deleteAvatar()', formlink, 'POST', 'ajax=delete_avatar&s_id='+urlencode(s_id)+'&avatar_id='+urlencode(avatar_id)+'&profile_user_id='+urlencode(profileUserId));
  }
  return false;
}
function _CALLBACK_deleteAvatar() {
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    toggleProgressBar(false);
    if (actionHandler.status==0) {
      // Avatar deleted
      // Reload avatars
      alert(actionHandler.message, 0, 0, 'getAvatars()');
    } else {
      alert(actionHandler.message);
    }
  }
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
}


/**
 * Delete nickname
 * @param   int       nickname_id   Nickname ID
 * @param   boolean   confirmed     Optional. If TRUE: no confirmation will be displayed.
 */
function deleteNickname(nickname_id, confirmed) {
  if (typeof(confirmed)!='boolean' || !confirmed) {
    var msg=getLng('confirm_delete_nickname').split('[NICKNAME]').join($('nickname_span_'+nickname_id).nickname_plain);
    confirm(msg, null, null, 'deleteNickname('+nickname_id+', true)');
  } else {
    sendData('_CALLBACK_deleteNickname('+nickname_id+')', formlink, 'POST', 'ajax=delete_nickname&s_id='+urlencode(s_id)+'&nickname_id='+urlencode(nickname_id)+'&profile_user_id='+urlencode(profileUserId));
  }
  return false;
}
function _CALLBACK_deleteNickname(nickname_id) {
  if (actionHandler.status==0) {
    // Nickname deleted
    // Redraw nicknames table
    flushNickNamesTable();
    toggleProgressBar(false);
    alert(actionHandler.message);
  } else if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  }
}


/**
 * Add new nickname or update existing one
 * @param   string    callBack      Callback function (optional) which will be executed on success
 * @param   int       nickname_id   Nickname ID (if update nickname)
 * @param   string    nickname      Nickname
 */
function manageNickname(callBack, nickname_id, nickname) {
  flushDisplay();
  if (typeof(callBack)!='string') {
    callBack='';
  }
  if (typeof(nickname_id)!='number') {
    nickname_id=0;
  }
  if (typeof(nickname)!='string') {
    prompt(getLng('enter_new_nickname')+':', last_nickname, null, null, 'manageNickname(\''+callBack.split("'").join("\\'")+'\', '+nickname_id+', promptboxValue)');
    return false;
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
        sendData('_CALLBACK_manageNickname(\''+callBack+'\')', formlink, 'POST', 'ajax=update_nickname'
                                                                                                                   +'&s_id='+urlencode(s_id)
                                                                                                                   +'&new_nickname='+urlencode(nickname)
                                                                                                                   +'&nickname_id='+urlencode(nickname_id)
                                                                                                                   +'&profile_user_id='+urlencode(profileUserId)
                                                                                                                   );
      } else {
        // Add new nickname
        sendData('_CALLBACK_manageNickname(\''+callBack+'\')', formlink, 'POST', 'ajax=add_nickname'
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
  toggleProgressBar(false);
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    if (actionHandler.status==0) {
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
        alert(actionHandler.message);
      }
    } else {
      // Nickname not added
      alert(actionHandler.message);
    }
  }
}


/**
 * Get nicknames list from server
 */
function getNickNames() {
  sendData('_CALLBACK_getNickNames()', formlink, 'POST', 'ajax=get_nicknames&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId));
}
function _CALLBACK_getNickNames() {
//debug(actionHandler.getResponseString()); return false;
  var nickname_nr=0;
  var nickname=null;
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    if (actionHandler.message=='OK') {
      // Redraw nicknames table
      flushNickNamesTable();
    } else {
      // An error
      alert(actionHandler.message);
    }
  }
  toggleProgressBar(false);
}


/**
 * Flush/redraw nicknames table
 */
function flushNickNamesTable() {
  var i=0;
  var nick='';
  var nick_plain='';
  var nick_id=0;
  var is_default='n';
  var nickNamesTbl=$('nicknames_table');

  nicknamesCount=0;
  CurrentNicknameID=0;
  for (var ii=nickNamesTbl.rows.length-1; ii>0; ii--) {
    if (nickNamesTbl.rows[ii] && nickNamesTbl.rows[ii].id.indexOf('nickname_row_')==0) {
      nickNamesTbl.deleteRow(ii);
    }
  }

  // IE6 behaviour
  $('nicknames_area').innerHTML=$('nicknames_area').innerHTML;
  for (i=0; i<actionHandler.data['nickname'].length; i++) {
    nicknamesCount++;
    nick_id=actionHandler.data['nickname'][i]['id'][0];
    nick=actionHandler.data['nickname'][i]['nickname'][0];
    nick_plain=actionHandler.data['nickname'][i]['nickname_plain'][0];
    is_default=actionHandler.data['nickname'][i]['default'][0];
    showNickNameRow(nick_id, nick, is_default=='y');
    if (is_default=='y') {
      CurrentNicknameID=nick_id;
    }
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
 * @param   string    email     Email address
 */
function changeEmailAddress(email) {
  flushDisplay();
  if (typeof(email)!='string') {
    prompt(getLng('enter_new_email_address')+':', last_email, 0, 0, 'changeEmailAddress(promptboxValue)');
  } else {
    email=trimString(email).substring(0, 255);
    last_email=email;
    if (email!=$('email_address_span').innerHTML) {
      if (!checkEmail(email)) {
        // New email address is invalid
        alert(getLng('email_invalid'), 0, 0, "prompt(getLng('enter_new_email_address')+':', last_email, 0, 0, 'changeEmailAddress(promptboxValue)')");
      } else {
        // Email address seems to be OK
        sendData('_CALLBACK_changeEmailAddress()', formlink, 'POST', 'ajax=change_email&s_id='+urlencode(s_id)+'&email='+urlencode(email)+'&profile_user_id='+urlencode(profileUserId));
      }
    }
  }
  return false;
}
function _CALLBACK_changeEmailAddress() {
  toggleProgressBar(false);
  switch (actionHandler.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case 0:
      // Email changed
      if (actionHandler.data['activation_required'][0]=='1') {
        // New email address must be activated
        flushDisplay();
        alert(actionHandler.message);
      } else {
        // Email address has been changed
        flushDisplay();
        $('email_address_span').innerHTML=htmlspecialchars(actionHandler.data['email'][0]);
        alert(actionHandler.message);
      }
    break;
    default:
      // An error occured
      alert(actionHandler.message);
    break;
  }
}


/**
 * Change password
 * @param   string    new_pass      New password
 * @param   string    new_pass2     New password again (for confirmation)
 */
function changePassword(new_pass, new_pass2) {
  flushDisplay();
  if (typeof(new_pass)!='string') {
    prompt(getLng('enter_new_password')+':', '', 0, 0, 'changePassword(promptboxValue)', true);
  } else {
    if (new_pass=='') {
      // Password is empty
      alert(getLng('password_empty'), 0, 0, "prompt(getLng('enter_new_password')+':', '', 0, 0, 'changePassword(promptboxValue)', true)");
    } else if (new_pass.length<3) {
      // Password is too short
      alert(getLng('password_too_short'), 0, 0, "prompt(getLng('enter_new_password')+':', '', 0, 0, 'changePassword(promptboxValue)', true)");
    } else if (typeof(new_pass2)!='string') {
      prompt(getLng('confirm_password')+':', '', 0, 0, 'changePassword("'+new_pass.split('"').join('\\"')+'", promptboxValue)', true);
    } else if (new_pass.length!=new_pass2.length || new_pass!=new_pass2) {
      alert(getLng('passwords_not_ident'), 0, 0, "prompt(getLng('enter_new_password')+':', '', 0, 0, 'changePassword(promptboxValue)', true)");
    } else {
      // Store new password
      sendData('_CALLBACK_changePassword()', formlink, 'POST', 'ajax=change_password&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId)+'&password='+base64encode(urlencode(new_pass)));
    }
  }
}
function _CALLBACK_changePassword() {
  toggleProgressBar(false);
  switch (actionHandler.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case 0:
      // Password changed
      flushDisplay();
      alert(actionHandler.message);
    break;
    default:
      // An error occured
      alert(actionHandler.message);
    break;
  }
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
  clearTimeout(enterChatRoomTimeOut);
  try {
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
      sendData('_CALLBACK_enterChatRoom()', formlink, 'POST', 'ajax=enter_chat_room&s_id='+urlencode(s_id)+'&room_id='+urlencode(ActiveRoomId)+'&nickname_id='+urlencode(nickname_id)+'&stealth_mode='+urlencode(stealth_mode)+'&password='+urlencode(base64encode(password)));
    }
  } catch (e) {
    toggleProgressBar(true);
    enterChatRoomTimeOut=setTimeout('enterChatRoom('+nickname_id+', '+(typeof(password)=='string'? '"'+password.split('"').join('\\"')+'"' : 'null')+', '+room_id+')', 200);
  }
}
function _CALLBACK_enterChatRoom() {
//debug(actionHandler.getResponseString()); return false;
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
  $('new_nickname_link_row').style.display=nicknamesCount<nicknamesMaxCount? '' : 'none';
}


/**
 * Change email visibility
 */
function changeEmailVisibility() {
  flushDisplay();
  sendData('_CALLBACK_changeEmailVisibility()', formlink, 'POST', 'ajax=change_email_visibility&s_id='+urlencode(s_id)+'&hide_email='+urlencode(hideEmail? '0' : '1')+'&profile_user_id='+urlencode(profileUserId));
}
function _CALLBACK_changeEmailVisibility() {
  toggleProgressBar(false);
  switch (actionHandler.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case 0:
      // Data changed
      alert(actionHandler.message);
      flushDisplay();
      if (actionHandler.data['hide_email'][0]=='1') {
        hideEmail=true;
        $('hide_email_span').innerHTML=htmlspecialchars(getLng('yes'));
      } else {
        hideEmail=false;
        $('hide_email_span').innerHTML=htmlspecialchars(getLng('no'));
      }
    break;
    default:
      // An error occured
      alert(actionHandler.message);
    break;
  }
}


/**
 * Update userdata field
 * @param   string    field       Field name
 * @param   string    init_val    Optional default value
 * @param   string    new_val     Optional. New value
 */
function updateUserdataField(field, init_val, new_val) {
  flushDisplay();
  if ($(field+'_span')) {
    if (typeof(new_val)!='string') {
      prompt(getLng('enter_your_'+field)+':', typeof(init_val)=='string'? init_val : htmlspecialchars_decode($(field+'_span').innerHTML), 0, 0, 'updateUserdataField("'+field.split('"').join('\\"')+'", "'+init_val.split('"').join('\\"')+'", promptboxValue)');
    } else {
      // Store new age
      new_val=trimString(new_val).substring(0, 255);
      sendData('_CALLBACK_updateUserdataField()', formlink, 'POST', 'ajax=update_userdata&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId)+'&'+field+'='+urlencode(new_val));
    }
  }
}
function _CALLBACK_updateUserdataField() {
  toggleProgressBar(false);
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  }
  if (actionHandler.status==0) {
    // Data updated
    flushDisplay();
    if (actionHandler.data['homepage'][0]!='') {
      $('homepage_span').innerHTML='<a href="'+formlink+'?external_url='+urlencode(actionHandler.data['homepage'][0])+'" title="'+htmlspecialchars(actionHandler.data['homepage'][0])+'" target="_blank">'+htmlspecialchars(actionHandler.data['homepage'][0])+'</a>';
      currentProfileHomepage=actionHandler.data['homepage'][0];
    } else {
      $('homepage_span').innerHTML='&nbsp;';
    }
    $('age_span').innerHTML=htmlspecialchars(actionHandler.data['age'][0]);
    $('icq_span').innerHTML=htmlspecialchars(actionHandler.data['icq'][0]);
    $('msn_span').innerHTML=htmlspecialchars(actionHandler.data['msn'][0]);
    $('aim_span').innerHTML=htmlspecialchars(actionHandler.data['aim'][0]);
    $('yim_span').innerHTML=htmlspecialchars(actionHandler.data['yim'][0]);
    $('location_span').innerHTML=htmlspecialchars(actionHandler.data['location'][0]);
    $('occupation_span').innerHTML=htmlspecialchars(actionHandler.data['occupation'][0]);
    $('interests_span').innerHTML=htmlspecialchars(actionHandler.data['interests'][0]);
    // Display gender image
    currentProfileGender=actionHandler.data['gender'][0];
    showGenderImage();
  }
  alert(actionHandler.message);
}


/**
 * Save gender setting
 */
function updateGender(gender) {
  flushDisplay();
  sendData('_CALLBACK_updateUserdataField()', formlink, 'POST', 'ajax=update_userdata&s_id='+urlencode(s_id)+'&gender='+urlencode(gender)+'&profile_user_id='+urlencode(profileUserId));
  return false;
}


/**
 * Set new default nickname
 * @param   int   id    Nickname ID
 */
function setDefaultNickname(id) {
  sendData('toggleProgressBar(false)', formlink, 'POST', 'ajax=set_default_nickname&s_id='+urlencode(s_id)+'&nickname_id='+urlencode(id)+'&profile_user_id='+urlencode(profileUserId));
}


/**
 * Set new primary avatar
 * @param   int   id    Avatar ID
 */
function setPrimaryAvatar(id) {
  sendData('toggleProgressBar(false)', formlink, 'POST', 'ajax=set_primary_avatar&s_id='+urlencode(s_id)+'&avatar_id='+urlencode(id)+'&profile_user_id='+urlencode(profileUserId));
}


/**
 * Get member data (if called by admin)
 */
function getMemberData() {
  if (isAdmin) {
    toggleProgressBar(true);
    ajaxMember.sendData('_CALLBACK_getMemberData()', 'POST', formlink, 'ajax=get_member_data&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId), true);
  }
}
function _CALLBACK_getMemberData() {
//debug(ajaxMember.getResponseString()); return false;
  var category_names=new Array();
  var room_names=new Array();

  if (ajaxMember.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  }

  if (typeof(ajaxMember.data['member_data'])!='undefined' && ajaxMember.data['member_data'].length) {
    // Username (login)
    $('member_username').innerHTML=htmlspecialchars(ajaxMember.data['member_data'][0]['login'][0]);
    // Level
    if ('1'==ajaxMember.data['member_data'][0]['is_admin'][0]) {
      // "Admin" level
      $('member_level_name').innerHTML=htmlspecialchars(getLng('admin'));
      $('member_level_id').value='a';
    } else if ('1'==ajaxMember.data['member_data'][0]['is_registered'][0]) {
      // "Registered user" level
      $('member_level_id').value='r';
      $('member_level_name').innerHTML=htmlspecialchars(getLng('registered'));
    } else {
      // Guest
      $('member_level_id').value='g';
      $('member_level_name').innerHTML=htmlspecialchars(getLng('guest'));
    }
    // Moderated categories
    if (typeof(ajaxMember.data['member_data'][0]['moderated_category'])!='undefined') {
      for (i=0; i<ajaxMember.data['member_data'][0]['moderated_category'].length; i++) {
        category_names.push(ajaxMember.data['member_data'][0]['moderated_category'][i]);
      }
    }
    if (category_names.length) {
      $('member_moderated_categories').innerHTML=htmlspecialchars('"'+category_names.join('", "')+'"');
    } else {
      $('member_moderated_categories').innerHTML='-';
    }
    // Moderated rooms
    if (typeof(ajaxMember.data['member_data'][0]['moderated_room'])!='undefined') {
      for (i=0; i<ajaxMember.data['member_data'][0]['moderated_room'].length; i++) {
        room_names.push(ajaxMember.data['member_data'][0]['moderated_room'][i]);
      }
    }
    if (room_names.length) {
      $('member_moderated_rooms').innerHTML=htmlspecialchars('"'+room_names.join('", "')+'"');
    } else {
      $('member_moderated_rooms').innerHTML='-';
    }
    // Activated?
    if ('1'!=ajaxMember.data['member_data'][0]['activated'][0]) {
      // No
      $('member_not_activated_row').style.display='';
    }
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
 * @param   boolean   confirmed     First confirmation
 * @param   boolean   confirmed2    Second confirmation
 */
function setMemberLevel(confirmed, confirmed2) {
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
    } else {
      if (typeof(confirmed)!='boolean' || !confirmed) {
        confirm(getLng('sure_change_user_level'), null, null, 'setMemberLevel(true)');
      } else if (typeof(confirmed2)!='boolean' || !confirmed2) {
        confirm(getLng('really_sure'), null, null, "toggleProgressBar(true); ajaxMember.sendData('_CALLBACK_setMemberLevel()', 'POST', formlink, 'ajax=set_user_level&s_id="+urlencode(s_id)+"&profile_user_id="+urlencode(profileUserId)+"&level="+urlencode(new_level)+"', true);");
      }
    }
  }
}
function _CALLBACK_setMemberLevel() {
  hideMemberLevelForm();
  switch (ajaxMember.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    default:
      alert(ajaxMember.message);
      getMemberData();
    break;
  }
}


/**
 * Delete user
 * @param   boolean   confirmed     First confirmation
 * @param   boolean   confirmed2    Second confirmation
 */
function deleteUser(confirmed, confirmed2) {
  if (isAdmin) {
    if (currentUserId==profileUserId) {
      alert(getLng('delete_yourself_error'));
    } else {
      if (typeof(confirmed)!='boolean' || !confirmed) {
        confirm(getLng('sure_delete_user'), null, null, 'deleteUser(true)');
      } else if (typeof(confirmed2)!='boolean' || !confirmed2) {
        confirm(getLng('really_sure'), null, null, "toggleProgressBar(true); ajaxMember.sendData('_CALLBACK_deleteUser()', 'POST', formlink, 'ajax=delete_user&s_id="+urlencode(s_id)+"&profile_user_id="+urlencode(profileUserId)+"', true);");
      }
    }
  }
}
function _CALLBACK_deleteUser() {
  toggleProgressBar(false);
  switch (ajaxMember.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case  0:
      // User deleted
      alert(ajaxMember.message, null, null, "$('memberlist_search_button', window.opener.document).click(); window.close();");
    break;
    default:
      alert(ajaxMember.message, null, null, 'getMemberData()');
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
 * @param   boolean   confirmed     First confirmation
 */
function activateUser(confirmed) {
  if (isAdmin) {
    if (typeof(confirmed)!='boolean' || !confirmed) {
      confirm(getLng('sure_activate_account'), null, null, 'activateUser(true)');
    } else {
      toggleProgressBar(true);
      ajaxMember.sendData('_CALLBACK_activateUser()', 'POST', formlink, 'ajax=activate_user&s_id='+urlencode(s_id)+'&profile_user_id='+urlencode(profileUserId), true);
    }
  }
}
function _CALLBACK_activateUser() {
  switch (ajaxMember.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case  0:
      // Account activated
      alert(ajaxMember.message);
      window.location.reload();
      toggleProgressBar(false);
    break;
    default:
      // An error occured
      toggleProgressBar(false);
      alert(ajaxMember.message);
    break;
  }
}
