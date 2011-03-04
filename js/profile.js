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
 * ID of profile user
 * @var int
 */
var profileUserId=currentUserId;

/**
 * ID of currently active user language
 * @var int
 */
var profileUserLanguageId=0;

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
 * Flag: if TRUE, then avatar gallery is allowed
 * @var boolean
 */
var avatarGalleryAllowed=false;

/**
 * Flag: if TRUE, then language selection will be displayed
 * @var boolean
 */
var allowLanguageSelection=false;

/**
 * Flag: if TRUE, then users are allowed to delete own account
 * @var boolean
 */
var allowDeleteOwnAccount=false;

/**
 * Userdata
 * @var array
 */
var UserData=new Array();

/**
 * Userdata profile custom fields referenced by their IDs
 * @var array
 */
var UserDataCustomFieldsById=new Array();

/**
 * Currently active profile page
 * @var object
 */
var CurrentProfilePage=null;

/**
 * Array with tasks for saveProfileChanges() function
 * @var array
 */
var saveProfileChangesTasks=new Array();

/**
 * Array with result AJAX messages of saveProfileChanges()
 * @var array
 */
var saveProfileChangesMessages=new Array();

/**
 * Flag. TRUE when after saveProfileChanges() execution window reload required
 * @var boolean
 */
var saveProfileChangesReload=false;

/**
 * Default nickname color
 * @var string
 */
var defaultNicknameColor='';

/**
 * Total number of nicknames
 * @var int
 */
var nicknamesCount=0;



/**
 * Initialize profile data
 * @param   int       nickname_length_min_          Minimum allowed nickname length, chars
 * @param   int       nickname_length_max_          Maximum allowed nickname length, chars
 * @param   string    default_nickname_color        Default nickname color
 * @param   int       avatars_max_count             How many avatars are allowed?
 * @param   int       nicknames_max_count           How many nicknames are allowed?
 * @param   boolean   profile_user_id               User ID
 * @param   boolean   avatar_gallery_allowed        Flag: if TRUE, then avatar gallery is allowed
 * @param   boolean   allow_language_selection      Flag: if TRUE, then language selecton is allowed
 * @param   boolean   allow_delete_own_account      Flag: if TRUE, then users are allowed to delete their account
 */
