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
 * Accept invitation
 * @param   int   room_id   Room ID
 */
function acceptInvitation(room_id) {
  sendData('_CALLBACK_acceptInvitation()', formlink, 'POST', 'ajax=enter_chat_room&s_id='+urlencode(s_id)+'&room_id='+urlencode(room_id));
}
function _CALLBACK_acceptInvitation() {
  var dummy_form=$('dummyform', opener.document);
  if (actionHandler.status==0) {
    // Room changed. Load room page.
    dummy_form.s_id.value=s_id;
    dummy_form.inc.value='chat_room';
    dummy_form.ts.value=unixTimeStamp();
    dummy_form.submit();
  }
  window.close();
}
