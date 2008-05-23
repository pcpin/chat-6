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
 * ID of currently opened for edit smilie
 * @var int
 */
var editSmilieID=0;


/**
 * Init window
 */
function initSmiliesForm() {
  // Get smilies
  getSmilies();
  // Show smilies table
  $('smilies_tbl').style.display='';
  $('new_smilie_tbl').style.display='';
}


/**
 * Get smilies
 */
function getSmilies() {
  sendData('_CALLBACK_getSmilies()', formlink, 'POST', 'ajax=get_smilies&s_id='+urlencode(s_id));
}
function _CALLBACK_getSmilies() {
//debug(actionHandler.getResponseString()); return false;

  var smilie=null;
  var smilie_nr=0;
  var smilie_id=0;
  var smilie_code='';
  var smilie_binaryfile_id='';
  var smilie_description='';

  var smilies_tbl=null;
  var tr=null;
  var td=null;
  var td_nr=0;

  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (actionHandler.message=='OK') {
      // OK
      smilies_tbl=$('smilies_tbl');
      // Clear table
      while (smilies_tbl.rows.length>1) {
        smilies_tbl.deleteRow(-1);
      }
      for (smilie_nr=0; smilie_nr<actionHandler.data['smilie'].length; smilie_nr++) {
        smilie=actionHandler.data['smilie'][smilie_nr];
        smilie_id=stringToNumber(smilie['id'][0]);
        smilie_code=smilie['code'][0];
        smilie_binaryfile_id=smilie['binaryfile_id'][0];
        smilie_description=smilie['description'][0];
        if (td_nr==0) {
          tr=smilies_tbl.insertRow(-1);
        }
        td=tr.insertCell(-1);
        td.innerHTML='<img src="'+htmlspecialchars(formlink)+'?b_id='+htmlspecialchars(smilie_binaryfile_id)+'" alt="'+htmlspecialchars(smilie_description)+'" title="'+htmlspecialchars(smilie_description)+'" border="0" />'
                    +'<br />'
                    +'<table border="0" cellspacing="0" cellpadding="3">'
                    +'<tr>'
                    +'<td class="tbl_row" width="50%" style="text-align:right"><b>'+htmlspecialchars(getLng('code'))+':</b></td>'
                    +'<td class="tbl_row">'
                    +'<input type="hidden" id="original_smilie_code_'+htmlspecialchars(smilie_id)+'" value="'+htmlspecialchars(smilie_code)+'" />'
                    +'<span id="smilie_code_'+htmlspecialchars(smilie_id)+'">'+htmlspecialchars(smilie_code)+'</span>'
                    +'<input style="display:none" id="edit_smilie_code_'+htmlspecialchars(smilie_id)+'" type="text" size="6" maxlength="32" value="" title="'+htmlspecialchars(getLng('code'))+'" />'
                    +'</td>'
                    +'</tr>'
                    +'<tr>'
                    +'<td class="tbl_row" style="text-align:right"><b>'+htmlspecialchars(getLng('description'))+':</b></td>'
                    +'<td class="tbl_row">'
                    +'<input type="hidden" id="original_smilie_description_'+htmlspecialchars(smilie_id)+'" value="'+htmlspecialchars(smilie_description)+'" />'
                    +'<span id="smilie_description_'+htmlspecialchars(smilie_id)+'">'+htmlspecialchars(smilie_description)+'</span>'
                    +'<input style="display:none" id="edit_smilie_description_'+htmlspecialchars(smilie_id)+'" type="text" size="16" maxlength="255" value="" title="'+htmlspecialchars(getLng('description'))+'" />'
                    +'</td>'
                    +'</tr>'
                    +'</table>'
                    +'<span id="smilie_ctl_links_'+htmlspecialchars(smilie_id)+'">'
                    +'<a href=":" onclick="showEditSmilieForm('+htmlspecialchars(smilie_id)+'); return false;" title="'+htmlspecialchars(getLng('edit'))+'">'+htmlspecialchars(getLng('edit'))+'</a>'
                    +'&nbsp;&nbsp;&nbsp;'
                    +'<a href=":" onclick="deleteSmilie('+htmlspecialchars(smilie_id)+'); return false;" title="'+htmlspecialchars(getLng('delete'))+'">'+htmlspecialchars(getLng('delete'))+'</a>'
                    +'</span>'
                    +'<span id="smilie_edit_ctl_buttons_'+htmlspecialchars(smilie_id)+'" style="display:none">'
                    +'<button type="button" onclick="saveSmilie()" title="'+htmlspecialchars(getLng('save_changes'))+'">'+htmlspecialchars(getLng('save_changes'))+'</button>'
                    +'&nbsp;&nbsp;&nbsp;'
                    +'<button type="button" onclick="hideEditSmilieForm()" title="'+htmlspecialchars(getLng('cancel'))+'">'+htmlspecialchars(getLng('cancel'))+'</button>'
                    +'</span>'
                    +'<br /><br />';
        setCssClass(td, '.tbl_row');
        td.style.textAlign='center';
        if (++td_nr==4) {
          td_nr=0;
        }
      }
      if (td_nr>0) {
        while (td_nr++<4) {
          td=tr.insertCell(-1);
          td.innerHTML='&nbsp;';
          setCssClass(td, '.tbl_row');
        }
      }
    } else {
      alert(actionHandler.message);
    }
  }
  toggleProgressBar(false);
}

