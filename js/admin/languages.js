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
 * @param   string    group   Settings group
 */
function initLanguagesPage() {
  // Get languages
  getAvailableLanguages('showLanguages()', true, true);
}

/**
 * Display languages
 */
function showLanguages() {
  var tr=null;
  var td=null;
  var lng_tbl=$('languages_tbl');

  hideNewLanguageForm();
  while (lng_tbl.rows.length>3) {
    lng_tbl.deleteRow(lng_tbl.rows.length-2);
  }
  for (var i in AvailableLanguages) {
    tr=lng_tbl.insertRow(lng_tbl.rows.length-1);

    // "Edit" and "Delete" links
    td=tr.insertCell(-1);
    td.innerHTML='<img src="./pic/clearpixel_1x1.gif" alt="" width="7" height="1" />'
                +'<img src="./pic/edit_13x13.gif" alt="'+htmlspecialchars(getLng('edit'))+'" title="'+htmlspecialchars(getLng('edit'))+'" style="cursor:pointer" onclick="showEditLanguageForm('+htmlspecialchars(AvailableLanguages[i].ID)+')" />'
                +'<img src="./pic/clearpixel_1x1.gif" alt="" width="10" height="1" />'
                +'<img src="./pic/delete_13x13.gif" alt="'+htmlspecialchars(getLng('delete'))+'" title="'+htmlspecialchars(getLng('delete'))+'" style="cursor:pointer" onclick="deleteLanguage('+htmlspecialchars(AvailableLanguages[i].ID)+')" />'
                ;
    setCssClass(td, '.tbl_row');

    // Language name
    td=tr.insertCell(-1);
    td.innerHTML=htmlspecialchars(AvailableLanguages[i].Name+' ('+AvailableLanguages[i].LocalName+')');
    setCssClass(td, '.tbl_row');

    // Active
    td=tr.insertCell(-1);
    td.innerHTML=htmlspecialchars(AvailableLanguages[i].Active=='y'? getLng('yes') : getLng('no'));
    setCssClass(td, '.tbl_row');
    td.style.textAlign='center';

    // "Download" link
    td=tr.insertCell(-1);
    td.innerHTML='<a href="'+formlink+'?s_id='+urlencode(s_id)+'&ainc=languages&download_language='+urlencode(AvailableLanguages[i].ID)+'" title="'+htmlspecialchars(getLng('download_language_file'))+'">'
                +'<img src="./pic/arrow_down_9x11.gif" title="'+htmlspecialchars(getLng('download_language_file'))+'" alt="'+htmlspecialchars(getLng('download_language_file'))+'" />'
                +'</a>';
    setCssClass(td, '.tbl_row');
    td.style.textAlign='center';

  }
  showLanguagesTable();
}

/**
 * Show languages table
 */
function showLanguagesTable() {
  $('languages_tbl').style.display='';
}


/**
 * Hide languages table
 */
function hideLanguages() {
  $('languages_tbl').style.display='none';
}


/**
 * Delete language
 * @param   int       id          Language ID
 * @param   boolean   confirmed   Optional. If TRUE: no confirmation will be displayed. Default: FALSE.
 */
function deleteLanguage(id, confirmed) {
  var lng=null;
  for (var i in AvailableLanguages) {
    if (AvailableLanguages[i].ID==id) {
      lng=AvailableLanguages[i];
    }
  }
  if (lng) {
    if (typeof(confirmed)!='boolean' || !confirmed) {
      confirm(getLng('sure_to_delete_language').split('[LANGUAGE]').join(lng.Name), null, null, 'deleteLanguage('+id+', true)');
    } else {
      sendData('_CALLBACK_deleteLanguage()', formlink, 'POST', 'ajax=delete_language&s_id='+urlencode(s_id)+'&language_id='+urlencode(id));
    }
  }
}
function _CALLBACK_deleteLanguage() {
//debug(actionHandler.getResponseString()); return false;
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    toggleProgressBar(false);
    if (actionHandler.status==0) {
      // Language deleted
      alert(actionHandler.message, 0, 0, 'initLanguagesPage()');
    } else {
      alert(actionHandler.message);
    }
  }
}


/**
 * Display "Add new language" form
 */
function showNewLanguageForm() {
  hideLanguages();
  $('new_language_tbl').style.display='';
}


/**
 * Hide "Add new language" form
 */
function hideNewLanguageForm() {
  $('new_language_tbl').style.display='none';
}


/**
 * Open "Upload new language" window
 */
function showUploadWindow() {
  setTimeout("openWindow(formlink+'?s_id='+s_id+'&inc=upload&f_target=language_file', 'file_upload', 400, 80, false, false, false, false, true);", 150);
}


/**
 * Parse response from "Upload new language" window
 * @param   int       code            Response code
 * @param   string    message         Response message
 * @param   int       binaryfile_id   Binaryfile ID
 * @param   int       width           If file was an image: width
 * @param   int       height          If file was an image: height
 * @param   string    filename        Filename
 */
function parseUploadResponse(code, message, binaryfile_id, width, height, filename) {
  if (typeof(code)!='undefined' && typeof(message)!='undefined') {
    switch (code) {

      case 0:
        // Success
        hideNewLanguageForm();
        alert(message);
        initLanguagesPage()
      break;

      case -1:
        // No file uploaded
        // do nothing ;)
      break;

      default:
        alert(message);
      break;

    }
  }
}


/**
 * Display "Edit language" form
 * @param   int   id    Language ID
 */
function showEditLanguageForm(id) {
  var lng=null;
  var lng_name_sel=null;

  if (typeof(id)=='number') {
    for (var i in AvailableLanguages) {
      if (AvailableLanguages[i].ID==id) {
        lng=AvailableLanguages[i];
        break;
      }
    }
    if (lng) {
      hideLanguages();
      // ID
      $('edit_language_id').value=lng.ID;
      // Make "Language name" selection
      lng_name_sel=$('edit_language_iso_name');
      lng_name_sel.options.length=0;
      for (var i in AvailableLanguageNames) {
        lng_name_sel.options[lng_name_sel.options.length]=new Option(AvailableLanguageNames[i].Name, i);
      }
      lng_name_sel.value=lng.ISO_Name;
      // Local name
      $('edit_language_local_name').value=lng.LocalName;
      // Active
      $('edit_language_active_'+lng.Active).click();
    }
  }
  $('edit_language_tbl').style.display='';
}


/**
 * Hide "Edit language" form
 */
function hideEditLanguageForm() {
  $('edit_language_tbl').style.display='none';
}


/**
 * Save language
 */
function saveLanguage() {
  var id=stringToNumber($('edit_language_id').value);
  if (id>0) {
    $('edit_language_local_name').value=trimString($('edit_language_local_name').value);
    if ($('edit_language_local_name').value=='') {
      $('edit_language_local_name').value=AvailableLanguageNames[$('edit_language_iso_name').value].Name;
    }
    // Send data to server
    $('edit_language_tbl').style.display='none';
    sendData('_CALLBACK_saveLanguage()', formlink, 'POST', 'ajax=update_language&s_id='+urlencode(s_id)
             +'&language_id='+urlencode(id)
             +'&iso_name='+urlencode($('edit_language_iso_name').value)
             +'&local_name='+urlencode($('edit_language_local_name').value)
             +'&active='+urlencode($('edit_language_active_y').checked? 'y' : 'n')
             );
  }
}
function _CALLBACK_saveLanguage() {
//alert(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  alert(actionHandler.message);
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else if (actionHandler.status==0) {
    // Language updated
    hideEditLanguageForm();
    initLanguagesPage();
  } else {
    $('edit_language_tbl').style.display='';
  }
}