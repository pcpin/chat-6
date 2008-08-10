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
 * Array containing user data
 * @var array
 */
var UserData=new Array();

/**
 * Array containing user avatars
 * @var array
 */
var UserAvatars=new Array();

/**
 * Maximum allowed avatars number
 * @var int
 */
var AvatarsMaxCount=0;


/**
 * Initialize profile
 * @param   int       user_id               User ID
 * @param   int       avatars_max_count     How many avatars are allowed?
 */
function initProfilePublic(user_id, avatars_max_count) {
  AvatarsMaxCount=avatars_max_count;
  // Get profile data
  getPublicProfileData(user_id);
  // Get focus
  window.focus();
}


/**
 * Get profile data
 * @param   int   user_id     User ID
 */
function getPublicProfileData(user_id) {
  if (typeof(user_id)=='number' && user_id>0) {
    toggleProgressBar(true);
    sendData('_CALLBACK_getPublicProfileData('+user_id+')',
             formlink,
             'POST',
             'ajax=get_memberlist'
             +'&s_id='+urlencode(s_id)
             +'&user_ids='+urlencode(user_id)
             +'&load_custom_fields=1'
             );
  }
}
function _CALLBACK_getPublicProfileData(user_id) {
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
          }
        } else {
          UserData[i]=actionHandler.data['member'][0][i][0];
        }
      }
    }
  }
  // Load avatars
  getUserAvatars(user_id);
}


/**
 * Get user avatars
 * @param user_id
 */
function getUserAvatars(user_id) {
  if (AvatarsMaxCount>0) {
    toggleProgressBar(true);
    sendData('_CALLBACK_getUserAvatars('+user_id+')',
             formlink,
             'POST',
             'ajax=get_avatars'
             +'&s_id='+urlencode(s_id)
             +'&profile_user_id='+urlencode(user_id)
             );
  } else {
    _CALLBACK_getUserAvatars(user_id);
  }
}
function _CALLBACK_getUserAvatars(user_id) {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  var avatar=null;

  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    if (typeof(actionHandler.data['avatar'])!='undefined') {
      for (var i=0; i<actionHandler.data['avatar'].length; i++) {
        avatar=new Array();
        for (var ii in actionHandler.data['avatar'][i]) {
          avatar[ii]=actionHandler.data['avatar'][i][ii][0];
        }
        UserAvatars.push(avatar);
      }
    }
  }
  // Display profile
  displayPublicProfile(user_id);
}


/**
 * Show public profile data
 */
