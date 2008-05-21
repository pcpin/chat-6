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
 * Init window
 */
function initAvatarsForm() {
  // Get avatars
  getAvatars();
  // Show avatars table
  $('avatars_tbl').style.display='';
  $('new_avatar_tbl').style.display='';
}


/**
 * Get avatars
 */
function getAvatars() {
  sendData('_CALLBACK_getAvatars()', formlink, 'POST', 'ajax=get_avatars_gallery&s_id='+urlencode(s_id));
}
function _CALLBACK_getAvatars() {
//debug(actionHandler.getResponseString()); return false;
  var avatar=null;
  var avatar_nr=0;
  var avatar_id=0;
  var avatar_binaryfile_id='';
  var avatar_primary='';

  var avatars_tbl=null;
  var tr=null;
  var td=null;
  var td_nr=0;

  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (actionHandler.message=='OK') {
      // OK
      avatars_tbl=$('avatars_tbl');
      // Clear table
      while (avatars_tbl.rows.length>1) {
        avatars_tbl.deleteRow(-1);
      }
      for (avatar_nr=0; avatar_nr<actionHandler.data['avatar'].length; avatar_nr++) {
        avatar=actionHandler.data['avatar'][avatar_nr];
        avatar_id=stringToNumber(avatar['id'][0]);
        avatar_binaryfile_id=avatar['binaryfile_id'][0];
        avatar_primary='y'==avatar['primary'][0];
        if (td_nr==0) {
          tr=avatars_tbl.insertRow(-1);
        }
        td=tr.insertCell(-1);

        td.innerHTML='<img id="avatar_img_'+htmlspecialchars(avatar_id)+'" src="'+htmlspecialchars(formlink)+'?b_id='+htmlspecialchars(avatar_binaryfile_id)+'&amp;s_id='+htmlspecialchars(s_id)+'&amp;b_x=100&amp;b_y=85" border="0" alt="'+htmlspecialchars(getLng('avatar'))+'" title="'+htmlspecialchars(getLng('avatar'))+'" style="cursor:pointer" />'
                    +'<br />'
                    +'<label for="avatar_primary_'+htmlspecialchars(avatar_id)+'" title="'+htmlspecialchars(getLng('primary'))+'">'
                    +'<input type="radio" name="avatar_primary" id="avatar_primary_'+htmlspecialchars(avatar_id)+'" onclick="setPrimaryAvatar('+htmlspecialchars(avatar_id)+')"; return false;" '+(avatar['primary'][0]=='y'? 'checked="checked"' : '')+'>'
                    +'&nbsp;'+htmlspecialchars(getLng('primary'))
                    +'</label>'
                    +'<br />'
                    +'<a href="." title="'+htmlspecialchars(getLng('delete_avatar'))+'" onclick="deleteAvatar('+htmlspecialchars(avatar_id)+'); return false;">'
                    +htmlspecialchars(getLng('delete_avatar'))
                    +'</a>'
                    +'<br /><br />'
                    ;
        setCssClass(td, '.tbl_row');
        td.style.textAlign='center';
        $('avatar_img_'+avatar_id).binaryfile_id=avatar_binaryfile_id;
        $('avatar_img_'+avatar_id).ow_width=stringToNumber(avatar['width'][0])+10;
        $('avatar_img_'+avatar_id).ow_height=stringToNumber(avatar['height'][0])+10;
        $('avatar_img_'+avatar_id).onclick=function() {
          openWindow(mainFormlink+'?inc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
          return false;
        };

        if (++td_nr==4) {
          td_nr=0;
        }
      }
      if (td_nr>0) {
        while (td_nr++<4) {
          td=tr.insertCell(-1);
          td.innerHTML='&nbsp;';
          setCssClass(td, '.tbl_row');
        }
      }
    } else {
      alert(actionHandler.message);
    }
  }
  toggleProgressBar(false);
}

/**
 * Display "Upload avatar image" window
 */
function showAvatarImageUploadForm() {
  setTimeout("openWindow(formlink+'?s_id='+s_id+'&ainc=upload&f_target=avatar_gallery_image', 'file_upload', 400, 80, false, false, false, false, true);", 150);
}

/**
 * Parse response from "Avatar image upload" window
 * @param   int       code            Response code
 * @param   string    message         Response message
 * @param   int       binaryfile_id   Binaryfile ID
 * @param   int       width           If file was an image: width
 * @param   int       height          If file was an image: height
 * @param   string    filename        Filename
 */
function parseUploadResponse(code, message, binaryfile_id, width, height, filename) {
  if (typeof(code)!='undefined' && typeof(message)!='undefined') {
    switch (code) {

      case 0:
        // Success
        $('avatar_image').innerHTML='<img src="'+htmlspecialchars(formlink)+'?s_id='+htmlspecialchars(s_id)+'&amp;b_id='+htmlspecialchars(binaryfile_id)+'" alt="'+htmlspecialchars(filename)+'" title="'+htmlspecialchars(filename)+'" border="0" />';
        $('avatar_image').title=filename;
        $('avatar_image').binaryfile_id=binaryfile_id;
        $('upload_image_link').style.display='none';
        $('delete_image_link').style.display='';
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
 * "Delete" uploaded avatar image
 */
function deleteAvatarImage() {
  $('avatar_image').innerHTML='';
  $('avatar_image').binaryfile_id=0;
  $('delete_image_link').style.display='none';
  $('upload_image_link').style.display='';
}

/**
 * Add new avatar
 */
function addNewAvatar() {
  var errors=new Array();

  if (stringToNumber($('avatar_image').binaryfile_id)==0) {
    errors.push(getLng('avatar_image_empty_error'));
  }

  if (errors.length>0) {
    alert(errors.join("\n"));
  } else {
    // Send data to server
    sendData('_CALLBACK_addNewAvatar()', formlink, 'POST', 'ajax=add_avatar_gallery&s_id='+urlencode(s_id));

  }
  return false;
}
function _CALLBACK_addNewAvatar() {
//alert(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  alert(actionHandler.message);
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else if (actionHandler.status==0) {
    $('avatar_image').innerHTML='';
    $('avatar_image').binaryfile_id=0;
    $('delete_image_link').style.display='none';
    $('upload_image_link').style.display='';
    getAvatars();
  }
}

/**
 * Delete avatar
 * @param   int       avatar_id   Avatar ID
 * @param   boolean   confirmed   Optional. If TRUE: no confirmation will be displayed. Default: FALSE.
 */
function deleteAvatar(avatar_id, confirmed) {
  if (typeof(avatar_id)=='string') {
    avatar_id=stringToNumber(avatar_id);
  }
  if (avatar_id>0) {
    if (typeof(confirmed)!='boolean' || !confirmed) {
      confirm(getLng('confirm_delete_avatar'), null, null, 'deleteAvatar('+avatar_id+', true)');
    } else {
      sendData('_CALLBACK_deleteAvatar()', formlink, 'POST', 'ajax=delete_avatar_gallery&s_id='+urlencode(s_id)+'&avatar_id='+urlencode(avatar_id));
    }
  }
  return false;
}
function _CALLBACK_deleteAvatar() {
  toggleProgressBar(false);
  alert(actionHandler.message);
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
  } else if (actionHandler.status==0) {
    getAvatars();
  }
}


/**
 * Set new primary avatar
 * @param   int   id    Avatar ID
 */
function setPrimaryAvatar(id) {
  sendData('toggleProgressBar(false)', formlink, 'POST', 'ajax=set_primary_avatar_gallery&s_id='+urlencode(s_id)+'&avatar_id='+urlencode(id));
}
