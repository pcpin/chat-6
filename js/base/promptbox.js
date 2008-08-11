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
 * Currently active input field (text or password) handler
 * @var object
 */
var promptboxInputField='';

/**
 * Callback function to execute after prompt box receives "OK".
 */
var promptboxCallback='';

/**
 * Value of the prompt box after submit
 */
var promptboxValue='';


/**
 * Display prompt box
 * @param   string    text            Text to display
 * @param   int       default_value   Optional. Default value.
 * @param   int       top_offset      Optional. How many pixels to add to the top position. Can be negative or positive.
 * @param   int       left_offset     Optional. How many pixels to add to the left position. Can be negative or positive.
 * @param   string    callback        Optional. Callback function to execute after prompt box receives "OK".
 * @param   boolean   password        Optional. If TRUE, then password field will be displayed instead of text field. Default: FALSE.
 */
function prompt(text, default_value, top_offset, left_offset, callback, password) {
  if (typeof(text)!='undefined' && typeof(text)!='string') {
    try {
      text=text.toString();
    } catch (e) {}
  }
  if (typeof(text)=='string') {
    document.onkeyup_promptbox=document.onkeyup;
    document.onkeyup=function(e) {
      switch (getKC(e)) {
        case 27:
          hidePromptBox();
          break;
      }
    };
    if (typeof(top_offset)!='number') top_offset=0;
    if (typeof(left_offset)!='number') left_offset=0;
    if (typeof(default_value)!='string') default_value='';
    $('promptbox_text').innerHTML=nl2br(htmlspecialchars(text));
    $('promptbox_input').style.display='none';
    $('promptbox_input_password').style.display='none';
    if (typeof(password)=='boolean' && password) {
      promptboxInputField=$('promptbox_input_password');
      promptboxInputField.value='';
    } else {
      promptboxInputField=$('promptbox_input');
      promptboxInputField.value=default_value;
    }
    promptboxInputField.style.display='';
    $('promptbox').style.display='';
    setTimeout("moveToCenter($('promptbox'), "+top_offset+", "+left_offset+"); promptboxInputField.select(); promptboxInputField.focus();", 25);
    if (typeof(callback)=='string') {
      promptboxCallback=callback;
    } else {
      promptboxCallback='';
    }
    setTimeout("$('promptbox').style.display='none'; $('promptbox').style.display='';", 200);
  }
}


/**
 * Hide prompt box
 @param   boolean   ok    TRUE, if "OK" button was clicked
 */
function hidePromptBox(ok) {
  document.onkeyup=document.onkeyup_promptbox;
  $('promptbox').style.display='none';
  if (typeof(ok)=='boolean' && ok) {
    promptboxValue=promptboxInputField.value;
    if (promptboxCallback!='') {
      eval('try { '+promptboxCallback+' } catch(e) {}');
    }
  }
}