/**
 * Display "Upload smilie image" window
 */
function showSmilieImageUploadForm() {
  setTimeout("openWindow(formlink+'?s_id='+s_id+'&ainc=upload&f_target=smilie_image', 'file_upload', 400, 80, false, false, false, false, true);", 150);
}

/**
 * Parse response from "Smilie image upload" window
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
        $('smilie_image').innerHTML='<img src="'+htmlspecialchars(formlink)+'?s_id='+htmlspecialchars(s_id)+'&amp;b_id='+htmlspecialchars(binaryfile_id)+'" alt="'+htmlspecialchars(filename)+'" title="'+htmlspecialchars(filename)+'" border="0" />';
        $('smilie_image').title=filename;
        $('smilie_image').binaryfile_id=binaryfile_id;
        $('upload_image_link').style.display='none';
        $('delete_image_link').style.display='';
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
 * "Delete" uploaded smilie image
 */
function deleteSmilieImage() {
  $('smilie_image').innerHTML='';
  $('smilie_image').binaryfile_id=0;
  $('delete_image_link').style.display='none';
  $('upload_image_link').style.display='';
}

/**
 * Add new smilie
 */
function addNewSmilie() {
  var errors=new Array();
  $('new_smilie_code').value=trimString($('new_smilie_code').value);
  $('new_smilie_description').value=trimString($('new_smilie_description').value);

  if ($('new_smilie_code').value=='') {
    errors.push(getLng('smilie_code_empty_error'));
  }

  if (stringToNumber($('smilie_image').binaryfile_id)==0) {
    errors.push(getLng('smilie_image_empty_error'));
  }

  if (errors.length>0) {
    alert(errors.join("\n"));
  } else {
    // Send data to server
    sendData('_CALLBACK_addNewSmilie()', formlink, 'POST', 'ajax=add_smilie&s_id='+urlencode(s_id)
             +'&code='+urlencode($('new_smilie_code').value)
             +'&description='+urlencode($('new_smilie_description').value)
             );

  }
  return false;
}
function _CALLBACK_addNewSmilie() {
//alert(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  alert(actionHandler.message);
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else if (actionHandler.status==0) {
    $('smilie_image').innerHTML='';
    $('smilie_image').binaryfile_id=0;
    $('delete_image_link').style.display='none';
    $('upload_image_link').style.display='';
    $('new_smilie_code').value='';
    $('new_smilie_description').value='';
    getSmilies();
  }
}

/**
 * Delete smilie
 * @param   int       smilie_id   Smilie ID
 * @param   boolean   confirmed   Optional. If TRUE: no confirmation will be displayed. Default: FALSE.
 */
