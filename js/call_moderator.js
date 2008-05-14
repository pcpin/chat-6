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
 * Init window box
 */
function initCMBox() {
  if (typeof(window.opener)!='object' || window.opener==null) {
    window.close();
    return false;
  }

  // Set "onunload" handler
  window.onunload=function() {
    try {
      window.opener.moderatorCallWindow=null;
    } catch (e) {}
  }

  // Assign onKeyUp event
  document.onkeyup=function(e) {
    if (getKC(e)==27) {
      window.close();
    }
  };

  // Log by opener window
  opener.moderatorCallWindow=window;

  // Resize window
  setTimeout('resizeForDocumentHeight(10)', 100);
  // Get focus
  window.focus();
}


/**
 * Send moderator call
 */
function sendModeratorCall() {
  var errortext=new Array();
  $('abuse_nickname').value=trimString($('abuse_nickname').value);
  if ($('abuse_nickname').value=='') {
    errortext.push(getLng('abuser_nickname_empty'));
  }
  if ($('abuse_category').selectedIndex==0) {
    errortext.push(getLng('violation_category_not_selected'));
  }

  if (errortext.length>0) {
    alert('- '+errortext.join("\n- "));
  } else {
    sendData('_CALLBACK_sendModeratorCall()',
             formlink,
             'POST',
             'ajax=call_moderator'
             +'&s_id='+urlencode(s_id)
             +'&abuse_nickname='+urlencode($('abuse_nickname').value)
             +'&abuse_category='+urlencode($('abuse_category').value)
             +'&abuse_description='+urlencode($('abuse_description').value)
             );
  }
}
function _CALLBACK_sendModeratorCall() {
  switch (actionHandler.status) {

    case  -1:
      // Session is invalid
      opener.document.location.href=formlink+'?session_timeout';
      window.close();
    break;

    case 0:
      // Data sent
      toggleProgressBar(false);
      alert(actionHandler.message);
      window.close();
    break;

    default:
      // An error occured
      alert(actionHandler.message);
    break;

  }
  toggleProgressBar(false);
}