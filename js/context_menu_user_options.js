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
 * XmlHttpRequest handler for managing users
 * @var object
 */
var ajaxUserOptions=new PCPIN_XmlHttpRequest();

/**
 * Name of the function to execute after context menu function
 * @var string
 */
var CallBackContextMenuFunc='';


/**
 * Display user options context menu
 * @param   int       user_id         User ID
 * @param   string    user_nickname   Optional. Nickname.
 */
function showUserOptionsBox(user_id, user_nickname) {
  var urec=null;
  var muted_locally=false;
  var global_muted=false;
  var nickname='';
  var banned=false;
  var online_status=0;
  var tmp=new Array($('context_menu_send_pm'),
                    $('context_menu_invite_user'),
                    $('context_menu_mute_locally'),
                    $('context_menu_unmute_locally'),
                    $('context_menu_kick'),
                    $('context_menu_mute_global'),
                    $('context_menu_unmute_global'),
                    $('context_menu_ban_user'),
                    $('context_menu_unban_user'),
                    $('context_menu_ipban'));
  if (typeof(UserList)=='object') {
    urec=UserList.getRecord(user_id);
  }
  if (urec!=null) {
    online_status=urec.getOnlineStatus();
    muted_locally=urec.getMutedLocally();
    global_muted=urec.getGlobalMuted();
    banned=urec.getBanned();
    nickname=urec.getNickname();
  } else {
    nickname=user_nickname;
  }
  $('context_menu_cmd_say').style.display='none';
  $('context_menu_cmd_whisper').style.display='none';
  if ($('user_options_box').style.display=='none' && typeof(user_id)!='undefined') {
    disableSelection();
    $('user_options_box').targetUserId=user_id;
    if (typeof($('context_menu_mute_locally').onmouseover_)=='undefined') {
      for (var i=0; i<tmp.length; i++) {
        if (tmp[i]) {
          tmp[i].onmouseover_=tmp[i].onmouseover;
          tmp[i].onmouseout_=tmp[i].onmouseout;
          tmp[i].onclick_=tmp[i].onclick;
        }
      }
    }
    if (currentUserId==user_id) {
      for (var i=0; i<tmp.length; i++) {
        if (tmp[i]) {
          setCssClass(tmp[i], '.context_menu_table_disabled_row');
          tmp[i].onmouseover=function() { return false; }
          tmp[i].onmouseout=function() { return false; }
          tmp[i].onclick=function() { return false; }
        }
      }
    } else {
      for (var i=0; i<tmp.length; i++) {
        if (tmp[i]) {
          setCssClass(tmp[i], '.context_menu_table_row');
          tmp[i].onmouseover=tmp[i].onmouseover_;
          tmp[i].onmouseout=tmp[i].onmouseout_;
          tmp[i].onclick=tmp[i].onclick_;
        }
      }
      if (true==muted_locally) {
        // User is locally muted
        setCssClass($('context_menu_mute_locally'), '.context_menu_table_disabled_row');
        setCssClass($('context_menu_unmute_locally'), '.context_menu_table_row');
        $('context_menu_mute_locally').onmouseover=function() { return false; }
        $('context_menu_mute_locally').onmouseout=function() { return false; }
        $('context_menu_mute_locally').onclick=function() { return false; }
        $('context_menu_unmute_locally').onmouseover=$('context_menu_unmute_locally').onmouseover_;
        $('context_menu_unmute_locally').onmouseout=$('context_menu_unmute_locally').onmouseout_;
        $('context_menu_unmute_locally').onclick=$('context_menu_unmute_locally').onclick_;
      } else if (false==muted_locally) {
        // User is not locally muted
        setCssClass($('context_menu_mute_locally'), '.context_menu_table_row');
        setCssClass($('context_menu_unmute_locally'), '.context_menu_table_disabled_row');
        $('context_menu_unmute_locally').onmouseover=function() { return false; }
        $('context_menu_unmute_locally').onmouseout=function() { return false; }
        $('context_menu_unmute_locally').onclick=function() { return false; }
        $('context_menu_mute_locally').onmouseover=$('context_menu_mute_locally').onmouseover_;
        $('context_menu_mute_locally').onmouseout=$('context_menu_mute_locally').onmouseout_;
        $('context_menu_mute_locally').onclick=$('context_menu_mute_locally').onclick_;
      } else {
        setCssClass($('context_menu_mute_locally'), '.context_menu_table_disabled_row');
        setCssClass($('context_menu_unmute_locally'), '.context_menu_table_disabled_row');
        $('context_menu_mute_locally').onmouseover=function() { return false; }
        $('context_menu_mute_locally').onmouseout=function() { return false; }
        $('context_menu_mute_locally').onclick=function() { return false; }
        $('context_menu_unmute_locally').onmouseover=function() { return false; }
        $('context_menu_unmute_locally').onmouseout=function() { return false; }
        $('context_menu_unmute_locally').onclick=function() { return false; }
      }
      if ($('context_menu_mute_global')) {
        if (true==global_muted) {
          // User is global muted
          setCssClass($('context_menu_mute_global'), '.context_menu_table_disabled_row');
          setCssClass($('context_menu_unmute_global'), '.context_menu_table_row');
          $('context_menu_mute_global').onmouseover=function() { return false; }
          $('context_menu_mute_global').onmouseout=function() { return false; }
          $('context_menu_mute_global').onclick=function() { return false; }
          $('context_menu_unmute_global').onmouseover=$('context_menu_unmute_global').onmouseover_;
          $('context_menu_unmute_global').onmouseout=$('context_menu_unmute_global').onmouseout_;
          $('context_menu_unmute_global').onclick=$('context_menu_unmute_global').onclick_;
        } else if (false==global_muted) {
          // User is not global muted
          setCssClass($('context_menu_mute_global'), '.context_menu_table_row');
          setCssClass($('context_menu_unmute_global'), '.context_menu_table_disabled_row');
          $('context_menu_unmute_global').onmouseover=function() { return false; }
          $('context_menu_unmute_global').onmouseout=function() { return false; }
          $('context_menu_unmute_global').onclick=function() { return false; }
          $('context_menu_mute_global').onmouseover=$('context_menu_mute_global').onmouseover_;
          $('context_menu_mute_global').onmouseout=$('context_menu_mute_global').onmouseout_;
          $('context_menu_mute_global').onclick=$('context_menu_mute_global').onclick_;
        } else {
          setCssClass($('context_menu_mute_global'), '.context_menu_table_disabled_row');
          setCssClass($('context_menu_unmute_global'), '.context_menu_table_disabled_row');
          $('context_menu_mute_global').onmouseover=function() { return false; }
          $('context_menu_mute_global').onmouseout=function() { return false; }
          $('context_menu_mute_global').onclick=function() { return false; }
          $('context_menu_unmute_global').onmouseover=function() { return false; }
          $('context_menu_unmute_global').onmouseout=function() { return false; }
          $('context_menu_unmute_global').onclick=function() { return false; }
        }
      }
      if ($('context_menu_ban_user')) {
        if (true==banned) {
          // User is banned
          setCssClass($('context_menu_ban_user'), '.context_menu_table_disabled_row');
          setCssClass($('context_menu_ipban'), '.context_menu_table_disabled_row');
          setCssClass($('context_menu_unban_user'), '.context_menu_table_row');
          $('context_menu_ban_user').onmouseover=function() { return false; }
          $('context_menu_ban_user').onmouseout=function() { return false; }
          $('context_menu_ban_user').onclick=function() { return false; }
          $('context_menu_ipban').onmouseover=function() { return false; }
          $('context_menu_ipban').onmouseout=function() { return false; }
          $('context_menu_ipban').onclick=function() { return false; }
          $('context_menu_unban_user').onmouseover=$('context_menu_unban_user').onmouseover_;
          $('context_menu_unban_user').onmouseout=$('context_menu_unban_user').onmouseout_;
          $('context_menu_unban_user').onclick=$('context_menu_unban_user').onclick_;
        } else if (false==banned) {
          // User is not banned
          setCssClass($('context_menu_ban_user'), '.context_menu_table_row');
          setCssClass($('context_menu_ipban'), '.context_menu_table_row');
          setCssClass($('context_menu_unban_user'), '.context_menu_table_disabled_row');
          $('context_menu_unban_user').onmouseover=function() { return false; }
          $('context_menu_unban_user').onmouseout=function() { return false; }
          $('context_menu_unban_user').onclick=function() { return false; }
          $('context_menu_ban_user').onmouseover=$('context_menu_ban_user').onmouseover_;
          $('context_menu_ban_user').onmouseout=$('context_menu_ban_user').onmouseout_;
          $('context_menu_ban_user').onclick=$('context_menu_ban_user').onclick_;
          $('context_menu_ipban').onmouseover=$('context_menu_ipban').onmouseover_;
          $('context_menu_ipban').onmouseout=$('context_menu_ipban').onmouseout_;
          $('context_menu_ipban').onclick=$('context_menu_ipban').onclick_;
        } else {
          setCssClass($('context_menu_ban_user'), '.context_menu_table_disabled_row');
          setCssClass($('context_menu_ipban'), '.context_menu_table_disabled_row');
          setCssClass($('context_menu_unban_user'), '.context_menu_table_disabled_row');
          $('context_menu_ban_user').onmouseover=function() { return false; }
          $('context_menu_ban_user').onmouseout=function() { return false; }
          $('context_menu_ban_user').onclick=function() { return false; }
          $('context_menu_unban_user').onmouseover=function() { return false; }
          $('context_menu_unban_user').onmouseout=function() { return false; }
          $('context_menu_unban_user').onclick=function() { return false; }
          $('context_menu_ipban').onmouseover=function() { return false; }
          $('context_menu_ipban').onmouseout=function() { return false; }
          $('context_menu_ipban').onclick=function() { return false; }
        }
      }
      if (online_status==0) {
        // User is offline
        setCssClass($('context_menu_invite_user'), '.context_menu_table_disabled_row');
        $('context_menu_invite_user').onmouseover=function() { return false; }
        $('context_menu_invite_user').onmouseout=function() { return false; }
        $('context_menu_invite_user').onclick=function() { return false; }
        if ($('context_menu_ipban')) {
          setCssClass($('context_menu_ipban'), '.context_menu_table_disabled_row');
          $('context_menu_ipban').onmouseover=function() { return false; }
          $('context_menu_ipban').onmouseout=function() { return false; }
          $('context_menu_ipban').onclick=function() { return false; }
        }
        if ($('context_menu_kick')) {
          setCssClass($('context_menu_kick'), '.context_menu_table_disabled_row');
          $('context_menu_kick').onmouseover=function() { return false; }
          $('context_menu_kick').onmouseout=function() { return false; }
          $('context_menu_kick').onclick=function() { return false; }
        }
        if ($('context_menu_client_info')) {
          setCssClass($('context_menu_client_info'), '.context_menu_table_disabled_row');
          $('context_menu_client_info').onmouseover=function() { return false; }
          $('context_menu_client_info').onmouseout=function() { return false; }
          $('context_menu_client_info').onclick=function() { return false; }
        }
      } else {
        // User is online
        if (typeof(openPMbox)!='undefined' && $('main_input_textarea')!=null && currentUserId!=urec.ID) {
          $('context_menu_cmd_say').style.display='';
          $('context_menu_cmd_whisper').style.display='';
        }
      }
    }
    if (online_status==0 || typeof(openPMbox)=='undefined' && (typeof(window.opener)=='undefined' || window.opener==null || typeof(window.opener.openPMbox)=='undefined')) {
      setCssClass($('context_menu_send_pm'), '.context_menu_table_disabled_row');
      setCssClass($('context_menu_send_pm'), '.context_menu_table_disabled_row');
      $('context_menu_send_pm').onmouseover=function() { return false; }
      $('context_menu_send_pm').onmouseout=function() { return false; }
      $('context_menu_send_pm').onclick=function() { return false; }
      $('context_menu_send_pm').onmouseover=function() { return false; }
      $('context_menu_send_pm').onmouseout=function() { return false; }
      $('context_menu_send_pm').onclick=function() { return false; }
    }
    if (typeof(currentRoomID)!='number' || currentRoomID==0) {
      setCssClass($('context_menu_invite_user'), '.context_menu_table_disabled_row');
      $('context_menu_invite_user').onmouseover=function() { return false; }
      $('context_menu_invite_user').onmouseout=function() { return false; }
      $('context_menu_invite_user').onclick=function() { return false; }
    }

    $('user_options_box_header').innerHTML=coloredToPlain(nickname, true);

    document.onclick_original=document.onclick;
    document.onkeypress_original=document.onkeypress;

    $('user_options_box').style.display='';
    winWidth=getWinWidth();
    winHeight=getWinHeight();
    if ($('user_options_box').scrollHeight+mouseY+5>winHeight) {
      $('user_options_box').style.top=(winHeight-$('user_options_box').scrollHeight-5)+'px';
    } else {
      $('user_options_box').style.top=mouseY+'px';
    }
    if ($('user_options_box').scrollWidth+mouseX+5>winWidth) {
      $('user_options_box').style.left=(winWidth-$('user_options_box').scrollWidth-5)+'px';
    } else {
      $('user_options_box').style.left=mouseX+'px';
    }
    setTimeout('document.onclick=function() { hideUserOptionsBox() }', 10);
    setTimeout('document.onkeypress=function() { hideUserOptionsBox() }', 10);
/*
    $('user_options_box').style.display='none';
    setTimeout("$('user_options_box').style.display='';", 10);
*/
  }
}


