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
 * This file contains "IRC"-Like command implementations
 */



/**
 * Look for any commands in a string and execute them. Commands will be removed from string.
 * @param   string    str   String to parse
 * @return  array   Array with message body as first element, it's type as second element and target room ID as third argument.
 */
function parseCommands(str) {
  var parsed='';
  var parts=null;
  var part='';
  var cmd_found=false;
  var parts_new=new Array();
  var part_nr=0;

  var type=3001;
  var target_room_id=null;
  var target_user_id=null;
  var privacy=null;

  var args=new Array();
  if (typeof(str)=='string' && str!='') {
    parts=str.split(' ');
    while (typeof((part=parts.shift()))=='string') {
      cmd_found=false;
      if (part!='' && part.charAt(0)=='/') {
        if (part_nr==0) {
          // Alias for "/help" command
          if (part=='/?') {
            part='/help';
          }
          // Commands that *MUST* return data to be sent to the server
          switch (part) {

            // Commands that send data to the server
            case '/clear':
            case '/say':
            case '/whisper':
              eval('cmd_found=typeof(_cmd_'+part.substring(1)+')==\'function\';');
              if (true==cmd_found) {
                eval('args=_cmd_'+part.substring(1)+'(parts)');
                parts=new Array();
                if (typeof(args['type'])!='undefined') {
                  type=args['type'];
                }
                if (typeof(args['body'])!='undefined') {
                  parts_new=new Array(args['body']);
                }
                if (typeof(args['target_room_id'])!='undefined') {
                  target_room_id=args['target_room_id'];
                }
                if (typeof(args['target_user_id'])!='undefined') {
                  target_user_id=args['target_user_id'];
                }
                if (typeof(args['privacy'])!='undefined' && args['privacy']!=null) {
                  privacy=args['privacy'];
                }
                parts=new Array();
              }
            break;

            // Commands that *MUST NOT* return data to be sent to the server
            case '/admin':
            case '/ban':
            case '/exit':
            case '/exitroom':
            case '/help':
            case '/ignore':
            case '/ipban':
            case '/logout':
            case '/kick':
            case '/mute':
            case '/quit':
            case '/show':
            case '/unignore':
            case '/unmute':
              if (isAlphaNumString(part.substring(1))) {
                eval('cmd_found=typeof(_cmd_'+part.substring(1)+')==\'function\';');
              }
              if (true==cmd_found) {
                eval('args=_cmd_'+part.substring(1)+'(parts)');
                parts=new Array();
              }
            break;

          }
        }
        // Commands that can be used in any part of the line
        if (false==cmd_found && part_nr>0) {
          switch (part) {
            // Commands that sends data to the server
            // Commands that *NOT* sends data to the server
          }
        }
      }
      part_nr++;
      if (false==cmd_found) {
        // Not a command
        parts_new[parts_new.length]=part;
      }
    }
    parsed=parts_new.join(' ');
  }
  return Array(parsed, type, target_room_id, target_user_id, privacy);
}







/**
 * Execute "/_" command
 * @param   array   args    Command arguments
 */
function _cmd__(args) {
  var args_str='';
  if (typeof(args)=='object' && args && args.join && args.length) {
    args_str=trimString(args.join(' '));
  }
  if (args_str!='') {
    // There are some arguments
  } else {
    // No arguments
  }
}


/**
 * Execute "/admin" command
 * @param   array   args    Command arguments
 */
function _cmd_admin(args) {
  openAdminWindow();
}

/**
 * Execute "/ban" command
 * @param   array   args    Command arguments
 */
