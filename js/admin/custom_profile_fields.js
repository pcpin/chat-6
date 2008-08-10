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
 * Profile fields
 * @var object
 */
var ProfileFields=new Array();

/**
 * Init window
 */
function initCustomFieldsWindow() {
  // Load fields
  getProfileFields();
}


/**
 * Get fields
 */
function getProfileFields() {
  sendData('_CALLBACK_getProfileFields()',
           formlink,
           'POST',
           'ajax=get_custom_profile_fields'
           +'&s_id='+urlencode(s_id)
           );
}
function _CALLBACK_getProfileFields() {
//debug(actionHandler.getResponseString()); return false;
  var field=null;

  ProfileFields=new Array();
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (typeof(actionHandler.data['field'])!='undefined' && actionHandler.data['field']) {
      for (var i=0; i<actionHandler.data['field'].length; i++) {
        field=new Array();
        for (var ii in actionHandler.data['field'][i]) {
          field[ii]=actionHandler.data['field'][i][ii][0];
        }
        ProfileFields.push(field);
      }
    }
  }

  // Display fields
  showFields();
  $('fields_tbl').style.display='';
  toggleProgressBar(false);
}


/**
 * Display fields
 */
function showFields() {

  var fields_tbl=$('fields_tbl');
  var field=null;
  var tr=null;
  var td=null;
  var type='';
  var choices=new Array();
  var visibility='';

  hideFieldForm();

  // Clean up table
  if (typeof(fields_tbl.original_rows_count)!='number') {
    fields_tbl.original_rows_count=fields_tbl.rows.length;
  } else {
    while (fields_tbl.rows.length>fields_tbl.original_rows_count) {
      fields_tbl.deleteRow(fields_tbl.rows.length-2);
    }
  }

  // Display fields
  if (ProfileFields.length>0) {
    for (var i=0; i<ProfileFields.length; i++) {
      field=ProfileFields[i];
      type='';
      choices=new Array();
      visibility='';

      switch (field['type']) {

        case 'string':
          type=getLng('single_text_field');
        break;

        case 'text':
          type=getLng('textarea');
        break;

        case 'url':
          type=getLng('url');
        break;

        case 'email':
          type=getLng('email_address');
        break;

        case 'choice':
          type=getLng('simple_choice');
          if (field['name']=='gender' && field['custom']=='n') {
            choices=new Array(getLng('gender_m'), getLng('gender_f'), getLng('gender_-'));
          } else {
            choices=field['choices'].split("\n");
          }
        break;

        case 'multichoice':
          type=getLng('multiple_choice');
          choices=field['choices'].split("\n");
        break;

      }

      switch (field['visibility']) {

        case 'public':
          visibility=getLng('everybody');
        break;

        case 'registered':
          visibility=getLng('registered_users_only');
        break;

        case 'moderator':
          visibility=getLng('moderators_only');
        break;

        case 'admin':
          visibility=getLng('admins_only');
        break;

      }

      tr=fields_tbl.insertRow(fields_tbl.rows.length-1);

      td=tr.insertCell(-1);
      td.innerHTML=(field['custom']=='y'? ('<img src="./pic/edit_13x13.gif" onclick="editField('+field['id']+')" title="'+htmlspecialchars(getLng('edit'))+'" alt="'+htmlspecialchars(getLng('edit'))+'" style="cursor:pointer" />') : '<img src="./pic/clearpixel_1x1.gif" width="13" height="13" alt="" />')
                  +'&nbsp;'
                  +(field['custom']=='y'? ('<img src="./pic/delete_13x13.gif" onclick="deleteField('+field['id']+')" title="'+htmlspecialchars(getLng('delete'))+'" alt="'+htmlspecialchars(getLng('delete'))+'" style="cursor:pointer" />') : '<img src="./pic/clearpixel_1x1.gif" width="13" height="13" alt="" />')
                   ;
      setCssClass(td, '.tbl_row');
      td.style.verticalAlign='top';
      td.style.width='30px';

      td=tr.insertCell(-1);
      td.innerHTML=field['disabled']=='y'? ('<a href="#" class="tbl_header_sub_link" onclick="activateField('+htmlspecialchars(field['id'])+', true); return false;">'+htmlspecialchars(getLng('no'))+'</a>') : ('<a href="#" class="tbl_header_sub_link" onclick="activateField('+htmlspecialchars(field['id'])+', false); return false;">'+htmlspecialchars(getLng('yes'))+'</a>');
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';
      td.style.verticalAlign='top';
      td.noWrap=true;

      td=tr.insertCell(-1);
      td.innerHTML=htmlspecialchars(field['name_translated']);
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';
      td.style.verticalAlign='top';

      td=tr.insertCell(-1);
      td.innerHTML=htmlspecialchars(type);
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';
      td.style.verticalAlign='top';

      td=tr.insertCell(-1);
      td.innerHTML=nl2br(htmlspecialchars(choices.join("\n")));
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';
      td.style.verticalAlign='top';

      td=tr.insertCell(-1);
      td.innerHTML=field['name']=='gender' && field['custom']=='n'? htmlspecialchars(getLng('gender_'+field['default_value'])) : nl2br(htmlspecialchars(field['default_value']));
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';
      td.style.verticalAlign='top';

      td=tr.insertCell(-1);
      td.innerHTML=htmlspecialchars(visibility);
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';
      td.style.verticalAlign='top';

      td=tr.insertCell(-1);
      td.innerHTML=(field['writeable']=='user'? '<a href="#" class="tbl_header_sub_link" onclick="setFieldWriteable('+htmlspecialchars(field['id'])+', \'admin\'); return false;">'+htmlspecialchars(getLng('profile_owner'))+'</a>' : '<a href="#" class="tbl_header_sub_link" onclick="setFieldWriteable('+htmlspecialchars(field['id'])+', \'user\'); return false;">'+htmlspecialchars(getLng('admins_only'))+'</a>')
                  ;
      setCssClass(td, '.tbl_row');
      td.style.textAlign='center';
      td.style.verticalAlign='top';

      td=tr.insertCell(-1);
      td.innerHTML=(i>0? ('<img src="./pic/arrow_up_13x9.gif" onclick="updateFieldOrder('+htmlspecialchars(field['id'])+', false)" title="'+htmlspecialchars(getLng('move_up'))+'" alt="'+htmlspecialchars(getLng('move_up'))+'" onclick="" style="cursor:pointer" />') : '<img src="./pic/clearpixel_1x1.gif" width="13" height="9" alt="" />')
                  +'&nbsp;'
                  +(i<ProfileFields.length-1? ('<img src="./pic/arrow_down_13x9.gif" onclick="updateFieldOrder('+htmlspecialchars(field['id'])+', true)" title="'+htmlspecialchars(getLng('move_down'))+'" alt="'+htmlspecialchars(getLng('move_down'))+'" onclick="" style="cursor:pointer" />') : '<img src="./pic/clearpixel_1x1.gif" width="13" height="9" alt="" />')
                  ;
      setCssClass(td, '.tbl_row');
      td.style.verticalAlign='top';
      td.style.width='30px';

    }
  }
}