/**
 * Hide user options context menu and open submenu, if needed
 * @param     int       selected_option     Selected option (see below)
 * @param     string    prompt_response     Optional. Response from prompt() box, if needed.
 * @param     string    prompt_response2    Optional. Response from second prompt() box, if needed.
 */
function hideUserOptionsBox(selected_option, prompt_response, prompt_response2) {
  var user_id=$('user_options_box').targetUserId;
  var reason='';
  var duration='';
  var urec=UserList.getRecord(user_id);
  var callback_needed=false;
  enableSelection();
  document.onclick=document.onclick_original;
  document.onkeypress=document.onkeypress_original
  $('user_options_box').style.display='none';
  if (user_id && typeof(selected_option)!='undefined') {
    switch (selected_option) {

      case 1  : // Show user profile
        showUserProfile(user_id);
        callback_needed=true;
      break;

      case 2  : // Send user a PM
        if (typeof(openPMbox)!='undefined') {
          // Using current window
          openPMbox(user_id);
          callback_needed=true;
        } else if (typeof(window.opener)!='undefined' && window.opener!=null && typeof(window.opener.openPMbox)!='undefined') {
          // Using opener window
          window.opener.openPMbox(user_id);
          callback_needed=true;
        }
      break;

      case 3  : // Mute user locally
        muteLocally(user_id);
        if (typeof(profile_get_update)!='undefined') {
          profile_get_update();
        }
        callback_needed=true;
      break;

      case 4  : // Unmute user locally
        unMuteLocally(user_id);
        if (typeof(profile_get_update)!='undefined') {
          profile_get_update();
        }
        callback_needed=true;
      break;

      case 5  : // Kick user
        if (typeof(prompt_response)!='string') {
          prompt(getLng('enter_reason')+': ('+getLng('optional')+')', '', 0, 0, 'hideUserOptionsBox('+selected_option+', promptboxValue)');
          return false;
        } else {
          reason=trimString(prompt_response);
          kickUser(user_id, reason);
          callback_needed=true;
        }
      break;

      case 6  : // Show client info
        showClientInfo(user_id);
        callback_needed=true;
      break;

      case 7  : // Global mute user
        if (typeof(prompt_response)!='string') {
          prompt(getLng('enter_reason')+': ('+getLng('optional')+')', '', 0, 0, 'hideUserOptionsBox('+selected_option+', promptboxValue)');
          return false;
        } else {
          reason=trimString(prompt_response);
          if (typeof(prompt_response2)!='string') {
            prompt(getLng('enter_duration'), '', 0, 0, 'hideUserOptionsBox('+selected_option+', \''+reason.split("'").join("\\'")+'\', promptboxValue)');
            return false;
          } else {
            duration=trimString(prompt_response2);
            if (duration!='' && !isDigitString(duration)) {
              alert(getLng('canceled_duration_invalid'));
              return false;
            } else {
              globalMuteUser(user_id, reason, duration);
              callback_needed=true;
            }
          }
        }
      break;

      case 8  : // Global unmute
        globalUnmuteUser(user_id);
        callback_needed=true;
      break;

      case 9  : // Ban user
        if (typeof(prompt_response)!='string') {
          prompt(getLng('enter_reason')+': ('+getLng('optional')+')', '', 0, 0, 'hideUserOptionsBox('+selected_option+', promptboxValue)');
          return false;
        } else {
          reason=trimString(prompt_response);
          if (typeof(prompt_response2)!='string') {
            prompt(getLng('enter_duration'), '', 0, 0, 'hideUserOptionsBox('+selected_option+', \''+reason.split("'").join("\\'")+'\', promptboxValue)');
            return false;
          } else {
            duration=trimString(prompt_response2);
            if (duration!='' && !isDigitString(duration)) {
              alert(getLng('canceled_duration_invalid'));
              return false;
            } else {
              banUser(user_id, reason, duration, false);
              callback_needed=true;
            }
          }
        }
      break;

      case 10 : // Ban user and his IP address
        if (urec!=null && urec.getIP()==currentIP) {
          // Client IP address equals to your current IP address
          alert(getLng('ban_canceled_ip_equals').split('[IP]').join(currentIP));
        } else {
          if (typeof(prompt_response)!='string') {
            prompt(getLng('enter_reason')+': ('+getLng('optional')+')', '', 0, 0, 'hideUserOptionsBox('+selected_option+', promptboxValue)');
            return false;
          } else {
            reason=trimString(prompt_response);
            if (typeof(prompt_response2)!='string') {
              prompt(getLng('enter_duration'), '', 0, 0, 'hideUserOptionsBox('+selected_option+', \''+reason.split("'").join("\\'")+'\', promptboxValue)');
              return false;
            } else {
              duration=trimString(prompt_response2);
              if (duration!='' && !isDigitString(duration)) {
                alert(getLng('canceled_duration_invalid'));
                return false;
              } else {
                banUser(user_id, reason, duration, true);
                callback_needed=true;
              }
            }
          }
        }
      break;

      case 11 : // Unban user
        if (urec!=null) {
          unBanUser(user_id);
          callback_needed=true;
        }
      break;

      case 12 : // Invite user
        if (urec!=null) {
          sendInvitation(user_id);
          callback_needed=false;
        }
      break;

      case 13 : // "/say" command
        if (urec!=null && $('main_input_textarea')) {
          $('main_input_textarea').value='/say "'+coloredToPlain(urec.getNickname(), false)+'" '+$('main_input_textarea').value;
          $('main_input_textarea').focus();
        }
      break;

      case 14 : // "/whisper" command
        if (urec!=null && $('main_input_textarea')) {
          $('main_input_textarea').value='/whisper "'+coloredToPlain(urec.getNickname(), false)+'" '+$('main_input_textarea').value;
          $('main_input_textarea').focus();
        }
      break;

    }
    if (true==callback_needed && CallBackContextMenuFunc!='') {
      try {
        eval(CallBackContextMenuFunc);
      } catch (e) {}
    }
  }
}

