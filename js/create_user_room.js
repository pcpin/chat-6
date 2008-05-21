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
 * Category ID
 * @var int
 */
var categoryId=0;


/**
 * Initialize form
 * @param   int       category_id       Category ID
 */
function initNewuserRoomForm(category_id) {
  if (typeof(category_id)!='number') {
    window.close();
  } else {
    categoryId=category_id;
    window.onclose=function() {
      opener.newUserRoomWindow=null;
      try {
        if (uploadWindow) {
          uploadWindow.close();
        }
      } catch (e) {}
    }
    opener.newUserRoomWindow=window;
    // Get focus
    window.focus();
    $('room_name').focus();
  }
  window.onfocus=function() {
    try {
      if (uploadWindow) {
        uploadWindow.focus();
      }
    } catch (e) {}
  }
}

/**
 * Show/hide room password fields
 */
function togglePasswordFields() {
  if ($('room_password_protected').value=='0') {
    $('room_password_fields').style.display='none';
  } else {
    $('room_password_fields').style.display='';
    $('room_password_1').value='';
    $('room_password_2').value='';
  }
}


/**
 * Display "Upload room image" window
 */
function showRoomImageUploadForm() {
  setTimeout("openWindow(formlink+'?s_id='+s_id+'&inc=upload&f_target=room_image', 'file_upload', 400, 80, false, false, false, false, true);", 150);
}

/**
 * Parse response from "Upload room image" window
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
        if (true==ImgResizeSupported) {
          $('background_image').innerHTML='<img src="'+htmlspecialchars(formlink)+'?s_id='+htmlspecialchars(s_id)+'&amp;b_id='+htmlspecialchars(binaryfile_id)+'&amp;b_x=30&amp;b_y=40" alt="'+htmlspecialchars(filename)+'" title="'+htmlspecialchars(filename)+'" border="0" />';
          $('background_image').title=filename;
          $('background_image').binaryfile_id=binaryfile_id;
          $('background_image').ow_width=width;
          $('background_image').ow_height=height;
          $('background_image').onclick=function() {
            openWindow(formlink+'?inc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
            return false;
          }
        } else {
          $('background_image').innerHTML=htmlspecialchars(htmlspecialchars(filename))+'<br />';
          $('background_image').title=filename;
          $('background_image').binaryfile_id=binaryfile_id;
          $('background_image').ow_width=width;
          $('background_image').ow_height=height;
          $('background_image').onclick=function() {
            openWindow(formlink+'?inc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
            return false;
          }
        }
        $('delete_image_link').style.display='';
        $('upload_image_link').style.display='none';
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
 * "Delete" uploaded image
 */
function deleteRoomImage() {
  $('background_image').innerHTML='';
  $('delete_image_link').style.display='none';
  $('upload_image_link').style.display='';
}


/**
 * Validate form and create room
 */
function createRoom() {
  var errors=new Array();
  $('room_name').value=trimString($('room_name').value);
  $('room_description').value=trimString($('room_description').value);
  if ($('room_name').value.length==0) {
    // Room name empty
    errors.push(getLng('room_name_empty'));
  }
  if ($('room_password_protected').value=='1') {
    if ($('room_password_1').value!=$('room_password_2').value) {
      // Passwords are not ident
      errors.push(getLng('passwords_not_ident'));
    } else if ($('room_password_1').value.length<3) {
      // Password too short
      errors.push(getLng('password_too_short'));
    }
  } else {
    $('room_password_1').value='';
    $('room_password_2').value='';
  }
  if (errors.length>0) {
    alert(errors.join("\n"));
  } else {
    sendData('_CALLBACK_createRoom(\''+base64encode($('room_password_1').value)+'\')', formlink, 'POST', 'ajax=create_user_room&s_id='+urlencode(s_id)+'&category_id='+urlencode(categoryId)+'&name='+urlencode($('room_name').value)+'&description='+urlencode($('room_description').value)+'&password_protect='+urlencode($('room_password_protected').value)+'&password='+urlencode(base64encode($('room_password_1').value))+'&image='+($('background_image').value!=''? '1' : '0'));
  }
}
function _CALLBACK_createRoom(password) {
  toggleProgressBar(false);
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else if (actionHandler.status==0) {
    // Room created
    opener.ActiveRoomId=stringToNumber(actionHandler.data['room_id'][0]);
    opener.enterChatRoom(base64decode(password));
    window.close();
  } else {
     alert(actionHandler.message);
  }
}