function _cmd_ban(args) {
  var args_str='';
  var return_data=new Array();
  var ban_duration='';
  var ban_reason='';
  if (typeof(args)=='object' && args && args.join && args.length) {
    args_str=trimString(args.join(' '));
  }
  if (args_str!='') {
    // There are some arguments
    var urec=null;
    var first_index=args_str.indexOf('"');
    var last_index=args_str.lastIndexOf('"');
    if (first_index>=0 && last_index>first_index) {
      urec=UserList.findRecordByNicknameInString(args_str, true, true);
      if (typeof(urec)=='object' && urec) {
        var nickname_plain=coloredToPlain(urec.getNickname());
        var parts=args_str.split('"'+nickname_plain+'"');
        args_str=parts.shift()+parts.join('"'+nickname_plain+'"');
        args=args_str.split(' ');
      }
    }
    if (urec==null) {
      var nickname_plain=args.shift();
      if (null==(urec=UserList.findRecordByNickname(nickname_plain, false, true))) { // Strict username search
        if (!isDigitString(nickname_plain) || null==(urec=UserList.getRecord(nickname_plain))) { // ID search
          urec=UserList.findRecordByNickname(nickname_plain, false, false); // Transitional username search
        }
      }
    }
    if (urec==null) {
      // No users found
      displayMessage(null, '/ban: '+getLng('nickname_matches_empty').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (urec==false) {
      // More than one user found
      displayMessage(null, '/ban: '+getLng('nickname_matches_multiple').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (typeof(urec)=='object' && urec) {
      // Exactly one user found
      if (urec.ID==currentUserId) {
        displayMessage(null, '/ban: '+getLng('cannot_apply_cmd_to_yourself'), 'font-weight: bold', false, 0);
      } else {
        ban_duration=args.length>0? args.shift() : '';
        ban_reason=args.join(' ');
        if (!isDigitString(ban_duration)) {
          ban_reason=ban_duration+' '+ban_reason;
          ban_duration='0';
        }
        banUser(urec.ID, ban_reason, stringToNumber(ban_duration), false);
      }
    }
  } else {
    // Invalid usage
    showCommandList('ban');
  }
  return return_data;
}

/**
 * Execute "/clear" command
 * @param   array   args    Command arguments
 * @return  array   Array with parsed data
 */
function _cmd_clear(args) {
  var args_str='';
  var return_data=new Array();
  if (typeof(args)=='object' && args && args.join && args.length) {
    args_str=trimString(args.join(' '));
  }
  if (args_str!='') {
    // There are some arguments
    switch (args_str) {
      case 'room':
        // Clear all room users' browsers
        return_data['type']=10001;
        return_data['body']='-';
        return_data['target_room_id']=currentRoomID;
      break;
      case 'all':
        // Clear chat messages frames by all users in all rooms
        return_data['type']=10001;
        return_data['body']='-';
        return_data['target_room_id']=0;
      break;
      default:
        // Invalid usage
        showCommandList('clear');
      break;
    }
  } else {
    // No arguments
    // Clear locally
    flushMessagesArea();
  }
  return return_data;
}

/**
 * Execute "/exit" command
 * @param   array   args    Command arguments
 */
function _cmd_exit(args) {
  _cmd_logout(args);
}

/**
 * Execute "/exitroom" command
 * @param   array   args    Command arguments
 */
function _cmd_exitroom(args) {
  confirm(getLng('sure_to_leave_room'), 0, 0, 'leaveRoom()');
}

/**
 * Execute "/mute" command
 * @param   array   args    Command arguments
 */
function _cmd_mute(args) {
  var args_str='';
  var return_data=new Array();
  var mute_duration='';
  var mute_reason='';
  if (typeof(args)=='object' && args && args.join && args.length) {
    args_str=trimString(args.join(' '));
  }
  if (args_str!='') {
    // There are some arguments
    var urec=null;
    var first_index=args_str.indexOf('"');
    var last_index=args_str.lastIndexOf('"');
    if (first_index>=0 && last_index>first_index) {
      urec=UserList.findRecordByNicknameInString(args_str, true, true);
      if (typeof(urec)=='object' && urec) {
        var nickname_plain=coloredToPlain(urec.getNickname());
        var parts=args_str.split('"'+nickname_plain+'"');
        args_str=parts.shift()+parts.join('"'+nickname_plain+'"');
        args=args_str.split(' ');
      }
    }
    if (urec==null) {
      var nickname_plain=args.shift();
      if (null==(urec=UserList.findRecordByNickname(nickname_plain, false, true))) { // Strict username search
        if (!isDigitString(nickname_plain) || null==(urec=UserList.getRecord(nickname_plain))) { // ID search
          urec=UserList.findRecordByNickname(nickname_plain, false, false); // Transitional username search
        }
      }
    }
    if (urec==null) {
      // No users found
      displayMessage(null, '/mute: '+getLng('nickname_matches_empty').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (urec==false) {
      // More than one user found
      displayMessage(null, '/mute: '+getLng('nickname_matches_multiple').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (typeof(urec)=='object' && urec) {
      // Exactly one user found
      if (urec.ID==currentUserId) {
        displayMessage(null, '/mute: '+getLng('cannot_apply_cmd_to_yourself'), 'font-weight: bold', false, 0);
      } else {
        mute_duration=args.length>0? args.shift() : '';
        mute_reason=args.join(' ');
        if (!isDigitString(mute_duration)) {
          mute_reason=mute_duration+' '+mute_reason;
          mute_duration='0';
        }
        globalMuteUser(urec.ID, mute_reason, mute_duration);
      }
    }
  } else {
    // Invalid usage
    showCommandList('mute');
  }
  return return_data;
}

/**
 * Execute "/unmute" command
 * @param   array   args    Command arguments
 */
function _cmd_unmute(args) {
  var args_str='';
  var return_data=new Array();
  var mute_duration='';
  var mute_reason='';
  if (typeof(args)=='object' && args && args.join && args.length) {
    args_str=trimString(args.join(' '));
  }
  if (args_str!='') {
    // There are some arguments
    var urec=null;
    var first_index=args_str.indexOf('"');
    var last_index=args_str.lastIndexOf('"');
    if (first_index>=0 && last_index>first_index) {
      urec=UserList.findRecordByNicknameInString(args_str, true, true);
      if (typeof(urec)=='object' && urec) {
        var nickname_plain=coloredToPlain(urec.getNickname());
        var parts=args_str.split('"'+nickname_plain+'"');
        args_str=parts.shift()+parts.join('"'+nickname_plain+'"');
        args=args_str.split(' ');
      }
    }
    if (urec==null) {
      var nickname_plain=args.shift();
      if (null==(urec=UserList.findRecordByNickname(nickname_plain, false, true))) { // Strict username search
        if (!isDigitString(nickname_plain) || null==(urec=UserList.getRecord(nickname_plain))) { // ID search
          urec=UserList.findRecordByNickname(nickname_plain, false, false); // Transitional username search
        }
      }
    }
    if (urec==null) {
      // No users found
      displayMessage(null, '/unmute: '+getLng('nickname_matches_empty').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (urec==false) {
      // More than one user found
      displayMessage(null, '/unmute: '+getLng('nickname_matches_multiple').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (typeof(urec)=='object' && urec) {
      // Exactly one user found
      if (urec.ID==currentUserId) {
        displayMessage(null, '/unmute: '+getLng('cannot_apply_cmd_to_yourself'), 'font-weight: bold', false, 0);
      } else {
        globalUnmuteUser(urec.ID);
      }
    }
  } else {
    // Invalid usage
    showCommandList('unmute');
  }
  return return_data;
}

/**
 * Execute "/ipban" command
 * @param   array   args    Command arguments
 */
function _cmd_ipban(args) {
  var args_str='';
  var return_data=new Array();
  var ban_duration='';
  var ban_reason='';
  if (typeof(args)=='object' && args && args.join && args.length) {
    args_str=trimString(args.join(' '));
  }
  if (args_str!='') {
    // There are some arguments
    var urec=null;
    var first_index=args_str.indexOf('"');
    var last_index=args_str.lastIndexOf('"');
    if (first_index>=0 && last_index>first_index) {
      urec=UserList.findRecordByNicknameInString(args_str, true, true);
      if (typeof(urec)=='object' && urec) {
        var nickname_plain=coloredToPlain(urec.getNickname());
        var parts=args_str.split('"'+nickname_plain+'"');
        args_str=parts.shift()+parts.join('"'+nickname_plain+'"');
        args=args_str.split(' ');
      }
    }
    if (urec==null) {
      var nickname_plain=args.shift();
      if (null==(urec=UserList.findRecordByNickname(nickname_plain, false, true))) { // Strict username search
        if (!isDigitString(nickname_plain) || null==(urec=UserList.getRecord(nickname_plain))) { // ID search
          urec=UserList.findRecordByNickname(nickname_plain, false, false); // Transitional username search
        }
      }
    }
    if (urec==null) {
      // No users found
      displayMessage(null, '/ipban: '+getLng('nickname_matches_empty').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (urec==false) {
      // More than one user found
      displayMessage(null, '/ipban: '+getLng('nickname_matches_multiple').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (typeof(urec)=='object' && urec) {
      // Exactly one user found
      if (urec.ID==currentUserId) {
        displayMessage(null, '/ipban: '+getLng('cannot_apply_cmd_to_yourself'), 'font-weight: bold', false, 0);
      } else {
        ban_duration=args.length>0? args.shift() : '';
        ban_reason=args.join(' ');
        if (!isDigitString(ban_duration)) {
          ban_reason=ban_duration+' '+ban_reason;
          ban_duration='0';
        }
        banUser(urec.ID, ban_reason, stringToNumber(ban_duration), true);
      }
    }
  } else {
    // Invalid usage
    showCommandList('ipban');
  }
  return return_data;
}

/**
 * Execute "/help" command
 * @param   array   args    Command arguments
 */
function _cmd_help(args) {
  var args_str='';
  var cmd_found=false;
  if (typeof(args)=='object' && args && args.join && args.length) {
    args_str=trimString(args.join(' '));
  }
  if (args_str.charAt(0)=='/') {
    args_str=args_str.substring(1);
  }
  args_str=trimString(args_str);
  if (args_str!='') {
    // There are some arguments
    if (isAlphaNumString(args_str)) {
      eval('cmd_found=typeof(_cmd_'+args_str+')==\'function\';');
    }
    if (true==cmd_found) {
      // Help to single command
      showCommandList(args_str);
    } else {
      // Command not found
      displayMessage(null, '/help '+args[0]+': '+getLng('command_not_found').split('[COMMAND]').join(args[0]), 'font-weight: bold', false, 0);
    }
  } else {
    // No arguments
    // Show all commands list
    showCommandList(null, true);
  }
}

/**
 * Execute "/kick" command
 * @param   array   args    Command arguments
 */
function _cmd_kick(args) {
  var args_str='';
  var return_data=new Array();
  if (typeof(args)=='object' && args && args.join && args.length) {
    args_str=trimString(args.join(' '));
  }
  if (args_str!='') {
    // There are some arguments
    var urec=null;
    var first_index=args_str.indexOf('"');
    var last_index=args_str.lastIndexOf('"');
    if (first_index>=0 && last_index>first_index) {
      urec=UserList.findRecordByNicknameInString(args_str, true, true);
      if (typeof(urec)=='object' && urec) {
        var nickname_plain=coloredToPlain(urec.getNickname());
        var parts=args_str.split('"'+nickname_plain+'"');
        args_str=parts.shift()+parts.join('"'+nickname_plain+'"');
        args=args_str.split(' ');
      }
    }
    if (urec==null) {
      var nickname_plain=args.shift();
      if (null==(urec=UserList.findRecordByNickname(nickname_plain, false, true))) { // Strict username search
        if (!isDigitString(nickname_plain) || null==(urec=UserList.getRecord(nickname_plain))) { // ID search
          urec=UserList.findRecordByNickname(nickname_plain, false, false); // Transitional username search
        }
      }
    }
    if (urec==null) {
      // No users found
      displayMessage(null, '/kick: '+getLng('nickname_matches_empty').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (urec==false) {
      // More than one user found
      displayMessage(null, '/kick: '+getLng('nickname_matches_multiple').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (typeof(urec)=='object' && urec) {
      // Exactly one user found
      if (urec.ID==currentUserId) {
        displayMessage(null, '/kick: '+getLng('cannot_apply_cmd_to_yourself'), 'font-weight: bold', false, 0);
      } else {
        kickUser(urec.ID, args.length>0? args.join(' ') : '');
      }
    }
  } else {
    // Invalid usage
    showCommandList('kick');
  }
  return return_data;
}

/**
 * Execute "/logout" command
 * @param   array   args    Command arguments
 */
function _cmd_logout(args) {
  confirm(getLng('sure_to_log_out'), 0, 0, 'logOut()');
}

/**
 * Execute "/ignore" command
 * @param   array   args    Command arguments
 */
function _cmd_ignore(args) {
  var args_str='';
  var return_data=new Array();
  if (typeof(args)=='object' && args && args.join && args.length) {
    args_str=trimString(args.join(' '));
  }
  if (args_str!='') {
    // There are some arguments
    var urec=null;
    var first_index=args_str.indexOf('"');
    var last_index=args_str.lastIndexOf('"');
    if (first_index>=0 && last_index>first_index) {
      urec=UserList.findRecordByNicknameInString(args_str, true, true);
      if (typeof(urec)=='object' && urec) {
        var nickname_plain=coloredToPlain(urec.getNickname());
        var parts=args_str.split('"'+nickname_plain+'"');
        args_str=parts.shift()+parts.join('"'+nickname_plain+'"');
        args=args_str.split(' ');
      }
    }
    if (urec==null) {
      var nickname_plain=args.shift();
      if (null==(urec=UserList.findRecordByNickname(nickname_plain, false, true))) { // Strict username search
        if (!isDigitString(nickname_plain) || null==(urec=UserList.getRecord(nickname_plain))) { // ID search
          urec=UserList.findRecordByNickname(nickname_plain, false, false); // Transitional username search
        }
      }
    }
    if (urec==null) {
      // No users found
      displayMessage(null, '/ignore: '+getLng('nickname_matches_empty').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (urec==false) {
      // More than one user found
      displayMessage(null, '/ignore: '+getLng('nickname_matches_multiple').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (typeof(urec)=='object' && urec) {
      // Exactly one user found
      if (urec.ID==currentUserId) {
        displayMessage(null, '/ignore: '+getLng('cannot_apply_cmd_to_yourself'), 'font-weight: bold', false, 0);
      } else {
        muteLocally(urec.ID);
      }
    }
  } else {
    // Invalid usage
    showCommandList('ignore');
  }
  return return_data;
}

/**
 * Execute "/quit" command
 * @param   array   args    Command arguments
 */
function _cmd_quit(args) {
  _cmd_logout(args);
}

/**
 * Execute "/say" command
 * @param   array   args    Command arguments
 */
function _cmd_say(args) {
  var args_str='';
  var return_data=new Array();
  var msg_body='';
  if (typeof(args)=='object' && args && args.join && args.length) {
    args_str=trimString(args.join(' '));
  }
  if (args_str!='' && args.length>=2) {
    // There are some arguments
    var urec=null;
    var first_index=args_str.indexOf('"');
    var last_index=args_str.lastIndexOf('"');
    if (first_index>=0 && last_index>first_index) {
      urec=UserList.findRecordByNicknameInString(args_str, true, true);
      if (typeof(urec)=='object' && urec) {
        var nickname_plain=coloredToPlain(urec.getNickname());
        var parts=args_str.split('"'+nickname_plain+'"');
        args_str=parts.shift()+parts.join('"'+nickname_plain+'"');
        args=args_str.split(' ');
      }
    }
    if (urec==null) {
      var nickname_plain=args.shift();
      if (null==(urec=UserList.findRecordByNickname(nickname_plain, false, true))) { // Strict username search
        if (!isDigitString(nickname_plain) || null==(urec=UserList.getRecord(nickname_plain))) { // ID search
          urec=UserList.findRecordByNickname(nickname_plain, false, false); // Transitional username search
        }
      }
    }
    if (urec==null) {
      // No users found
      displayMessage(null, '/say: '+getLng('nickname_matches_empty').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (urec==false) {
      // More than one user found
      displayMessage(null, '/say: '+getLng('nickname_matches_multiple').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (typeof(urec)=='object' && urec) {
      // Exactly one user found
      if (urec.ID==currentUserId) {
        displayMessage(null, '/say: '+getLng('cannot_apply_cmd_to_yourself'), 'font-weight: bold', false, 0);
      } else {
        // Make the message
        return_data['type']=3001;
        return_data['body']=args.join(' ');
        return_data['target_room_id']=currentRoomID;
        return_data['target_user_id']=urec.ID;
        return_data['privacy']=0;
      }
    }
  } else {
    // Invalid usage
    showCommandList('say');
  }
  return return_data;
}

/**
 * Execute "/show" command
 * @param   array   args    Command arguments
 */
function _cmd_show(args) {
  var args_str='';
  if (typeof(args)=='object' && args && args.join && args.length) {
    args_str=trimString(args.join(' '));
  }
  if (args_str!='') {
    // There are some arguments
    if (isDigitString(args_str)) {
      args_str=stringToNumber(args_str);
      if (args_str>0) {
        startUpdater(true, false, false, args_str, true);
      }
    }
  }
}

/**
 * Execute "/unignore" command
 * @param   array   args    Command arguments
 */
function _cmd_unignore(args) {
  var args_str='';
  var return_data=new Array();
  if (typeof(args)=='object' && args && args.join && args.length) {
    args_str=trimString(args.join(' '));
  }
  if (args_str!='') {
    // There are some arguments
    var urec=null;
    var first_index=args_str.indexOf('"');
    var last_index=args_str.lastIndexOf('"');
    if (first_index>=0 && last_index>first_index) {
      urec=UserList.findRecordByNicknameInString(args_str, true, true);
      if (typeof(urec)=='object' && urec) {
        var nickname_plain=coloredToPlain(urec.getNickname());
        var parts=args_str.split('"'+nickname_plain+'"');
        args_str=parts.shift()+parts.join('"'+nickname_plain+'"');
        args=args_str.split(' ');
      }
    }
    if (urec==null) {
      var nickname_plain=args.shift();
      if (null==(urec=UserList.findRecordByNickname(nickname_plain, false, true))) { // Strict username search
        if (!isDigitString(nickname_plain) || null==(urec=UserList.getRecord(nickname_plain))) { // ID search
          urec=UserList.findRecordByNickname(nickname_plain, false, false); // Transitional username search
        }
      }
    }
    if (urec==null) {
      // No users found
      displayMessage(null, '/unignore: '+getLng('nickname_matches_empty').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (urec==false) {
      // More than one user found
      displayMessage(null, '/unignore: '+getLng('nickname_matches_multiple').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (typeof(urec)=='object' && urec) {
      // Exactly one user found
      if (urec.ID==currentUserId) {
        displayMessage(null, '/unignore: '+getLng('cannot_apply_cmd_to_yourself'), 'font-weight: bold', false, 0);
      } else {
        unMuteLocally(urec.ID);
      }
    }
  } else {
    // Invalid usage
    showCommandList('unignore');
  }
  return return_data;
}

/**
 * Execute "/whisper" command
 * @param   array   args    Command arguments
 */
function _cmd_whisper(args) {
  var args_str='';
  var return_data=new Array();
  var msg_body='';
  if (typeof(args)=='object' && args && args.join && args.length) {
    args_str=trimString(args.join(' '));
  }
  if (args_str!='' && args.length>=2) {
    // There are some arguments
    var urec=null;

    var first_index=args_str.indexOf('"');
    var last_index=args_str.lastIndexOf('"');
    if (first_index>=0 && last_index>first_index) {
      urec=UserList.findRecordByNicknameInString(args_str, true, true);
      if (typeof(urec)=='object' && urec) {
        var nickname_plain=coloredToPlain(urec.getNickname());
        var parts=args_str.split('"'+nickname_plain+'"');
        args_str=parts.shift()+parts.join('"'+nickname_plain+'"');
        args=args_str.split(' ');
      }
    }
    if (urec==null) {
      var nickname_plain=args.shift();
      if (null==(urec=UserList.findRecordByNickname(nickname_plain, false, true))) { // Strict username search
        if (!isDigitString(nickname_plain) || null==(urec=UserList.getRecord(nickname_plain))) { // ID search
          urec=UserList.findRecordByNickname(nickname_plain, false, false); // Transitional username search
        }
      }
    }
    if (urec==null) {
      // No users found
      displayMessage(null, '/whisper: '+getLng('nickname_matches_empty').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (urec==false) {
      // More than one user found
      displayMessage(null, '/whisper: '+getLng('nickname_matches_multiple').split('[NICKNAME]').join(nickname_plain), 'font-weight: bold', false, 0);
    } else if (typeof(urec)=='object' && urec) {
      // Exactly one user found
      if (urec.ID==currentUserId) {
        displayMessage(null, '/whisper: '+getLng('cannot_apply_cmd_to_yourself'), 'font-weight: bold', false, 0);
      } else {
        // Make the message
        return_data['type']=3001;
        return_data['body']=args.join(' ');
        return_data['target_room_id']=currentRoomID;
        return_data['target_user_id']=urec.ID;
        return_data['privacy']=1;
      }
    }
  } else {
    // Invalid usage
    showCommandList('whisper');
  }
  return return_data;
}






/**
 * Display the list of all available commands or a single command
 * @param   string    help_cmd      Command (optional)
 * @param   boolean   short_form    If TRUE, then short description will be displayed (command interface + first sentence). Default: FALSE.
 */
function showCommandList(help_cmd, short_form) {
  var help_spans=$$('SPAN', $('cmd_help_records'));
  var cmd='';
  var cmd_found=false;
  var help_text_lines=null;
  var prepend_text='';
  if (typeof(help_cmd)!='string') {
    help_cmd='';
    prepend_text=htmlspecialchars(getLng('help_hint'));
  }
  if (help_spans.length>0) {
    displayMessage(null, ' ', null, false, 0);
    displayMessage(null, prepend_text, null, false, 0);
    for (var i=0; i<help_spans.length; i++) {
      cmd=help_spans[i].id.substring(16);
      if (cmd!='') {
        eval('cmd_found=typeof(_cmd_'+cmd+')==\'function\';');
        if (true==cmd_found && (help_cmd=='' || help_cmd==cmd)) {
          help_text_lines=help_spans[i].innerHTML.split('@BR@');
          displayMessage(null, help_text_lines.shift(), 'font-weight: bold', false, 0);
          if (help_text_lines.length>0 && (typeof(short_form)!='boolean' || short_form==false)) {
            displayMessage(null, help_text_lines.join(' '), null, false, 0);
          }
        }
      }
    }
    displayMessage(null, ' ', null, false, 0);
  }
}