/**
 * Delete field
 * @param   int       id          Field ID
 * @param   boolean   confirmed
 */
function deleteField(id, confirmed) {
  if (typeof(id)=='number' && id>0) {
    if (typeof(confirmed)!='boolean') {
      confirm(getLng('sure_delete_field'), 0, 0, 'deleteField('+id+', true)');
    } else if (confirmed) {
      sendData('_CALLBACK_deleteField()',
               formlink,
               'POST',
               'ajax=delete_custom_profile_field'
                +'&field_id='+urlencode(id)
                +'&s_id='+urlencode(s_id)
               );
    }
  }
}
function _CALLBACK_deleteField() {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    alert(actionHandler.message, 0, 0, 'initCustomFieldsWindow()');
  }
}


/**
 * Activate/deactivate field
 * @param   int       id      Field ID
 * @param   boolean   state   New state (TRUE: activate or FALSE: deactivate)
 */
function activateField(id, state) {
  if (typeof(id)=='number' && id>0 && typeof(state)=='boolean') {
    sendData('initCustomFieldsWindow()',
             formlink,
             'POST',
             'ajax=update_custom_profile_field'
              +'&field_id='+urlencode(id)
              +'&disabled='+urlencode(state? 'n' : 'y')
              +'&s_id='+urlencode(s_id)
             );
  }
}


/**
 * Set "writeable" field flag
 * @param   int       id          Field ID
 * @param   string    writeable
 */
function setFieldWriteable(id, writeable) {
  if (typeof(id)=='number' && id>0 && typeof(writeable)=='string') {
    sendData('initCustomFieldsWindow()',
             formlink,
             'POST',
             'ajax=update_custom_profile_field'
              +'&field_id='+urlencode(id)
              +'&writeable='+urlencode(writeable)
              +'&s_id='+urlencode(s_id)
             );
  }
}


/**
 * Change field display order
 * @param   int       id      Field ID
 * @param   boolean   order   If TRUE: increase order, if FASLE: decrease order
 */
function updateFieldOrder(id, order) {
  if (typeof(id)=='number' && id>0 && typeof(order)=='boolean') {
    sendData('initCustomFieldsWindow()',
             formlink,
             'POST',
             'ajax=update_custom_profile_field'
              +'&field_id='+urlencode(id)
              +'&order='+urlencode(order? '1' : '0')
              +'&s_id='+urlencode(s_id)
             );
  }
}


