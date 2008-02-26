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
function initAbuseWindow() {

  var abuse_data=null;

  // Get abuse data
  if (!window.opener || !window.opener.getAbuseData || null==(abuse_data=window.opener.getAbuseData(window, stringToNumber(window.name.substring(6))))) {
    window.close();
  }

  // Display data
  $('abuse_date').innerHTML=htmlspecialchars(abuse_data['date']);
  $('abuse_room').innerHTML=htmlspecialchars(abuse_data['room_name']);
  $('abuse_author').innerHTML=coloredToHTML(abuse_data['author_nickname']);
  $('abuse_category').innerHTML=htmlspecialchars(abuse_data['category']);
  $('abuse_abuser_nickname').innerHTML=htmlspecialchars(abuse_data['abuser_nickname']);
  $('abuse_description').innerHTML=nl2br(htmlspecialchars(abuse_data['description']));
  $('abuse_data_table').style.display='';

  $('enter_room_btn').innerHTML=htmlspecialchars(getLng('enter_chat_room').split('[ROOM]').join(abuse_data['room_name']));
  $('enter_room_btn').title=$('enter_room_btn').innerHTML;
  $('enter_room_btn').abuse_room_id=stringToNumber(abuse_data['room_id']);
  $('enter_room_btn').onclick=function() {
    if (window.opener.enterChatRoom) {
      window.opener.enterChatRoom(null, null, this.abuse_room_id);
    } else if (window.opener.switchChatRoom) {
      window.opener.switchChatRoom(this.abuse_room_id);
    }
    this.disabled=true;
  }

  // Resize window
  setTimeout('resizeForDocumentHeight(10)', 100);
  // Get focus
  window.focus();
}