function displayPublicProfile(user_id) {
  var online_seconds=0;
  var online_days=0;
  var online_hours=0;
  var online_minutes=0;
  var online_time_html='';
  var profile_fields_tbl=$('profile_table').tBodies[0];;
  var custom_field_tr_tpl=$('contents_profile_data_custom_field_tr_tpl');
  var custom_field_tr=null;
  var profile_fields_tbl_last_row=$('profile_fields_tbl_last_row');
  var avatar=null;
  var avatar_id=0;
  var avatar_binaryfile_id=0;
  var avatar_width=0;
  var avatar_height=0;


  // Set window title
  document.title=getLng('users_profile').split('[USER]').join(UserData['nickname_plain']);
  $('profile_header').innerHTML=htmlspecialchars(getLng('users_profile').split('[USER]').join(UserData['nickname_plain']));
  // Show nickname
  $('contents_profile_data_nickname').innerHTML='<b>'+coloredToHTML(UserData['nickname'])+'</b>'+(UserData['is_guest']=='1'? ' ('+htmlspecialchars(getLng('guest'))+')' : '');
  // Show registration date
  $('contents_profile_data_regdate_row').style.display=UserData['is_guest']=='1'? 'none' : '';
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
  if (UserData['is_guest']=='0' && user_id==currentUserId || UserData['hide_email']=='0') {
    $('contents_profile_data_email').innerHTML='<div title="'+htmlspecialchars(getLng('email_address'))+': '+htmlspecialchars(UserData['email'])+'"><a href="'+formlink+'?external_url='+urlencode('mailto:'+UserData['email'])+'" target="_blank">'+htmlspecialchars(UserData['email'])+'</a></div>';
    $('contents_profile_data_email_row').style.display='';
  }

  // Display custom profile fields
  if (typeof(UserData['custom_field'])=='object' && UserData['custom_field']) {
    // Clean up table
    if (typeof(profile_fields_tbl.original_rows_count)!='number') {
      profile_fields_tbl.original_rows_count=profile_fields_tbl.rows.length;
    } else {
      while (profile_fields_tbl.rows.length>profile_fields_tbl.original_rows_count) {
        profile_fields_tbl.deleteRow(profile_fields_tbl.rows.length-2);
      }
    }
    for (var i in UserData['custom_field']) {
      if (UserData['custom_field'][i]['field_value']!='') {
        custom_field_tr=custom_field_tr_tpl.cloneNode(true);
        custom_field_tr.id='custom_field_'+i;
        profile_fields_tbl.insertBefore(custom_field_tr, profile_fields_tbl_last_row);
        custom_field_tr.cells[0].innerHTML=htmlspecialchars(UserData['custom_field'][i]['name_translated'])+': ';
        custom_field_tr.cells[1].innerHTML=makeCustomDataFieldHTML(UserData['custom_field'][i]);
        custom_field_tr.style.display='';
      }
    }
  }

  // Display avatars
  if (UserAvatars.length>0) {
    $('avatars_row').style.display='';
    $('avatar_image').innerHTML='';
    $('avatar_thumbs').innerHTML='';
    for (var avatar_nr=0; avatar_nr<UserAvatars.length; avatar_nr++) {
      avatar=UserAvatars[avatar_nr];
      avatar_id=stringToNumber(avatar['id']);
      avatar_binaryfile_id=stringToNumber(avatar['binaryfile_id']);
      avatar_width=stringToNumber(avatar['width']);
      avatar_height=stringToNumber(avatar['height']);
      if (avatar_nr==0) {
        // First avatar
        $('avatar_image').innerHTML='<img id="avatar_img" onload="setTimeout(\'resizeForDocumentHeight(10)\', 200);" src="'+htmlspecialchars(formlink)+'?b_id='+htmlspecialchars(avatar_binaryfile_id)+'&amp;s_id='+htmlspecialchars(s_id)+'&amp;b_x=120&amp;b_y=100" border="0" alt="'+htmlspecialchars(getLng('avatar'))+'" title="'+htmlspecialchars(getLng('avatar'))+'" style="cursor:pointer" />';
        $('avatar_img').binaryfile_id=avatar_binaryfile_id;
        $('avatar_img').ow_width=avatar_width;
        $('avatar_img').ow_height=avatar_height;
      }
      // Display thumb
      $('avatar_thumbs').innerHTML+='<img style="cursor:pointer" id="avatar_thumb_'+htmlspecialchars(avatar_id)+'" src="'+formlink+'?b_id='+htmlspecialchars(avatar_binaryfile_id)+'&amp;s_id='+htmlspecialchars(s_id)+'&amp;b_x=50&amp;b_y=43'+'" border="0" alt="'+htmlspecialchars(getLng('avatar'))+'" title="'+htmlspecialchars(getLng('avatar'))+'" onmouseover="$(\'avatar_img\').onload=\'\'; $(\'avatar_img\').src=\''+formlink+'?b_id='+htmlspecialchars(avatar_binaryfile_id)+'&amp;s_id='+htmlspecialchars(s_id)+'&amp;b_x=120&amp;b_y=100'+'\'; $(\'avatar_img\').binaryfile_id='+htmlspecialchars(avatar_binaryfile_id)+'; $(\'avatar_img\').ow_width='+htmlspecialchars(avatar_width)+'; $(\'avatar_img\').ow_height='+htmlspecialchars(avatar_height)+';" onclick="$(\'avatar_img\').onclick()" />'
                                  + '<img src="./pic/clearpixel_1x1.gif" width="5" height="1" alt="" />';
      $('avatar_img').onclick=function() {
        openWindow(formlink+'?inc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
        return false;
      };
    }
    if (UserAvatars.length==1) {
      // There is only one avatar. Hide thumb.
      $('avatar_thumb_'+htmlspecialchars(avatar_id)).style.display='none';
    }
  }

  // Online status
  if (UserData['online_status']!='0') {
    $('profile_online_status').innerHTML=htmlspecialchars(getLng('user_is_logged_in')).split('[USER]').join('<b>'+coloredToHTML(UserData['nickname'])+'</b>');
  } else {
    $('profile_online_status').innerHTML=htmlspecialchars(getLng('user_is_not_logged_in')).split('[USER]').join('<b>'+coloredToHTML(UserData['nickname'])+'</b>');
  }

  // "Invite" button
  if (UserData['invitable']=='1') {
    $('invite_user').style.display='';
    $('invite_button').title=getLng('invite_user_to_your_room').split('[USER]').join(UserData['nickname_plain']);
    $('invite_button').innerHTML=htmlspecialchars(getLng('invite_user_to_your_room').split('[USER]').join(UserData['nickname_plain']));
    $('invite_button').tgt_user_id=user_id;
    $('invite_button').onclick=function() {
      sendInvitation(this.tgt_user_id);
    }
  }


  $('profile_table').style.display='';
  $('close_window_btn_div').style.display='';
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

    default:
      html+='<div title="'+htmlspecialchars(custom_field_data['name_translated'])+'">'+htmlspecialchars(custom_field_data['field_value'])+'</div>';
    break;

    case 'url':
      html+='<div title="'+htmlspecialchars(custom_field_data['name_translated'])+': '+htmlspecialchars(custom_field_data['field_value'])+'"><a href="'+formlink+'?external_url='+urlencode(custom_field_data['field_value'])+'" target="_blank">'+htmlspecialchars(custom_field_data['field_value'])+'</a></div>';
    break;

    case 'email':
      html+='<div title="'+htmlspecialchars(custom_field_data['name_translated'])+': '+htmlspecialchars(custom_field_data['field_value'])+'"><a href="'+formlink+'?external_url='+urlencode('mailto:'+custom_field_data['field_value'])+'" target="_blank">'+htmlspecialchars(custom_field_data['field_value'])+'</a></div>';
    break;
    
    case 'text':
    case 'multichoice':
      html+='<div title="'+htmlspecialchars(custom_field_data['name_translated'])+'">'+nl2br(htmlspecialchars(custom_field_data['field_value']))+'</div>';
    break;

    case 'choice':
      if (custom_field_data['name']=='gender' && custom_field_data['custom']=='n') {
        html+='<div><img src="./pic/gender_'+htmlspecialchars(custom_field_data['field_value'])+'_10x10.gif" title="'+htmlspecialchars(getLng('gender_'+custom_field_data['field_value']))+'" alt="'+htmlspecialchars(getLng('gender_'+custom_field_data['field_value']))+'" /> '+htmlspecialchars(getLng('gender_'+custom_field_data['field_value']))+'</div>';
      } else {
        html+='<div title="'+htmlspecialchars(custom_field_data['name_translated'])+'">'+htmlspecialchars(custom_field_data['field_value'])+'</div>';
      }
    break;

  }
  return html;
}
