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
function initDisallowNamesForm() {
  // Get disallowed names
  getDisallowedNames();
  // Init "Add name" form
//  initAddDisallowedNameForm();
}


/**
 * Get filtered IP addresses
 */
function getDisallowedNames() {
  sendData('_CALLBACK_getDisallowedNames()', formlink, 'POST', 'ajax=get_disallowed_names&s_id='+urlencode(s_id));
}
function _CALLBACK_getDisallowedNames() {
//debug(actionHandler.getResponseString()); return false;
  var name=null;
  var name_nr=0;
  var name_id=0;
  var names_tbl=null;
  var tr=null;
  var td=null;

  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (actionHandler.message=='OK') {
      // OK
      names_tbl=$('names_tbl');
      // Clear table
      for (var i=names_tbl.rows.length-1; i>1; i--) {
        names_tbl.deleteRow(i);
      }
      for (name_nr=0; name_nr<actionHandler.data['name'].length; name_nr++) {
        name=actionHandler.data['name'][name_nr];
        name_id=stringToNumber(name['id'][0]);
        tr=names_tbl.insertRow(-1);

        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars(name['name'][0]);
        setCssClass(td, '.tbl_row');

        td=tr.insertCell(-1);
        td.innerHTML='<a href=":" onclick="deleteDisallowedName('+htmlspecialchars(name_id)+'); return false;" title="'+htmlspecialchars(getLng('delete'))+'">'+htmlspecialchars(getLng('delete'))+'</a>';
        setCssClass(td, '.tbl_row');

      }
    } else {
      alert(actionHandler.message);
    }
  }
  toggleProgressBar(false);
}


/**
 * Init "Add new name" form
 */
function initAddDisallowedNameForm() {
  $('new_name_name').value='';
}


/**
 * Add new name to the filter
 */
function addDisallowedName() {
  var errors=new Array();
  $('new_name_name').value=trimString($('new_name_name').value);
  if ($('new_name_name').value=='') {
    errors.push(getLng('name_empty_error'));
  }

  if (errors.length>0) {
    alert(errors.join("\n"));
  } else {
    // Send data to server
    sendData('_CALLBACK_addDisallowedName()', formlink, 'POST', 'ajax=add_disallowed_name&s_id='+urlencode(s_id)
             +'&name='+urlencode($('new_name_name').value)
             );

  }
  return false;
}
function _CALLBACK_addDisallowedName() {
//alert(actionHandler.getResponseString()); return false;
  alert(actionHandler.message);
  toggleProgressBar(false);
  if (actionHandler.status==0) {
    getDisallowedNames();
    initAddDisallowedNameForm();
  }
}

/**
 * Delete name from filter
 * @param   int       name_id     Name ID
 * @param   boolean   confirmed   Optional. If TRUE: no confirmation will be displayed. Default: FALSE.
 */
function deleteDisallowedName(name_id, confirmed) {
  if (typeof(name_id)=='string') {
    name_id=stringToNumber(name_id);
  }
  if (typeof(name_id)=='number' && name_id>0) {
    if (typeof(confirmed)!='boolean' || !confirmed) {
      confirm(getLng('confirm_delete_name'), null, null, 'deleteDisallowedName('+name_id+', true)');
    } else {
      sendData('_CALLBACK_deleteDisallowedName()', formlink, 'POST', 'ajax=delete_disallowed_name'
                                                                            +'&s_id='+urlencode(s_id)
                                                                            +'&name_id='+urlencode(name_id)
                                                                            );
    }
  }
  return false;
}
function _CALLBACK_deleteDisallowedName() {
//alert(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  alert(actionHandler.message, null, null, 'getDisallowedNames()');
}