function initProfile(
                     nickname_length_min_,
                     nickname_length_max_,
                     default_nickname_color,
                     avatars_max_count,
                     nicknames_max_count,
                     profile_user_id,
                     avatar_gallery_allowed,
                     allow_language_selection,
                     allow_delete_own_account
                     ) {
  profileUserId=profile_user_id;
  nickname_length_min=nickname_length_min_;
  nickname_length_max=nickname_length_max_;
  defaultNicknameColor=default_nickname_color;
  avatarsMaxCount=avatars_max_count;
  nicknamesMaxCount=nicknames_max_count;
  avatarGalleryAllowed=avatar_gallery_allowed;
  allowLanguageSelection=allow_language_selection;
  allowDeleteOwnAccount=allow_delete_own_account;
  profileUserLanguageId=allowLanguageSelection? parseInt($('contents_profile_data_language_id').value) : 0;
  // Set "onunload" handler
  window.onunload=function() {
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
  if (avatarsMaxCount==0) {
    $('navigation_link_avatars').style.display='none';
  }
  // Load and show profile data
  showProfileContents('profile');

  // Get focus
  window.focus();
}


/**
 * Show profile page
 * @param   string    id    Page identifier (e.g. 'profile' or 'avatars')
 */
function showProfileContents(id) {
  flushDisplay();
  if (CurrentProfilePage) {
    CurrentProfilePage.style.display='none';
    CurrentProfilePage=null;
  }
  switch (id) {

    case 'profile':
      loadProfileData('showProfileContents_profile()');
    break;

    case 'avatars':
      getAvatars();
    break;

    case 'nicknames':
      getNickNames();
    break;

    case 'level':
      loadProfileData('showUserLevel()');
    break;

    case 'password':
      resetPasswordForm();
    break;

    case 'ignore_list':
      loadProfileData('showIgnoreList()');
    break;

  }
  CurrentProfilePage=$('contents_data_'+id);
  CurrentProfilePage.style.display='';
}


/**
 * Load profile data
 * @param   string    callback    Callback function
 */
function loadProfileData(callback) {
  if (typeof(callback)!='string') callback='';
  toggleProgressBar(true);
  sendData('_CALLBACK_loadProfileData(\''+callback.split("'").join("\\'")+'\')',
           formlink,
           'POST',
           'ajax=get_memberlist'
           +'&s_id='+urlencode(s_id)
           +'&user_ids='+urlencode(profileUserId)
           +'&load_custom_fields=1'
           );
}
function _CALLBACK_loadProfileData(callback) {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  var custom_field_name='';
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    if (actionHandler.data['member'].length) {
      for (var i in actionHandler.data['member'][0]) {
        if (i=='custom_field') {
          UserData['custom_field']=new Array();
          for (var ii=0; ii<actionHandler.data['member'][0]['custom_field'].length; ii++) {
            custom_field_name=actionHandler.data['member'][0]['custom_field'][ii]['name'][0];
            UserData['custom_field'][custom_field_name]=new Array();
            for (var iii in actionHandler.data['member'][0]['custom_field'][ii]) {
              UserData['custom_field'][custom_field_name][iii]=actionHandler.data['member'][0]['custom_field'][ii][iii][0];
            }
            UserData['custom_field'][custom_field_name]['id']=parseInt(UserData['custom_field'][custom_field_name]['id']);
            UserDataCustomFieldsById[UserData['custom_field'][custom_field_name]['id']]=UserData['custom_field'][custom_field_name];
          }
        } else {
          UserData[i]=actionHandler.data['member'][0][i][0];
        }
      }
    }
  }
  // Show activation form
  if (isAdmin && UserData['activated']!='1') {
    $('account_activation_required').style.display='';
  }
  // Show "Change password" link
  $('navigation_link_password').style.display=UserData['is_guest']=='0'? '' : 'none';
  // Set window title
  document.title=getLng('users_profile').split('[USER]').join(UserData['nickname_plain']);
  // Show username
  $('contents_profile_data_username').innerHTML=htmlspecialchars(UserData['login']);
  // Show registration date
  $('contents_profile_data_regdate_row').style.display=UserData['is_guest']=='1'? 'none' : '';
  // Show "Level" link
  $('navigation_link_level').style.display=isAdmin? '' : 'none';
  // Show Email fields
  $('contents_profile_data_email_row0').style.display=UserData['is_guest']=='1'? 'none' : '';
  $('contents_profile_data_email_row1').style.display=UserData['is_guest']=='1'? 'none' : '';
  // Execute callback
  if (callback!='') {
    try {
      eval(callback);
    } catch(e) {}
  }
}


/**
 * Show profile data page
 */
function showProfileContents_profile() {
  var online_seconds=0;
  var online_days=0;
  var online_hours=0;
  var online_minutes=0;
  var online_time_html='';

  var profile_fields_tbl=$('profile_fields_tbl').tBodies[0];;
  var custom_field_tr_tpl=$('contents_profile_data_custom_field_tr_tpl');
  var custom_field_tr=null;

  // Calculate time spent online
  online_seconds=parseInt(UserData['time_online']);
  online_days=Math.floor(online_seconds/86400);
  online_seconds-=online_days*86400;
  online_hours=Math.floor(online_seconds/3600);
  online_seconds-=online_hours*3600;
  online_minutes=Math.floor(online_seconds/60);
  online_seconds-=online_minutes*60;

  online_time_html=online_seconds+' '+getLng('seconds');
  if (online_minutes>0 || online_hours>0 || online_days>0) {
    online_time_html=online_minutes+' '+getLng('minutes')+', '+online_time_html;
    if (online_hours>0 || online_days>0) {
      online_time_html=online_hours+' '+getLng('hours')+', '+online_time_html;
      if (online_days>0) {
        online_time_html=online_days+' '+getLng('days')+', '+online_time_html;
      }
    }
  }
  
  $('contents_profile_data_registration_date').innerHTML=htmlspecialchars(date(dateFormat, UserData['joined']));
  $('contents_profile_data_online_time').innerHTML=htmlspecialchars(online_time_html);
  $('contents_profile_data_email').value=UserData['email'];
  $('contents_profile_data_hide_email').value=UserData['hide_email'];

  // Display custom profile fields
  if (typeof(UserData['custom_field'])=='object' && UserData['custom_field']) {
    // Clean up table
    if (typeof(profile_fields_tbl.original_rows_count)!='number') {
      profile_fields_tbl.original_rows_count=profile_fields_tbl.rows.length;
    } else {
      while (profile_fields_tbl.rows.length>profile_fields_tbl.original_rows_count) {
        profile_fields_tbl.deleteRow(profile_fields_tbl.rows.length-3);
      }
    }
    for (var i in UserData['custom_field']) {
      custom_field_tr=custom_field_tr_tpl.cloneNode(true);
      custom_field_tr.id='custom_field_'+i;
      profile_fields_tbl.insertBefore(custom_field_tr, $('contents_profile_data_delete_own_account_row'));
      custom_field_tr.cells[0].innerHTML=htmlspecialchars(UserData['custom_field'][i]['name_translated'])+': ';
      custom_field_tr.cells[1].innerHTML=makeCustomDataFieldHTML(UserData['custom_field'][i]);
      custom_field_tr.style.display='';
    }
  }
  if (allowDeleteOwnAccount && UserData['is_guest']=='0') {
    $('contents_profile_data_delete_own_account_row').style.display='';
  }
  setTimeout('resizeForDocumentHeight(10, false)', 200);
}