/**
 * Show "Create new field" form / save data
 * @param   boolean   submitted   TRUE if for was submitted
 */
function createNewField(submitted) {
  if (typeof(submitted)!='boolean' || !submitted) {
    // Show form
    $('fields_tbl').style.display='none';
    $('field_form').reset();
    $('field_form_tbl').style.display='';
    $('field_form_create_header').style.display='';
    $('field_form_create_footer').style.display='';
    $('field_form_edit_header').style.display='none';
    $('field_form_edit_footer').style.display='none';
    formatFields();
  } else {
    // Validate form
    var errors=new Array();
    var choices=new Array();
    var default_value='';
    var default_value_multichoice=new Array();
    $('field_form_name').value=trimString($('field_form_name').value);
    if ($('field_form_name').value=='') {
      errors.push(getLng('name_empty_error'));
    }
    default_value=trimString($($('field_form_default_value_id').value).value);
    if ($('field_form_type').value=='choice' || $('field_form_type').value=='multichoice') {
      choices=parseChoices($('field_form_choices').value);
      if (choices.length==0) {
        errors.push(getLng('no_options_specified'));
      }
      if ($('field_form_type').value=='multichoice') {
        for (var i=0; i<$($('field_form_default_value_id').value).options.length; i++) {
          if ($($('field_form_default_value_id').value).options[i].selected) {
            default_value_multichoice.push($($('field_form_default_value_id').value).options[i].value);
          }
        }
        default_value=default_value_multichoice.join("\n");
      }
    }
    if (errors.length) {
      alert(errors.join("\n"));
    } else {
      // Save data
      sendData('_CALLBACK_createNewField()',
               formlink,
               'POST',
               'ajax=create_custom_profile_field'
                +'&s_id='+urlencode(s_id)
                +'&type='+urlencode($('field_form_type').value)
                +'&name='+urlencode($('field_form_name').value)
                +'&default_value='+urlencode(default_value)
                +'&choices='+urlencode(choices.join("\n"))
                +'&visibility='+urlencode($('field_form_visibility').value)
                +'&writeable='+urlencode($('field_form_writeable').value)
                +'&disabled='+urlencode($('field_form_disabled').value)
               );
    }
  }
}
function _CALLBACK_createNewField() {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (actionHandler.status==0) {
      alert(actionHandler.message, 0, 0, 'initCustomFieldsWindow()');
    } else {
      alert(actionHandler.message);
    }
  }
}


/**
 * Format "Default value" and "Choices fields"
 */
function formatFields() {
  switch ($('field_form_type').value) {

    case 'string':
    case 'url':
    case 'email':
      $('field_form_default_value_id').value='field_form_default_value_string';
      $('field_form_default_value_string').style.display='';
      $('field_form_default_value_text').style.display='none';
      $('field_form_default_value_choice').style.display='none';
      $('field_form_default_value_multichoice').style.display='none';
      $('field_form_choices_row').style.display='none';
    break;

    case 'text':
      $('field_form_default_value_id').value='field_form_default_value_text';
      $('field_form_default_value_string').style.display='none';
      $('field_form_default_value_text').style.display='';
      $('field_form_default_value_choice').style.display='none';
      $('field_form_default_value_multichoice').style.display='none';
      $('field_form_choices_row').style.display='none';
    break;

    case 'choice':
      $('field_form_default_value_id').value='field_form_default_value_choice';
      $('field_form_default_value_string').style.display='none';
      $('field_form_default_value_text').style.display='none';
      $('field_form_default_value_choice').style.display='';
      $('field_form_default_value_multichoice').style.display='none';
      $('field_form_choices_row').style.display='';
      $('field_form_choices').value=trimString($('field_form_choices').value);
      $('field_form_choices').onkeyup=$('field_form_choices').onclick=$('field_form_choices').onchange=function() {
        fillChoicesField($('field_form_default_value_choice'), this.value);
      }
      fillChoicesField($('field_form_default_value_choice'), $('field_form_choices').value);
    break;

    case 'multichoice':
      $('field_form_default_value_id').value='field_form_default_value_multichoice';
      $('field_form_default_value_string').style.display='none';
      $('field_form_default_value_text').style.display='none';
      $('field_form_default_value_choice').style.display='none';
      $('field_form_default_value_multichoice').style.display='';
      $('field_form_choices_row').style.display='';
      $('field_form_choices').value=trimString($('field_form_choices').value);
      $('field_form_choices').onkeyup=$('field_form_choices').onclick=$('field_form_choices').onchange=function() {
        fillChoicesField($('field_form_default_value_multichoice'), this.value);
      }
      fillChoicesField($('field_form_default_value_multichoice'), $('field_form_choices').value);
    break;

  }
}


