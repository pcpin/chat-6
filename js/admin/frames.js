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
 * Init frameset
 */
function initAdminFames() {
  window.appName_='pcpin_chat';
  // Set "onunload" handler
  window.onunload=function() {
    // Send "Page unloaded" signal to server
    if (!SkipPageUnloadedMsg && (typeof(window.opener)=='undefined' || typeof(window.opener.appName_)!='string' || window.opener.appName_!='pcpin_chat' || typeof(window.opener.initChatRoom)=='undefined')) {
      openWindow(mainFormlink+'?inc='+urlencode('page_unloaded')+'&s_id='+urlencode(s_id), '', 1, 1, false, false, false, false, false, false, false, false, false, false, 0, 0);
    }
  }
}
