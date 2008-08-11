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
 * Callback function to execute after confirm box receives "OK".
 */
var confirmboxCallback='';


/**
 * Display confirm box
 * @param   string    $text         Text to display
 * @param   int       top_offset    Optional. How many pixels to add to the top position. Can be negative or positive.
 * @param   int       left_offset   Optional. How many pixels to add to the left position. Can be negative or positive.
 * @param   string    callback      Optional. Callback function to execute after confirm box receives "OK".
 */
function confirm(text, top_offset, left_offset, callback) {
  if (typeof(text)!='undefined' && typeof(text)!='string') {
    try {
      text=text.toString();
    } catch (e) {}
  }
  if (typeof(text)=='string') {
    document.onkeyup_confirmbox=document.onkeyup;
    document.onkeyup=function(e) {
      switch (getKC(e)) {
        case 27:
          hideConfirmBox(false);
          break;
      }
    };
    if (typeof(top_offset)!='number') top_offset=0;
    if (typeof(left_offset)!='number') left_offset=0;
    $('confirmbox_text').innerHTML=nl2br(htmlspecialchars(text));
    $('confirmbox').style.display='';
    $('confirmbox_btn_ok').focus();
    setTimeout("moveToCenter($('confirmbox'), "+top_offset+", "+left_offset+"); $('confirmbox_btn_ok').focus();", 25);
    if (typeof(callback)=='string') {
      confirmboxCallback=callback;
    } else {
      confirmboxCallback='';
    }
    setTimeout("$('confirmbox').style.display='none'; $('confirmbox').style.display='';", 200);
  }
}


/**
 * Hide confirm box
 @param   boolean   ok    TRUE, if "OK" button was clicked
 */
function hideConfirmBox(ok) {
  document.onkeyup=document.onkeyup_confirmbox;
  $('confirmbox').style.display='none';
  if (typeof(ok)=='boolean' && ok && confirmboxCallback!='') {
    eval('try { '+confirmboxCallback+' } catch(e) {}');
  }
}
