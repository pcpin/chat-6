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

function executeLogOut(close_when_done) {
  window.focus();
  window.onblur=function() {
    window.focus();
  }
  try {
    if (typeof(window.opener.adminArea_)=='boolean') {
      // Called from Admin area
      if (close_when_done) {
        window.opener.parent.SkipPageUnloadedMsg=true;
        window.opener.parent.close();
      }
      window.opener.parent.document.location.href=exit_url;
    } else {
      // Called from public area
      if (close_when_done) {
        window.opener.SkipPageUnloadedMsg=true;
        window.opener.close();
      }
      try {
        if (opener.mainApp_) {
          opener.mainApp_.SkipPageUnloadedMsg=true;
          opener.mainApp_.document.location.href=exit_url;
        } else {
          opener.SkipPageUnloadedMsg=true;
          opener.document.location.href=exit_url;
        }
      } catch (e) {}
    }
  } catch (e) {}
  sendData('SkipPageUnloadedMsg=true; window.close();', formlink, 'POST', 'ajax=do_logout&s_id='+urlencode(s_id), true, true);
}