/**
 * Show user public profile
 * @param   int   user_id   User ID
 */
function showUserProfile(user_id) {
  openWindow(formlink+'?s_id='+s_id+'&inc=profile_public&user_id='+urlencode(user_id), 'profile_public_'+user_id, 600, 450, false, false, false, false, true, false, false, false, false, true);
}

/**
 * Show client info
 * @param   int   user_id   User ID
 */
function showClientInfo(user_id) {
  openWindow(formlink+'?s_id='+s_id+'&inc=client_info&user_id='+urlencode(user_id), 'client_info_'+user_id, 500, 250, false, false, false, false, true);
}

/**
 * Mute user locally
 * @param   int   user_id   Target User ID
 */
function muteLocally(user_id) {
  toggleProgressBar(true);
  setTimeout("ajaxUserOptions.sendData('_CALLBACK_muteUnmuteUserLocally()', 'POST', formlink, 'ajax=mute_unmute_locally&s_id='+urlencode(s_id)+'&target_user_id='+urlencode("+user_id+")+'&action=1', true);", 50);
}

/**
 * Unmute user locally
 * @param   int   user_id   Target User ID
 */
function unMuteLocally(user_id) {
  toggleProgressBar(true);
  setTimeout("ajaxUserOptions.sendData('_CALLBACK_muteUnmuteUserLocally()', 'POST', formlink, 'ajax=mute_unmute_locally&s_id='+urlencode(s_id)+'&target_user_id='+urlencode("+user_id+")+'&action=0', true);", 50);
}

