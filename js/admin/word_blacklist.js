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
 * Init window
 */
function initWordBlacklistForm() {
  // Get filtered words
  getFilteredWords();
  // Init "Add word" form
  initAddBadWordForm();
}


/**
 * Get filtered IP addresses
 */
function getFilteredWords() {
  sendData('_CALLBACK_getFilteredWords()', formlink, 'POST', 'ajax='+urlencode('get_filtered_words')+'&s_id='+urlencode(s_id));
}
function _CALLBACK_getFilteredWords() {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');

  var word=null;
  var word_nr=0;
  var word_id=0;

  var words_tbl=null;
  var tr=null;
  var td=null;

  if (status=='-1') {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (message=='OK') {
      // OK
      words_tbl=$('words_tbl');
      // Clear table
      for (var i=words_tbl.rows.length-1; i>1; i--) {
        words_tbl.deleteRow(i);
      }
      while (null!=(word=actionHandler.getElement('word', word_nr++))) {
        word_id=stringToNumber(actionHandler.getCdata('id', 0, word));
        tr=words_tbl.insertRow(-1);

        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars(actionHandler.getCdata('word', 0, word));
        setCssClass(td, '.tbl_row');

        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars(actionHandler.getCdata('replacement', 0, word));
        setCssClass(td, '.tbl_row');

        td=tr.insertCell(-1);
        td.innerHTML='<a href=":" onclick="deleteFilteredWord('+htmlspecialchars(word_id)+'); return false;" title="'+htmlspecialchars(getLng('delete'))+'">'+htmlspecialchars(getLng('delete'))+'</a>';
        setCssClass(td, '.tbl_row');

      }
    } else if (message!=null) {
      alert(message);
    }
  }
  toggleProgressBar(false);
}


/**
 * Init "Add new word" form
 */
function initAddBadWordForm() {
  $('new_word_word').value='';
  $('new_word_replacement').value='';
}


/**
 * Add new word to the filter
 */
function addBadWord() {
  var errors=new Array();
  $('new_word_word').value=trimString($('new_word_word').value);
  $('new_word_replacement').value=trimString($('new_word_replacement').value);
  if ($('new_word_word').value=='') {
    errors.push(getLng('word_empty_error'));
  }

  if (errors.length>0) {
    alert(errors.join("\n"));
  } else {
    // Send data to server
    sendData('_CALLBACK_addBadWord()', formlink, 'POST', 'ajax='+urlencode('add_filtered_word')+'&s_id='+urlencode(s_id)
             +'&word='+urlencode($('new_word_word').value)
             +'&replacement='+urlencode($('new_word_replacement').value)
             );

  }
  return false;
}
function _CALLBACK_addBadWord() {
//alert(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  if (message!=null) {
    alert(message);
  }
  toggleProgressBar(false);
  if (status=='0') {
    getFilteredWords();
    initAddBadWordForm();
  }
}

/**
 * Delete word from filter
 * @param   int   word_id   Word ID
 */
function deleteFilteredWord(word_id) {
  if (typeof(word_id)=='string') {
    word_id=stringToNumber(word_id);
  }
  if (typeof(word_id)=='number' && word_id>0 && confirm(getLng('confirm_delete_word'))) {
    sendData('_CALLBACK_deleteFilteredWord()', formlink, 'POST', 'ajax='+urlencode('delete_filtered_word')
                                                                +'&s_id='+urlencode(s_id)
                                                                +'&word_id='+urlencode(word_id)
                                                                );
  }
  return false;
}
function _CALLBACK_deleteFilteredWord() {
//alert(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  if (message!=null) {
    alert(message);
  }
  toggleProgressBar(false);
  getFilteredWords();
}
