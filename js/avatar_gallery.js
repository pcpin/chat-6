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
 * Initialize
 * @param   boolean   profile_user_id       User ID
 */
function initAvatarGallery(profile_user_id) {
  profileUserId=profile_user_id;
  // Resize window
  setTimeout('resizeForDocumentHeight(10)', 500);
  // Get focus
  window.focus();
}


/**
 * Pick an avatar
 * @param     object    avatar_img    Clicked avatar image
 */
function pickAvatar(avatar_img) {
  if (typeof(avatar_img)=='object' && avatar_img!=null) {
    sendData('_CALLBACK_pickAvatar()', formlink, 'POST', 'ajax=set_avatar_from_gallery'
                                                        +'&s_id='+urlencode(s_id)
                                                        +'&avatar_id='+urlencode(avatar_img.id.substring(15))
                                                        +'&profile_user_id='+urlencode(profileUserId)
                                                        );
  }
}
function _CALLBACK_pickAvatar() {
//debug(actionHandler.getResponseString()); return false;

  if (actionHandler.status==-1) {
    // Session is invalid
    opener.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    window.close();
    return false;
  } else {
    if (actionHandler.status==0) {
      if (window.opener && window.opener.getAvatars) {
        window.opener.getAvatars();
      }
      window.close();
    } else {
      // An error occured
      toggleProgressBar(false);
      alert(actionHandler.message);
    }
  }
}