function _CALLBACK_muteUnmuteUserLocally() {
//debug(ajaxUserOptions.getResponseString());
  var muted_users=ajaxUserOptions.data['muted_users'][0];
  var muted_ids=null;
  var urecs=null;
  if (ajaxUserOptions.status!=-1) {
    // OK
    muted_users=','+muted_users+',';
    urecs=UserList.getAllRecords();
    for (var i in urecs) {
      if (-1!=muted_users.indexOf(','+urecs[i].ID+',')) {
        urecs[i].setMutedLocally(1);
      } else {
        urecs[i].setMutedLocally(0);
      }
    }
    if (typeof(redrawUserlist)!='undefined') {
      redrawUserlist();
    }
  }
  if (typeof(window.opener)!='undefined' && window.opener!=null && typeof(window.opener.redrawUserlist)!='undefined') {
    if (status!='-1') {
      // OK
      muted_users=','+muted_users+',';
      urecs=window.opener.UserList.getAllRecords();
      for (var i in urecs) {
        if (-1!=muted_users.indexOf(','+urecs[i].ID+',')) {
          urecs[i].setMutedLocally(1);
        } else {
          urecs[i].setMutedLocally(0);
        }
      }
      window.opener.redrawUserlist();
    }
  }
  toggleProgressBar(false);
}