/**
 * Create HTML contents for custom field
 * @param   object    custom_field_data     Custom field data
 * @return string
 */
function makeCustomDataFieldHTML(custom_field_data) {
  var html='';
  var choices=null;
  var values=null;
  var choice_text='';
  var editable=custom_field_data['writeable']=='user' || isAdmin;
  var option_background_image='';
  var option_padding='';
  switch (custom_field_data['type']) {

    case 'string':
    case 'url':
    case 'email':
      if (editable) {
        html+='<input type="text" id="custom_field_contents_input_'+htmlspecialchars(custom_field_data['id'])+'" title="'+htmlspecialchars(custom_field_data['name_translated'])+'" size="64" maxlength="255" value="'+htmlspecialchars(custom_field_data['field_value'])+'" />';
      } else {
        html+='<div title="'+htmlspecialchars(custom_field_data['name_translated'])+'">'+htmlspecialchars(custom_field_data['field_value'])+'</div>';
      }
    break;

    case 'text':
      if (editable) {
        html+='<textarea rows="8" cols="64" id="custom_field_contents_input_'+htmlspecialchars(custom_field_data['id'])+'" title="'+htmlspecialchars(custom_field_data['name_translated'])+'">'+htmlspecialchars(custom_field_data['field_value'])+'</textarea>';
      } else {
        html+='<div title="'+htmlspecialchars(custom_field_data['name_translated'])+'">'+nl2br(htmlspecialchars(custom_field_data['field_value']))+'</div>';
      }
    break;

    case 'choice':
      if (editable) {
        choices=custom_field_data['choices'].split("\n");
        html+='<select id="custom_field_contents_input_'+htmlspecialchars(custom_field_data['id'])+'" title="'+htmlspecialchars(custom_field_data['name_translated'])+'">'
        for (var i=0; i<choices.length; i++) {
          if (custom_field_data['name']=='gender' && custom_field_data['custom']=='n') {
            choice_text=getLng('gender_'+choices[i]);
            option_background_image='url(./pic/gender_'+choices[i]+'_10x10.gif)';
            option_padding='padding-left:13px;';
          } else {
            choice_text=choices[i];
            option_background_image='';
            option_padding='';
          }
          html+='<option style="background-position:center left;background-repeat:no-repeat;background-image:'+option_background_image+';'+option_padding+'" title="'+htmlspecialchars(custom_field_data['name_translated']+"\n"+custom_field_data['description'])+'" value="'+htmlspecialchars(choices[i])+'" '+(custom_field_data['field_value']==choices[i]? 'selected="selected"' : '')+'>'+htmlspecialchars(choice_text)+'</option>';
        }
        html+='</select>';
      } else {
        if (custom_field_data['name']=='gender' && custom_field_data['custom']=='n') {
          html+='<div><img src="./pic/gender_'+htmlspecialchars(custom_field_data['field_value'])+'_10x10.gif" title="'+htmlspecialchars(getLng('gender_'+custom_field_data['field_value']))+'" alt="'+htmlspecialchars(getLng('gender_'+custom_field_data['field_value']))+'" /> '+htmlspecialchars(getLng('gender_'+custom_field_data['field_value']))+'</div>';
        } else {
          html+='<div title="'+htmlspecialchars(custom_field_data['name_translated'])+'">'+htmlspecialchars(custom_field_data['field_value'])+'</div>';
        }
      }
    break;

    case 'multichoice':
      if (editable) {
        choices=custom_field_data['choices'].split("\n");
        values="\n"+custom_field_data['field_value']+"\n";
        html+='<select multiple="multiple" size="'+(choices.length<8? choices.length : 8)+'" id="custom_field_contents_input_'+htmlspecialchars(custom_field_data['id'])+'" title="'+htmlspecialchars(custom_field_data['name_translated'])+'">'
        for (var i=0; i<choices.length; i++) {
          html+='<option value="'+htmlspecialchars(choices[i])+'" '+(-1!=values.indexOf("\n"+choices[i]+"\n")? 'selected="selected"' : '')+'>'+htmlspecialchars(choices[i])+'</option>';
        }
        html+='</select>';
      } else {
        html+='<div title="'+htmlspecialchars(custom_field_data['name_translated'])+'">'+nl2br(htmlspecialchars(custom_field_data['field_value']))+'</div>';
      }
    break;

  }
  return html;
}


