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
 * Callback function to execute after alert box disappears.
 */
var alertboxCallback='';


/**
 * Display alert box
 * @param   string    $text         Text to display
 * @param   int       top_offset    Optional. How many pixels to add to the top position. Can be negative or positive.
 * @param   int       left_offset   Optional. How many pixels to add to the left position. Can be negative or positive.
 * @param   string    callback      Optional. Callback function to execute after alert box disappears.
 */
function alert(text, top_offset, left_offset, callback) {
  if (typeof(text)=='string') {
    if (typeof(top_offset)!='number') top_offset=0;
    if (typeof(left_offset)!='number') left_offset=0;
    $('alertbox_text').innerHTML=nl2br(htmlspecialchars(text));
    $('alertbox').style.display='';
    setTimeout("moveToCenter($('alertbox'), "+top_offset+", "+left_offset+")", 25);
    if (typeof(callback)=='string') {
      alertboxCallback=callback;
    } else {
      alertboxCallback='';
    }
  }
}


/**
 * Hide alert box
 */
function hideAlertBox() {
  $('alertbox').style.display='none';
  if (alertboxCallback!='') {
    eval('try { '+alertboxCallback+' } catch(e) {}');
  }
}