function deleteSmilie(smilie_id, confirmed) {
  if (typeof(smilie_id)=='string') {
    smilie_id=stringToNumber(smilie_id);
  }
  if (smilie_id>0) {
    if (typeof(confirmed)!='boolean' || !confirmed) {
      confirm(getLng('confirm_delete_smilie'), 0, 0, 'deleteSmilie('+smilie_id+', true)');
    } else {
      sendData('_CALLBACK_deleteSmilie()', formlink, 'POST', 'ajax=delete_smilie&s_id='+urlencode(s_id)+'&smilie_id='+urlencode(smilie_id));
    }
  }
  return false;
}
function _CALLBACK_deleteSmilie() {
//alert(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else if (actionHandler.status==0) {
    alert(actionHandler.message, 0, 0, 'getSmilies()');
  } else {
    alert(actionHandler.message);
  }
}

/**
 * Display smilie edit form
 * @param   int   smilie_id     Smilie ID
 */
function showEditSmilieForm(smilie_id) {
  hideEditSmilieForm();
  editSmilieID=smilie_id;
  $('smilie_ctl_links_'+editSmilieID).style.display='none';
  $('smilie_code_'+editSmilieID).style.display='none';
  $('smilie_description_'+editSmilieID).style.display='none';
  $('smilie_edit_ctl_buttons_'+editSmilieID).style.display='';
  $('edit_smilie_code_'+editSmilieID).style.display='';
  $('edit_smilie_description_'+editSmilieID).style.display='';
  $('edit_smilie_code_'+editSmilieID).value=$('original_smilie_code_'+editSmilieID).value;
  $('edit_smilie_description_'+editSmilieID).value=$('original_smilie_description_'+editSmilieID).value;
}

/**
 * Hide smilie edit form
 */
function hideEditSmilieForm() {
  if (editSmilieID>0) {
    $('smilie_ctl_links_'+editSmilieID).style.display='';
    $('smilie_code_'+editSmilieID).style.display='';
    $('smilie_description_'+editSmilieID).style.display='';
    $('smilie_edit_ctl_buttons_'+editSmilieID).style.display='none';
    $('edit_smilie_code_'+editSmilieID).style.display='none';
    $('edit_smilie_description_'+editSmilieID).style.display='none';
    editSmilieID=0;
  }
}

/**
 * Save smilie changes
 */
function saveSmilie() {
  if (editSmilieID>0) {
    var errors=new Array();
    $('edit_smilie_code_'+editSmilieID).value=trimString($('edit_smilie_code_'+editSmilieID).value);
    $('edit_smilie_description_'+editSmilieID).value=trimString($('edit_smilie_description_'+editSmilieID).value);

    if ($('edit_smilie_code_'+editSmilieID).value=='') {
      errors.push(getLng('smilie_code_empty_error'));
    }
    if (errors.length>0) {
      alert(errors.join("\n"));
    } else {
      // Send data to server
      sendData('_CALLBACK_saveSmilie()', formlink, 'POST', 'ajax=update_smilie&s_id='+urlencode(s_id)
               +'&smilie_id='+urlencode(editSmilieID)
               +'&code='+urlencode($('edit_smilie_code_'+editSmilieID).value)
               +'&description='+urlencode($('edit_smilie_description_'+editSmilieID).value)
               );
  
    }
  }
  return false;
}
function _CALLBACK_saveSmilie() {
//alert(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  alert(actionHandler.message);
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else if (actionHandler.status==0) {
    $('original_smilie_code_'+editSmilieID).value=$('edit_smilie_code_'+editSmilieID).value;
    $('original_smilie_description_'+editSmilieID).value=$('edit_smilie_description_'+editSmilieID).value;
    $('smilie_code_'+editSmilieID).innerHTML=htmlspecialchars($('edit_smilie_code_'+editSmilieID).value);
    $('smilie_description_'+editSmilieID).innerHTML=htmlspecialchars($('edit_smilie_description_'+editSmilieID).value);
    hideEditSmilieForm();
  }
}