/**
 * Save profile changes
 */
function saveProfileChanges() {
  var errors=new Array();
  var field=null;
  var field_input_value='';
  var custom_userdata_fields=new Array();
  saveProfileChangesTasks=new Array();
  saveProfileChangesMessages=new Array();
  saveProfileChangesReload=false;

  // Email address
  if (UserData['is_guest']=='0') {
    $('contents_profile_data_email').value=trimString($('contents_profile_data_email').value);
    if (!checkEmail($('contents_profile_data_email').value)) {
      errors.push(getLng('email_invalid'));
    } else if ($('contents_profile_data_email').value!=UserData['email']) {
      saveProfileChangesTasks.push('saveEmailAddress("'+$('contents_profile_data_email').value.split('"').join('\\"')+'")');
    }
  }

  // Email address visibility
  if ($('contents_profile_data_hide_email').value!=UserData['hide_email']) {
    saveProfileChangesTasks.push('setEmailVisibility("'+$('contents_profile_data_hide_email').value.split('"').join('\\"')+'")');
  }

  // Custom fields
  for (var field_id in UserDataCustomFieldsById) {
    field=$('custom_field_contents_input_'+field_id);
    if (field) {
      if (UserDataCustomFieldsById[field_id]['type']=='multichoice') {
        field_input_value='';
        for (var i=0; i<field.options.length; i++) {
          if (field.options[i].selected) {
            field_input_value+=(field_input_value.length? ("\n"+field.options[i].value) : field.options[i].value);
          }
        }
      } else {
        field_input_value=trimString(field.value);
      }
      if (urlencode(UserDataCustomFieldsById[field_id]['field_value'])!=urlencode(field_input_value)) {
        custom_userdata_fields.push('custom_fields['+field_id+']='+urlencode(field_input_value));
      }
    }
  }
  if (custom_userdata_fields.length) {
    saveProfileChangesTasks.push('saveUserdataCustomFields("'+custom_userdata_fields.join('&')+'")');
  }

  // Language
  if (allowLanguageSelection && profileUserLanguageId!=parseInt($('contents_profile_data_language_id').value)) {
    saveProfileChangesTasks.push('setNewLanguage('+$('contents_profile_data_language_id').value+')');
  }
//alert(saveProfileChangesTasks); return false;

  if (errors.length) {
    alert(errors.join("\n"));
  } else if (saveProfileChangesTasks.length) {
    executeProfileChangesTask();
  }
}


/**
 * Execute profile changing task
 */
function executeProfileChangesTask() {
  if (saveProfileChangesTasks.length) {
    try {
      eval(saveProfileChangesTasks.shift());
    } catch(e) {}
  } else {
    if (saveProfileChangesMessages.length) {
      alert(saveProfileChangesMessages.join("\n"), 0, 0, saveProfileChangesReload? 'window.location.reload()' : 'loadProfileData(\'showProfileContents_profile()\')');
    } else if (saveProfileChangesReload) {
      window.location.reload();
    } else {
      loadProfileData('showProfileContents_profile()');
    }
  }
}