function parseChoices(choices_str) {
  var choices=trimString(choices_str.split("\r\n").join("\n"))
  do {
    choices=choices.split("\n\n").join("\n");
  } while (choices.indexOf("\n\n")!=-1);
  return choices!=''? choices.split("\n") : new Array();
}


function fillChoicesField(field, choices_str) {
  var choices=parseChoices(choices_str);
  field.options.length=0;
  for (var i=0; i<choices.length; i++) {
    field.options[field.options.length]=new Option(choices[i], choices[i]);
  }
}


/**
 * Hide field form
 */
function hideFieldForm() {
  $('fields_tbl').style.display='';
  $('field_form_tbl').style.display='none';
}


/**
 * Show "Edit field" form / save data
 * @param   int       id          Field ID
 * @param   boolean   submitted   TRUE if for was submitted
 */
function editField(id, submitted) {
  var errors=new Array();
  var choices=new Array();
  var default_value='';
  var default_value_multichoice=new Array();
  var select_field=null;
  if (typeof(submitted)!='boolean' || !submitted) {
    var field=null;
    for (var i=0; i<ProfileFields.length; i++) {
      if (parseInt(ProfileFields[i]['id'])==id) {
        field=ProfileFields[i];
        break;
      }
    }
    if (field==null) {
      return false;
    }
    // Show form
    $('fields_tbl').style.display='none';
    $('field_form').reset();
    $('field_form_create_header').style.display='none';
    $('field_form_create_footer').style.display='none';
    $('field_form_edit_header').style.display='';
    $('field_form_edit_footer').style.display='';
    $('field_form_tbl').style.display='';
    $('field_form_id').value=id;
    $('field_form_name').value=ProfileFields[i]['name'];
    $('field_form_type').value=ProfileFields[i]['type'];
    $('field_form_choices').value=ProfileFields[i]['choices'];
    $('field_form_visibility').value=ProfileFields[i]['visibility'];
    $('field_form_writeable').value=ProfileFields[i]['writeable'];
    $('field_form_disabled').value=ProfileFields[i]['disabled'];
    formatFields();
    if (ProfileFields[i]['type']=='choice' || ProfileFields[i]['type']=='multichoice') {
      select_field=$('field_form_default_value_'+ProfileFields[i]['type']);
      choices=parseChoices(ProfileFields[i]['default_value']);
      for (var i=0; i<choices.length; i++) {
        for (var ii=0; ii<select_field.options.length; ii++) {
          if (choices[i]==select_field.options[ii].value) {
            select_field.options[ii].selected=true;
            break;
          }
        }
      }
    } else {
      $($('field_form_default_value_id').value).value=ProfileFields[i]['default_value'];
    }
  } else {
    // Validate form
    $('field_form_name').value=trimString($('field_form_name').value);
    if ($('field_form_name').value=='') {
      errors.push(getLng('name_empty_error'));
    }
    default_value=trimString($($('field_form_default_value_id').value).value);
    if ($('field_form_type').value=='choice' || $('field_form_type').value=='multichoice') {
      choices=parseChoices($('field_form_choices').value);
      if (choices.length==0) {
        errors.push(getLng('no_options_specified'));
      }
      if ($('field_form_type').value=='multichoice') {
        for (var i=0; i<$($('field_form_default_value_id').value).options.length; i++) {
          if ($($('field_form_default_value_id').value).options[i].selected) {
            default_value_multichoice.push($($('field_form_default_value_id').value).options[i].value);
          }
        }
        default_value=default_value_multichoice.join("\n");
      }
    }
    if (errors.length) {
      alert(errors.join("\n"));
    } else {
      // Save data
      sendData('_CALLBACK_editField()',
               formlink,
               'POST',
               'ajax=update_custom_profile_field'
                +'&s_id='+urlencode(s_id)
                +'&field_id='+urlencode($('field_form_id').value)
                +'&type='+urlencode($('field_form_type').value)
                +'&name='+urlencode($('field_form_name').value)
                +'&default_value='+urlencode(default_value)
                +'&choices='+urlencode(choices.join("\n"))
                +'&visibility='+urlencode($('field_form_visibility').value)
                +'&writeable='+urlencode($('field_form_writeable').value)
                +'&disabled='+urlencode($('field_form_disabled').value)
               );
    }
  }
}
function _CALLBACK_editField() {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  if (actionHandler.status==-1) {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (actionHandler.status==0) {
      alert(actionHandler.message, 0, 0, 'initCustomFieldsWindow()');
    } else {
      alert(actionHandler.message);
    }
  }
}