/**
 * Kick user
 * @param   int       id          User ID
 * @param   string    reason      Kick reason
 */
function kickUser(id, reason) {
  if (typeof(id)=='number') {
    if (typeof(reason)!='string') {
      reason='';
    }
    reason=trimString(reason).substring(0, 255);
    toggleProgressBar(true);
    setTimeout("ajaxUserOptions.sendData('_CALLBACK_kickUser()', 'POST', formlink, 'ajax=kick&s_id='+urlencode(s_id)+'&target_user_id='+urlencode("+id+")+'&reason="+urlencode(reason)+"', true);", 50);
  }
}
function _CALLBACK_kickUser() {
  if (typeof(startUpdater)!='undefined') {
    startUpdater(true);
  } else if (typeof(profile_start_update)!='undefined') {
    profile_start_update();
  }
  toggleProgressBar(false);
}

/**
 * Ban user
 * @param   int       id          User ID
 * @param   string    reason      Ban reason
 * @param   int       minutes     Ban duration in minutes, empty value means PERMANENT ban
 * @param   boolean   ip_ban      If TRUE, then IP address will be also banned
 */
function banUser(id, reason, minutes, ip_ban) {
  if (typeof(id)=='number') {
    if (typeof(reason)!='string') {
      reason='';
    }
    if (typeof(minutes)!='number') {
      minutes=stringToNumber(minutes);
    }
    reason=trimString(reason).substring(0, 255);
    if (typeof(ip_ban)=='boolean' && true==ip_ban) {
      ip_ban='1';
    } else {
      ip_ban='';
    }
    toggleProgressBar(true);
    setTimeout("ajaxUserOptions.sendData('_CALLBACK_banUser()', 'POST', formlink, 'ajax=ban&s_id='+urlencode(s_id)+'&target_user_id='+urlencode("+id+")+'&duration="+urlencode(minutes)+"&reason="+urlencode(reason)+"&ip_ban="+ip_ban+"', true);", 50);
  }
}
function _CALLBACK_banUser() {
  if (typeof(startUpdater)!='undefined') {
    startUpdater(true);
  } else if (typeof(profile_start_update)!='undefined') {
    profile_start_update();
  }
  toggleProgressBar(false);
}