/**
 * Save custom profile fields
 * @param   string    fields
 */
function saveUserdataCustomFields(fields) {
  if (typeof(fields)=='string' && fields!='') {
    sendData('_CALLBACK_saveUserdataCustomFields()',
             formlink,
             'POST',
             'ajax=update_userdata'
             +'&s_id='+urlencode(s_id)
             +'&profile_user_id='+urlencode(profileUserId)
             +'&'+fields
             );
  }
}
function _CALLBACK_saveUserdataCustomFields() {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    saveProfileChangesMessages.push(actionHandler.message);
  }
  executeProfileChangesTask();
}


/**
 * Set new email address
 * @var string  email   New email address
 */
function saveEmailAddress(email) {
  sendData('_CALLBACK_changeEmailAddress()',
           formlink,
           'POST',
           'ajax=change_email'
           +'&s_id='+urlencode(s_id)
           +'&profile_user_id='+urlencode(profileUserId)
           +'&email='+urlencode(email)
           );
}
function _CALLBACK_changeEmailAddress() {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  switch (actionHandler.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case 0:
      // Email changed
      saveProfileChangesMessages.push(actionHandler.message);
    break;
    default:
      // An error occured
      saveProfileChangesMessages.push(actionHandler.message);
      // Cancel remaining tasks
      saveProfileChangesTasks=new Array();
    break;
  }
  executeProfileChangesTask();
}


/**
 * Set new language
 * @param   int   language_id   New language
 */
function setNewLanguage(language_id) {
  sendData('_CALLBACK_setNewLanguage()',
           formlink,
           'POST',
           'ajax=set_user_language'
           +'&s_id='+urlencode(s_id)
           +'&profile_user_id='+urlencode(profileUserId)
           +'&set_language_id='+urlencode(language_id)
           );
}
function _CALLBACK_setNewLanguage() {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  switch (actionHandler.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case 0:
      // Data changed
      saveProfileChangesReload=true;
    break;
    default:
      // An error occured
      saveProfileChangesMessages.push(actionHandler.message);
    break;
  }
  executeProfileChangesTask();
}


/**
 * Reset "Change password" form
 */
function resetPasswordForm() {
  $('new_password_0').value='';
  $('new_password_1').value='';
}


/**
 * Change password
 */
