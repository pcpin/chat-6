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
 * Initialize profile
 * @param   int       user_id               User ID
 * @param   int       avatars_max_count     How many avatars are allowed?
 */
function initProfilePublic(user_id, avatars_max_count) {
  if (avatars_max_count==0) {
    $('avatars_row').style.display='none';
  } else {
    $('avatars_row').style.display='';
  }
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
    sendData('_CALLBACK_getPublicProfileData('+user_id+')', formlink, 'POST', 'ajax='+urlencode('get_public_profile_data')+'&s_id='+urlencode(s_id)+'&user_id='+urlencode(user_id));
  }
}
function _CALLBACK_getPublicProfileData(user_id) {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  var profile_data=actionHandler.getElement('profile_data');
  var nickname='';
  var gender='';
  var avatar=null;
  var avatar_nr=0;
  var avatars_count=0;
  var seconds_online=0;
  var homepage='';

  if (status=='-1') {
    // Session is invalid
    window.close();
    opener.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (message=='OK') {
      nickname=actionHandler.getCdata('nickname', 0, profile_data);
      if (nickname==null) {
        nickname='-';
      }
      $('profile_header').innerHTML=htmlspecialchars(actionHandler.getCdata('header', 0, profile_data));
      $('profile_nickname').innerHTML=coloredToHTML(nickname);
      if (null!=actionHandler.getCdata('hide_email', 0, profile_data)) {
        // Email is hidden
        $('profile_email_row').style.display='none';
      } else {
        $('profile_email_link').innerHTML=htmlspecialchars(actionHandler.getCdata('email', 0, profile_data));
        $('profile_email_link').title=$('profile_email_link').innerHTML;
        $('profile_email_link').href='mailto:'+actionHandler.getCdata('email', 0, profile_data);
      }
      if ('y'==actionHandler.getCdata('is_guest', 0, profile_data)) {
        $('profile_registration_date').innerHTML=htmlspecialchars('- ('+getLng('guest')+')');
      } else {
        $('profile_registration_date').innerHTML=htmlspecialchars(date(dateFormat, actionHandler.getCdata('registration_date', 0, profile_data)));
      }
      // Time spent online
      $('profile_time_spent_online').innerHTML='';
      seconds_online=actionHandler.getCdata('time_online', 0, profile_data);
      $('profile_time_spent_online').innerHTML=htmlspecialchars((seconds_online%60)+' '+getLng('seconds'));
      if (seconds_online>60) {
        $('profile_time_spent_online').innerHTML=htmlspecialchars((Math.floor(seconds_online/60)%60)+' '+getLng('minutes')+', ')+$('profile_time_spent_online').innerHTML;
        if (seconds_online>3600) {
          $('profile_time_spent_online').innerHTML=htmlspecialchars((Math.floor(seconds_online/3600)%24)+' '+getLng('hours')+', ')+$('profile_time_spent_online').innerHTML;
          if (seconds_online>86400) {
            $('profile_time_spent_online').innerHTML=htmlspecialchars((Math.floor(seconds_online/86400))+' '+getLng('days')+', ')+$('profile_time_spent_online').innerHTML;
          }
        }
      }
      // Avatars
      avatars_count=actionHandler.countElements('avatar', profile_data);
      $('avatar_image').innerHTML='';
      $('avatar_thumbs').innerHTML='';
      while (null!=(avatar=actionHandler.getElement('avatar', avatar_nr++, profile_data))) {
        avatar_id=stringToNumber(actionHandler.getCdata('id', 0, avatar));
        avatar_binaryfile_id=stringToNumber(actionHandler.getCdata('binaryfile_id', 0, avatar));
        avatar_width=stringToNumber(actionHandler.getCdata('width', 0, avatar));
        avatar_height=stringToNumber(actionHandler.getCdata('height', 0, avatar));
        if (avatar_nr==1) {
          // First avatar
          $('avatar_image').innerHTML='<img id="avatar_img" onload="setTimeout(\'resizeForDocumentHeight(10)\', 100);" src="'+htmlspecialchars(formlink)+'?b_id='+htmlspecialchars(avatar_binaryfile_id)+'&amp;s_id='+htmlspecialchars(s_id)+'&amp;b_x=120&amp;b_y=85" border="0" alt="'+htmlspecialchars(getLng('avatar'))+'" title="'+htmlspecialchars(getLng('avatar'))+'" style="cursor:pointer" />';
          $('avatar_img').binaryfile_id=avatar_binaryfile_id;
          $('avatar_img').ow_width=avatar_width;
          $('avatar_img').ow_height=avatar_height;
        }

        // Display thumb
        $('avatar_thumbs').innerHTML+='<img id="avatar_thumb_'+htmlspecialchars(avatar_id)+'" src="'+formlink+'?b_id='+htmlspecialchars(avatar_binaryfile_id)+'&amp;s_id='+htmlspecialchars(s_id)+'&amp;b_x=50&amp;b_y=43'+'" border="0" alt="'+htmlspecialchars(getLng('avatar'))+'" title="'+htmlspecialchars(getLng('avatar'))+'" onmouseover="$(\'avatar_img\').onload=\'\'; $(\'avatar_img\').src=\''+formlink+'?b_id='+htmlspecialchars(avatar_binaryfile_id)+'&amp;s_id='+htmlspecialchars(s_id)+'&amp;b_x=120&amp;b_y=85'+'\'; $(\'avatar_img\').binaryfile_id='+htmlspecialchars(avatar_binaryfile_id)+'; $(\'avatar_img\').ow_width='+htmlspecialchars(avatar_width)+'; $(\'avatar_img\').ow_height='+htmlspecialchars(avatar_height)+';" />'
                                    + '<img src="./pic/clearpixel_1x1.gif" width="5" height="1" alt="" />';
        $('avatar_img').onclick=function() {
          openWindow(formlink+'?inc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
          return false;
        };

      }
      if (avatars_count==1) {
        // There is only one avatar. Hide thumb.
        $('avatar_thumb_'+htmlspecialchars(avatar_id)).style.display='none';
      } else if (avatars_count==0) {
        // No avatars
        setTimeout('resizeForDocumentHeight(10)', 200);
      }

      // Online status
      if (stringToNumber(actionHandler.getCdata('online_status', 0, profile_data))>=0) {
        $('profile_online_status').innerHTML=htmlspecialchars(getLng('user_is_logged_in')).split('[USER]').join('<b>'+coloredToHTML(nickname)+'</b>');
      } else {
        $('profile_online_status').innerHTML=htmlspecialchars(getLng('user_is_not_logged_in')).split('[USER]').join('<b>'+coloredToHTML(nickname)+'</b>');
      }
      // "Invite" button
      if (actionHandler.getCdata('invitable', 0, profile_data)=='1') {
        $('send_pm_button').style.display='';
        $('invite_button').title=htmlspecialchars(getLng('invite_user_to_your_room')).split('[USER]').join(coloredToPlain(nickname, false));
        $('invite_button').tgt_user_id=user_id;
        $('invite_button').onclick=function() {
          sendInvitation(this.tgt_user_id);
        }
      }

      var profile_rows=new Array('gender',
                                 'homepage',
                                 'age',
                                 'icq',
                                 'msn',
                                 'aim',
                                 'yim',
                                 'location',
                                 'occupation',
                                 'interests'
                                 );
      var data_field=null;
      for (var field in profile_rows) {
        data_field=actionHandler.getCdata(profile_rows[field], 0, profile_data);
        if (data_field==null || profile_rows[field]=='gender' && data_field=='-') {
          // No data
          try {
            $('profile_'+profile_rows[field]+'_row').style.display='none';
          } catch (e) {}
        } else {
          // Display data
          if (profile_rows[field]=='gender') {
            // Gender
            $('gender_image').src='./pic/gender_'+data_field+'_10x10.gif';
            $('gender_image').title=getLng('gender_'+data_field);
          } else if (profile_rows[field]=='homepage') {
            // Homepage
            $('profile_homepage').innerHTML='<a href="'+formlink+'?external_url='+urlencode(data_field)+'" target="_blank" title="'+htmlspecialchars(data_field)+'">'+htmlspecialchars(data_field)+'</a>';
          } else {
            // All other fields
            $('profile_'+profile_rows[field]).innerHTML=htmlspecialchars(data_field);
          }
        }
      }

      // Display table
      $('profile_table').style.display='';
      $('close_window_btn_tbl').style.display='';
    }
  }
  toggleProgressBar(false);
}