/**
 * Unban user
 * @param   int       id          User ID
 * @param   string    reason      Ban reason
 * @param   int       minutes     Ban duration in minutes, empty value means PERMANENT ban
 * @param   boolean   ip_ban      If TRUE, then IP address will be also banned
 */
function unBanUser(id) {
  if (typeof(id)=='number') {
    toggleProgressBar(true);
    setTimeout("ajaxUserOptions.sendData('_CALLBACK_unBanUser()', 'POST', formlink, 'ajax=unban&s_id='+urlencode(s_id)+'&target_user_id='+urlencode("+id+"), true);", 50);
  }
}
function _CALLBACK_unBanUser() {
  if (typeof(startUpdater)!='undefined') {
    startUpdater(true);
  } else if (typeof(profile_start_update)!='undefined') {
    profile_start_update();
  }
  toggleProgressBar(false);
}

/**
 * Global mute user
 * @param   int       id          User ID
 * @param   string    reason      Mute reason
 * @param   int       minutes     Mute duration in minutes, empty value means PERMANENT mute
 */
function globalMuteUser(id, reason, minutes) {
  if (typeof(id)=='number') {
    if (typeof(reason)!='string') {
      reason='';
    }
    if (typeof(minutes)!='number') {
      minutes=stringToNumber(minutes);
    }
    reason=trimString(reason).substring(0, 255);
    toggleProgressBar(true);
    setTimeout("ajaxUserOptions.sendData('_CALLBACK_globalMuteUser()', 'POST', formlink, 'ajax=globalmute&s_id='+urlencode(s_id)+'&target_user_id='+urlencode("+id+")+'&duration="+urlencode(minutes)+"&reason="+urlencode(reason)+"&action=1', true);", 50);
  }
}
function _CALLBACK_globalMuteUser() {
//debug(ajaxUserOptions.getResponseString()); return false;
  if (typeof(startUpdater)!='undefined') {
    startUpdater(true);
  } else if (typeof(profile_start_update)!='undefined') {
    profile_start_update();
  }
  toggleProgressBar(false);
}