function changePassword() {
  var new_pass=$('new_password_0').value;
  var new_pass2=$('new_password_1').value;
  if (typeof(new_pass)!='string') {
    prompt(getLng('enter_new_password')+':', '', 0, 0, 'changePassword(promptboxValue)', true);
  } else {
    if (new_pass=='') {
      // Password is empty
      alert(getLng('password_empty'));
    } else if (new_pass.length<3) {
      // Password is too short
      alert(getLng('password_too_short'));
    } else if (new_pass.length!=new_pass2.length || new_pass!=new_pass2) {
      alert(getLng('passwords_not_ident'));
      return false;
    } else {
      // Store new password
      sendData('_CALLBACK_changePassword()',
               formlink,
               'POST',
               'ajax=change_password'
               +'&s_id='+urlencode(s_id)
               +'&profile_user_id='+urlencode(profileUserId)
               +'&password='+base64encode(urlencode(new_pass))
               );
    }
  }
}
function _CALLBACK_changePassword() {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  switch (actionHandler.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case 0:
      // Password changed
      resetPasswordForm();
      alert(actionHandler.message);
    break;
    default:
      // An error occured
      alert(actionHandler.message);
    break;
  }
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
  var avatars_in_row=4;

  var tr=null;
  var td=null;

  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else if (avatars_tbl) {
    avatars_tbl.style.display='';
    // Empty avatars table
    for (var i=avatars_tbl.rows.length-1; i>1; i--) {
      avatars_tbl.deleteRow(i-1);
    }
    $('avatars_header_cell').colSpan=avatars_in_row;
    $('avatars_footer_cell').colSpan=avatars_in_row;
    avatars_count=actionHandler.data['avatar'].length;
    for (avatar_nr=0; avatar_nr<avatars_count; avatar_nr++) {
      if (0==(avatar_nr%avatars_in_row)) {
        tr=avatars_tbl.insertRow(avatars_tbl.rows.length-1);
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
    if (1==(avatar_nr%avatars_in_row)) {
      td.colSpan=avatars_in_row;
    }
    if (avatars_count<avatarsMaxCount || avatars_count==1 && avatarsMaxCount>0 && avatar_id==0) {
      $('upload_avatar_btn').style.display='';
      if (avatarGalleryAllowed) {
        $('avatar_gallery_btn').style.display='';
      } else {
        $('avatar_gallery_btn').style.display='none';
      }
    } else {
      $('upload_avatar_btn').style.display='none';
      $('avatar_gallery_btn').style.display='none';
    }
  }
  toggleProgressBar(false);
  setTimeout('resizeForDocumentHeight(10, false)', 200);
}


/**
 * Open "Upload avatar" window
 */
function showNewAvatarForm() {
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
 * Hide all dynamically displayed input fields and forms
 */
function flushDisplay() {
  hideNicknameForm();
}


/**
 * Delete avatar
 * @param   int       avatar_id     Avatar ID
 * @param   boolean   confirmed     Optional. If TRUE: no confirmation will be displayed. Default: FALSE. 
 */
function deleteAvatar(avatar_id, confirmed) {
  if (typeof(confirmed)!='boolean' || !confirmed) {
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
      getAvatars();
    }
    alert(actionHandler.message);
  }
}


/**
 * Set new primary avatar
 * @param   int   id    Avatar ID
 */
function setPrimaryAvatar(id) {
  sendData('toggleProgressBar(false)', formlink, 'POST', 'ajax=set_primary_avatar&s_id='+urlencode(s_id)+'&avatar_id='+urlencode(id)+'&profile_user_id='+urlencode(profileUserId));
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
  $('contents_data_nicknames').innerHTML=$('contents_data_nicknames').innerHTML;
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
    $('nicknames_tbl_header2').style.display='';
  } else {
    // There are no nicknames
    $('no_nicknames').style.display='';
    $('nicknames_tbl_header2').style.display='none';
  }
  if (i<nicknamesMaxCount) {
    $('new_nickname_link_row').style.display='';
  } else {
    $('new_nickname_link_row').style.display='none';
  }
  setTimeout('resizeForDocumentHeight(10, false)', 200);
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
  newCol.innerHTML='<input type="radio" title="'+htmlspecialchars(getLng('active'))+'" name="nickname_selector" id="nickname_selector_'+nickname_id+'" value="'+nickname_id+'" onclick="setDefaultNickname('+nickname_id+')" '+(is_default? 'checked="checked"' : '')+' />';
  setCssClass(newCol, '.tbl_row');
  newCol.style.textAlign='center';

  newCol=newRow.insertCell(-1);
  setCssClass(newCol, '.tbl_row');
  newCol.innerHTML='<a href="#" onclick="deleteNickname('+nickname_id+')" title="'+htmlspecialchars(getLng('delete_nickname'))+'"><img src="./pic/delete_13x13.gif" alt="'+htmlspecialchars(getLng('delete_nickname'))+'" border="0"></a>'
                  +'&nbsp;&nbsp;'
                  +'<a href="#" onclick="showNicknameForm('+nickname_id+')" title="'+htmlspecialchars(getLng('edit'))+'"><img src="./pic/edit_13x13.gif" alt="'+htmlspecialchars(getLng('edit'))+'" border="0"></a>'
                  ;
  newCol.style.textAlign='center';

  newCol=newRow.insertCell(-1);
  setCssClass(newCol, '.tbl_row');
  newCol.innerHTML='<label for="nickname_selector_'+nickname_id+'">'
                   +'<span id="nickname_span_'+nickname_id+'">'+coloredToHTML(nickname)+'</span>'
                   +'</label>'
                   ;
  newCol.style.width='100%';

  $('nickname_span_'+nickname_id).nickname_plain=coloredToPlain(nickname, false);
  $('nickname_span_'+nickname_id).nickname_colored=nickname;
}


/**
 * Set new default nickname
 * @param   int   id    Nickname ID
 */
function setDefaultNickname(id) {
  sendData('toggleProgressBar(false)', formlink, 'POST', 'ajax=set_default_nickname&s_id='+urlencode(s_id)+'&nickname_id='+urlencode(id)+'&profile_user_id='+urlencode(profileUserId));
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
 * Show nickname form
 * @param   int   nickname_id   Nickname ID
 */
function showNicknameForm(nickname_id) {
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
    $('nickname_preview').innerHTML=coloredToHTML($('nickname_text_input').value_colored)+'&nbsp;';
  };
  $('nickname_text_input').onchange=$('nickname_text_input').onkeydown;
  $('nickname_text_input').onclick=$('nickname_text_input').onkeydown;
  $('nickname_text_input').onblur=$('nickname_text_input').onkeydown;
  $('nickname_text_input').onfocus=$('nickname_text_input').onkeydown;
  $('nickname_preview').innerHTML=coloredToHTML($('nickname_text_input').value_colored)+'&nbsp;';

  eval('$(\'save_nickname_color_btn\').onclick=function() { manageNickname(null, '+nickname_id+', $(\'nickname_text_input\').value_colored); }');

  colorbox_callback_func=function(color) {
    $('nickname_text_input').value_colored=applyColorCode('nickname_text_input', color, $('nickname_text_input').value_colored);
    $('nickname_preview').innerHTML=coloredToHTML($('nickname_text_input').value_colored)+'&nbsp;';
  }
  openColorBox(null, null, null, null, false, false, false, defaultNicknameColor, false, getTopPos($('nickname_colorbox_container')), getLeftPos($('nickname_colorbox_container')));
  $('nickname_text_input').focus();
}


/**
 * Hide nickname form
 */
function hideNicknameForm() {
  $('nickname_colorizer_table').style.display='none';
  $('nicknames_table').style.display='';
  $('new_nickname_link_row').style.display=nicknamesCount<nicknamesMaxCount? '' : 'none';
  closeColorBox('', false);
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
 * Activate user account manually
 * @param   boolean   confirmed     First confirmation
 */
function activateUser(confirmed) {
  if (isAdmin) {
    if (typeof(confirmed)!='boolean' || !confirmed) {
      confirm(getLng('sure_activate_account'), null, null, 'activateUser(true)');
    } else {
      toggleProgressBar(true);
      sendData('_CALLBACK_activateUser()',
               formlink,
               'POST',
               'ajax=activate_user'
               +'&s_id='+urlencode(s_id)
               +'&profile_user_id='+urlencode(profileUserId)
               );
    }
  }
}
function _CALLBACK_activateUser() {
  toggleProgressBar(false);
  switch (actionHandler.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case  0:
      // Account activated
      alert(actionHandler.message, 0, 0, "try { $('memberlist_search_button', window.opener.document).click(); } catch (e) {} window.location.reload();");
    break;
    default:
      // An error occured
      alert(actionHandler.message);
    break;
  }
}


/**
 * Display user level data
 */
function showUserLevel() {
  $('member_level').style.display='none';
  $('member_level_options').style.display='';
  if ($('member_level_option_'+$('member_level_id').value)) {
    $('member_level_option_'+$('member_level_id').value).click();
  }
  if (UserData['is_registered']=='0') {
    $('member_level_option_g').click();
  } else {
    $('member_level_option_g').disabled=true;
    if (UserData['is_admin']=='1') {
      $('member_level_option_a').click();
    } else {
      $('member_level_option_r').click();
    }
  }
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
    } else {
      return false;
    }
    if (currentUserId==profileUserId) {
      alert(getLng('change_own_level_error'));
    } else {
      if (typeof(confirmed)!='boolean' || !confirmed) {
        confirm(getLng('sure_change_user_level'), null, null, 'setMemberLevel(true)');
      } else if (typeof(confirmed2)!='boolean' || !confirmed2) {
        confirm(getLng('really_sure'), null, null, 'setMemberLevel(true, true)');
      } else {
        toggleProgressBar(true);
        sendData('_CALLBACK_setMemberLevel()',
                 formlink,
                 'POST',
                 'ajax=set_user_level'
                 +'&s_id='+urlencode(s_id)
                 +'&profile_user_id='+urlencode(profileUserId)
                 +'&level='+urlencode(new_level)
                 );
      }
    }
  }
}
function _CALLBACK_setMemberLevel() {
  toggleProgressBar(false);
  switch (actionHandler.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    default:
      alert(actionHandler.message);
      loadProfileData('showUserLevel()');
    break;
  }
}


/**
 * Display "Ignore list"
 */
function showIgnoreList() {
  var ignore_list_table=$('ignore_list_table');
  // Clean up table
  if (typeof(ignore_list_table.original_rows_count)!='number') {
    ignore_list_table.original_rows_count=ignore_list_table.rows.length;
  } else {
    while (ignore_list_table.rows.length>ignore_list_table.original_rows_count) {
      ignore_list_table.deleteRow(ignore_list_table.rows.length-1);
    }
  }
  if (UserData['muted_users']=='') {
    $('no_members_found').style.display='';
  } else {
    toggleProgressBar(true);
    sendData('_CALLBACK_showIgnoreList()',
             formlink,
             'POST',
             'ajax=get_memberlist'
             +'&s_id='+urlencode(s_id)
             +'&sort_by=1'
             +'&sort_dir=0'
             +'&user_ids='+urlencode(UserData['muted_users'])
             );
  }
}
function _CALLBACK_showIgnoreList() {
//debug(actionHandler.getResponseString()); return false;
  var member=null;
  var tr=null;
  var td=null;

  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  }
  if (actionHandler.data['member'].length==0) {
    $('no_members_found').style.display='none';
  } else {
    for (var i=0; i<actionHandler.data['member'].length; i++) {
      member=actionHandler.data['member'][i];
      tr=ignore_list_table.insertRow(-1);

      td=tr.insertCell(-1);
      td.innerHTML='<img src="./pic/delete_13x13.gif" alt="'+htmlspecialchars(getLng('delete'))+'" title="'+htmlspecialchars(getLng('delete'))+'" style="cursor:pointer" onclick="deleteFromIgnoreList('+htmlspecialchars(member['id'][0])+')" />'
                  +'&nbsp;&nbsp;'
                  +'<span title="'+htmlspecialchars(member['nickname_plain'][0])+'" style="cursor:default"><b>'+coloredToHTML(member['nickname'][0])+'</b></span>'
                  ;
      setCssClass(td, 'tbl_row');
    }
  }
  toggleProgressBar(false);
  setTimeout('resizeForDocumentHeight(10, false)', 200);
}