/**
 * Unmute global muted user
 * @param   int       id          User ID
 */
function globalUnmuteUser(id) {
  if (typeof(id)=='number') {
    toggleProgressBar(true);
    setTimeout("ajaxUserOptions.sendData('_CALLBACK_globalUnmuteUser()', 'POST', formlink, 'ajax=globalmute&s_id='+urlencode(s_id)+'&target_user_id='+urlencode("+id+")+'&action=0', true);", 50);
  }
}
function _CALLBACK_globalUnmuteUser() {
  if (typeof(startUpdater)!='undefined') {
    startUpdater(true);
  } else if (typeof(profile_start_update)!='undefined') {
    profile_start_update();
  }
  toggleProgressBar(false);
}

/**
 * Send room invitation to the user
 * @param   int   id    Target user ID
 */
function sendInvitation(id) {
  if (typeof(id)=='number' && id>0) {
    toggleProgressBar(true);
    setTimeout("ajaxUserOptions.sendData('_CALLBACK_sendInvitation("+id+")', 'POST', formlink, 'ajax=invite&s_id='+urlencode(s_id)+'&user_id='+urlencode("+id+"), true);", 50);
  }
}
function _CALLBACK_sendInvitation(user_id) {
  if (ajaxUserOptions.status==-1) {
    // Session is invalid
    window.close();
    opener.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    toggleProgressBar(false);
    alert(ajaxUserOptions.message);
  }
}