/**
 * Delete user from ignore list
 * @param   int   user_id
 */
function deleteFromIgnoreList(user_id) {
  if (typeof(user_id)=='number' && user_id>0) {
    toggleProgressBar(true);
    sendData('showProfileContents(\'ignore_list\')',
             formlink,
             'POST',
             'ajax=mute_unmute_locally'
             +'&s_id='+urlencode(s_id)
             +'&target_user_id='+urlencode(user_id)
             +'&action=0'
             +'&profile_user_id='+urlencode(profileUserId)
             +'&post_control_message=1'
             );
  }
}


/**
 * Set new email visibility
 * @var   int     hide_email
 */
function setEmailVisibility(hide_email) {
  toggleProgressBar(true);
  sendData('_CALLBACK_setEmailVisibility()',
           formlink,
           'POST',
           'ajax=change_email_visibility'
           +'&s_id='+urlencode(s_id)
           +'&profile_user_id='+urlencode(profileUserId)
           +'&hide_email='+urlencode(hide_email)
           );
}
function _CALLBACK_setEmailVisibility() {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  switch (actionHandler.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    default:
      saveProfileChangesMessages.push(actionHandler.message);
    break;
  }
  executeProfileChangesTask();
}


/**
 * Delete own account
 * @var   boolean   confirmed
 */
function deleteOwnAccount(confirmed) {
  if (typeof(confirmed)!='boolean' || !confirmed) {
    confirm(getLng('delete_my_account_confirmation'), null, null, 'deleteOwnAccount(true)');
  } else {
    toggleProgressBar(true);
    sendData('_CALLBACK_deleteOwnAccount()',
             formlink,
             'POST',
             'ajax=delete_user'
             +'&s_id='+urlencode(s_id)
             +'&profile_user_id='+urlencode(profileUserId)
             );
  }
}
function _CALLBACK_deleteOwnAccount() {
  window.opener.location.href=window.opener.location.href+'?';
  window.close();
}