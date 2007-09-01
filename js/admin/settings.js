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
 * Current settings group
 * @var string
 */
var CurrentSettingsGroup='';

/**
 * Array with setting values, indexed by their ID
 * @var object
 */
var SettingValues=new Array();

/**
 * ID of room list selection element
 * @var string
 */
var RoomListInputID='';

/**
 * ID of room list selection setting
 * @var int
 */
var RoomListSettingID=0;

/**
 * Master modules list for Slave mode
 * @var object
 */
var SlaveModeMasters=new Array();



/**
 * Setting object
 * @var object
 */
var Setting=function(id, nr, type, name, value, choices, description, group, subgroup) {
  this.ID=stringToNumber(id);
  this.Nr=nr;
  this.Type=type;
  this.Name=name;
  this.Value=value;
  this.Choices=choices;
  this.Description=description;
  this.Group=group;
  this.Subgroup=subgroup;
}

/**
 * Init window
 * @param   string    group   Settings group
 */
function initSettingsForm(group) {
  if (typeof(group)=='string' && group!='') {
    CurrentSettingsGroup=group;
    // Get languages and settings
    getSlaveMasters();
  }
}


/**
 * Get master modules for slave mode
 */
function getSlaveMasters() {
  sendData('_CALLBACK_getSlaveMasters()', formlink, 'POST', 'ajax='+urlencode('get_slave_mode_masters')+'&s_id='+urlencode(s_id));
}
function _CALLBACK_getSlaveMasters() {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');

  var master=null;
  var master_nr=0;
  SlaveModeMasters=new Array();

  if (status=='-1') {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    while (null!=(master=actionHandler.getCdata('master', master_nr++))) {
      SlaveModeMasters.push(master);
    }
    getAvailableLanguages('getSettings()');
  }
}

/**
 * Get settings
 */
function getSettings() {
  SettingObjects=new Array();
  if (typeof(CurrentSettingsGroup)=='string' && CurrentSettingsGroup!='') {
    sendData('_CALLBACK_getSettings()', formlink, 'POST', 'ajax='+urlencode('get_settings')+'&group='+htmlspecialchars(CurrentSettingsGroup)+'&s_id='+urlencode(s_id));
  }
}
function _CALLBACK_getSettings() {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');

  var settings=null;
  var setting=null;
  var setting_id=0;
  var setting_nr=0;

  var settings_tbl=$('settings_tbl');
  var tr=null;
  var td=null;
  var last_subgroup='';

  var inputs_data=null;

  if (status=='-1') {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (message=='OK') {
      // OK
      RoomListInputID='';
      while (settings_tbl.rows.length>3) {
        settings_tbl.deleteRow(settings_tbl.rows.length-2);
      }
      if (null!=(settings=actionHandler.getElement('settings', 0))) {
        while (null!=(setting=actionHandler.getElement('setting', setting_nr++, settings))) {
          setting_id=stringToNumber(actionHandler.getCdata('id', 0, setting));
          SettingObjects[setting_id]=new Setting(setting_id,
                                                 setting_nr,
                                                 actionHandler.getCdata('type', 0, setting, ''),
                                                 actionHandler.getCdata('name', 0, setting, ''),
                                                 actionHandler.getCdata('value', 0, setting, ''),
                                                 actionHandler.getCdata('choices', 0, setting, ''),
                                                 actionHandler.getCdata('description', 0, setting, ''),
                                                 actionHandler.getCdata('group', 0, setting, ''),
                                                 actionHandler.getCdata('subgroup', 0, setting, '')
                                                 );
        }
      }
    } else if (message!=null) {
      alert(message);
    }
  }
  // Display settings table
  last_subgroup='';
  for (var i in SettingObjects) {
    if (SettingObjects[i].Subgroup!=last_subgroup) {
      last_subgroup=SettingObjects[i].Subgroup;

      // Display subgroup row
      tr=settings_tbl.insertRow(settings_tbl.rows.length-1);

      td=tr.insertCell(-1);
      td.innerHTML=htmlspecialchars(last_subgroup);
      td.colSpan=3;
      setCssClass(td, 'tbl_row');
      td.style.fontWeight='bold';
    }

    tr=settings_tbl.insertRow(settings_tbl.rows.length-1);

    td=tr.insertCell(-1);
    td.innerHTML=htmlspecialchars(SettingObjects[i].Nr+'.');
    setCssClass(td, 'tbl_row');
    td.style.verticalAlign='top';
    td.style.textAlign='right';

    td=tr.insertCell(-1);
    td.innerHTML=nl2br(htmlspecialchars(SettingObjects[i].Description));
    setCssClass(td, 'tbl_row');
    td.style.verticalAlign='top';

    inputs_data=makeSettingInput(SettingObjects[i].ID, SettingObjects[i].Value, SettingObjects[i].Type, SettingObjects[i].Choices, SettingObjects[i].Description);
    td=tr.insertCell(-1);
    td.innerHTML=inputs_data[0];
    setCssClass(td, 'tbl_row');
    td.style.verticalAlign='top';
    td.noWrap=true;

    // Assign events
    for (var ii in inputs_data[1]) {
      $(ii).element_nr=SettingObjects[i].Nr;
      $(ii).choices=SettingObjects[i].Choices;
      // "onkeydown" for text fields
      if ($(ii).type=='text') {
        if (inputs_data[1][ii]=='int') {
          // Int field
          $(ii).validate=function() {
            var result=false;
            this.value=trimString(this.value);
            if (isDigitString(this.value)) {
              var num_val=stringToNumber(this.value);
              this.value=num_val;
              var choice_parts=this.choices.split('|');
              if (   (choice_parts[0]=='*' || num_val>=stringToNumber(choice_parts[0]))
                  && (choice_parts[1]=='*' || num_val<=stringToNumber(choice_parts[1]))) {
                result=true;
              }
            }
            return result;
          }
        } else {
          // String field
          switch (SettingObjects[i].Choices) {

            case '<email>':
              // String must contain email address
              $(ii).validate=function() {
                this.value=trimString(this.value);
                return checkEmail(this.value);
              }
            break;

            default:
              $(ii).validate=function() {
                return true;
              }
            break;

          }
        }
      }
    }
  }
  settings_tbl.style.display='';
  toggleProgressBar(false);
  setMouseoverStatus();
  if (RoomListInputID!='') {
    // Get room list
    getRoomList();
  }
}


/**
 * Generate input element HTML code for a setting depending on value type
 * @param   int       id            Value ID
 * @param   string    val           Value to display in input element
 * @param   string    type          Value type
 * @param   string    choices       Value choices or range
 * @param   string    description   Element description
 * @return  array Array with HTML code as string and assigned IDs as an array
 */
function makeSettingInput(id, val, type, choices, description) {
  var input_html='';
  var parts=null;
  var size=12;
  var maxlength=255;
  var choices_array=null;
  var choices_data=null;
  var assigned_ids=new Array();
  var choice_parts=null;
  var choice_key='';
  var choice_val='';

  if (typeof(id)=='number') {
    parts=type.split('_');
    switch (parts[0]) {

      case 'int':
      case 'float':
        size=8;
      break;

      default:
        size=64;
      break;

    }

    if (parts[0]=='string' && isDigitString(parts[1])) {
      // Limited string
      maxlength=stringToNumber(parts[1]);
      if (size>maxlength) {
        size=maxlength;
      }
    }

    switch (parts[1]) {

      case 'choice':
        // Simple selection
        if (choices=='<languages>') {
          choices_array=new Array();
          for (var i in AvailableLanguages) {
            choices_array.push(AvailableLanguages[i].ID+'='+AvailableLanguages[i].Name+' ('+AvailableLanguages[i].LocalName+')');
          }
        } else if (choices=='<slave_masters>') {
          choices_array=new Array();
          for (var i in SlaveModeMasters) {
            choices_array.push(SlaveModeMasters[i]+'='+SlaveModeMasters[i]);
          }
        } else {
          choices_array=choices.split('|');
        }
        if (parts[0]=='boolean') {
          for (var i=0; i<choices_array.length; i++) {
            choices_data=choices_array[i].split('=');
            choice_key=choices_data.shift();
            choice_val=choices_data.join('=');
            input_html+='<label for="setting_'+htmlspecialchars(id)+'_'+i+'" title="'+htmlspecialchars(description+': '+choice_val)+'">'
                       +'<input type="radio" name="setting_'+htmlspecialchars(id)+'" id="setting_'+htmlspecialchars(id)+'_'+i+'" title="'+htmlspecialchars(description+': '+choice_val)+'" value="'+htmlspecialchars(choice_key)+'" '+((choice_key==val)? 'checked="checked"' : '')+' />&nbsp;'+htmlspecialchars(choice_val)
                       +'</label>&nbsp;&nbsp;&nbsp;&nbsp;'
                       ;
            assigned_ids['setting_'+id+'_'+i]=parts[0];
          }
        } else {
          input_html+='<select id="setting_'+htmlspecialchars(id)+'" title="'+htmlspecialchars(description)+'">';
          for (var i=0; i<choices_array.length; i++) {
            choices_data=choices_array[i].split('=');
            choice_key=choices_data.shift();
            choice_val=choices_data.join('=');
            input_html+='<option value="'+htmlspecialchars(choice_key)+'" '+((choice_key==val)? 'selected="selected"' : '')+'>'+htmlspecialchars(choice_val)+'</option>';
          }
          input_html+='</select>';
          assigned_ids['setting_'+id]=parts[0];
        }
      break;

      case 'multichoice':
        // Multiple selection
        choices_array=choices.split('|');
        for (var i=0; i<choices_array.length; i++) {
          choices_data=choices_array[i].split('=');
          choice_key=choices_data.shift();
          choice_val=choices_data.join('=');
          input_html+='<label for="setting_'+htmlspecialchars(id)+'_'+i+'" title="'+htmlspecialchars(description+': '+choice_key)+'">'
                     +'<input type="checkbox" id="setting_'+htmlspecialchars(id)+'_'+i+'" value="'+htmlspecialchars(choice_key.split(',').join('|'))+'" '+((-1!=('|'+val+'|').indexOf('|'+choice_key.split(',').join('|')+'|'))? 'checked="checked"' : '')+' />'
                     +htmlspecialchars(choice_val)
                     +'</label>'
                     +'<br />'
                     ;
          assigned_ids['setting_'+id+'_'+i]=parts[0];
        }
      break;

      default:
        if (choices=='<color>') {
          // Show color box
          input_html+='<input type="hidden" id="setting_'+htmlspecialchars(id)+'" value="'+htmlspecialchars(val)+'" />'
                     +'<div id="setting_color_'+htmlspecialchars(id)+'" style="border: solid 1px #000000; cursor:pointer; background-color:#'+htmlspecialchars(val)+'; width:60px; height: 20px;" title="'+htmlspecialchars(description)+'" onclick="openColorBox(\'setting_color_'+htmlspecialchars(id)+'\', \'background-color\', this, \'$(\\\'setting_'+htmlspecialchars(id)+'\\\').value\', true); return false;">'
                     + '&nbsp;'
                     +'</div>'
                     ;
          assigned_ids['setting_'+id]=parts[0];
        } else if (choices=='<room>') {
          // Show room selection
          input_html+='<select id="setting_'+htmlspecialchars(id)+'"></select>';
          assigned_ids['setting_'+id]=parts[0];
          RoomListInputID='setting_'+id;
          RoomListSettingID=id;
        } else {
          // Show text input field
          input_html+='<input type="text" id="setting_'+htmlspecialchars(id)+'" size="'+size+'" maxlength="'+maxlength+'" value="'+htmlspecialchars(val)+'" title="'+htmlspecialchars(description)+'" autocomplete="off" />';
          assigned_ids['setting_'+id]=parts[0];
          if (type=='int_range') {
            choice_parts=choices.split('|');
            if (choice_parts[0]=='*') {
              choice_parts[0]=getLng('unlimited');
            }
            if (choice_parts[1]=='*') {
              choice_parts[1]=getLng('unlimited');
            }
            input_html+='&nbsp;('
                       +urlencode(choice_parts[0])
                       +' .. '
                       +urlencode(choice_parts[1])
                       +')'
                       ;
          }
        }
      break;

    }
    // Additional test features
    if (choices=='<url>') {
      input_html+='<br />'
                 +'<a href=":" title="'+htmlspecialchars(getLng('check_url'))+'" target="_blank" onclick="this.href=$(\'setting_'+htmlspecialchars(id)+'\').value">'+htmlspecialchars(getLng('check_url'))+'</a>';
    }
  }
  return Array(input_html, assigned_ids);
}

/**
 * Validate form and save data
 */
function updateSettings() {
  var inputs=$$('INPUT');
  var selects=$$('SELECT');
  var errors=new Array();
  var setting_ids='';
  var setting_id=0;
  var values_array=new Array();
  var post_settings=new Array();

  for (var i=0; i<inputs.length; i++) {
    if (inputs[i].type=='text' && inputs[i].id.indexOf('setting_')==0) {
      if (true!=inputs[i].validate()) {
        if (errors.length==0) {
          inputs[i].focus();
        }
        errors.push(getLng('setting_error').split('[NR]').join(inputs[i].element_nr));
      }
    }
  }
  if (errors.length!=0) {
    alert(errors.join("\n"));
  } else {
    // Update settings
    values_array=new Array();
    for (var i=0; i<inputs.length; i++) {
      if (inputs[i].id.indexOf('setting_')==0) {
        setting_ids=(inputs[i].id.substring(8)).split('_');
        setting_id=stringToNumber(setting_ids[0]);
        if (null==values_array[setting_id]) {
          values_array[setting_id]=new Array();
        }
        if (inputs[i].type!='checkbox' && inputs[i].type!='radio' || inputs[i].checked) {
          values_array[setting_id].push(inputs[i].value);
        }
      }
    }
    for (var i=0; i<selects.length; i++) {
      if (selects[i].id.indexOf('setting_')==0) {
        setting_ids=(selects[i].id.substring(8)).split('_');
        setting_id=stringToNumber(setting_ids[0]);
        if (null==values_array[setting_id]) {
          values_array[setting_id]=new Array();
        }
        values_array[setting_id].push(selects[i].value);
      }
    }
    for (var i in values_array) {
      SettingObjects[i].Value=values_array[i].join('|');
      post_settings.push('settings['+SettingObjects[i].Name+']='+urlencode(SettingObjects[i].Value));
    }
    // Send new settings to server
    sendData('_CALLBACK_updateSettings()', formlink, 'POST', 'ajax='+urlencode('update_settings')+'&s_id='+urlencode(s_id)+'&'+post_settings.join('&'));
  }
}
function _CALLBACK_updateSettings() {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');

  if (status=='-1') {
    // Session is invalid
    window.parent.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else if (message!=null) {
    alert(message);
  }
  toggleProgressBar(false);
}


/**
 * Get room list
 * @param   object    categories    Categories XML element
 */
function getRoomList() {
  sendData('_CALLBACK_getRoomList()', formlink, 'POST', 'ajax='+urlencode('get_room_structure')+'&s_id='+urlencode(s_id), true);
}
function _CALLBACK_getRoomList() {
  var categories=actionHandler.getElement('categories', 0, actionHandler.getElement('category', 0, actionHandler.getElement('categories')));
//debug(actionHandler.getResponseString()); return false;
  var rs=$(RoomListInputID);
  if (typeof(categories)=='object' && categories && rs) {
    var cat=null;
    var cat_nr=0;
    var room=null;
    var room_nr=0;
    var room_id=0;
    var s_cat=null;
    var s_room=null;
    rs.innerHTML='';
    s_room=document.createElement('OPTION');
    s_room.innerHTML='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
    s_room.value='0';
    rs.appendChild(s_room);
    while (null!=(cat=actionHandler.getElement('category', cat_nr++, categories))) {
      if (actionHandler.countElements('room', cat)>0) {
        s_cat=document.createElement('OPTGROUP');
        s_cat.label=actionHandler.getCdata('name', 0, cat);
        room_nr=0;
        while (null!=(room=actionHandler.getElement('room', room_nr++, cat))) {
          room_id=stringToNumber(actionHandler.getCdata('id', 0, room));
          s_room=document.createElement('OPTION');
          s_room.value=room_id;
          s_room.password_protect='1'==actionHandler.getCdata('password_protected', 0, room);
          s_room.innerHTML=(s_room.password_protect? '* ' : '')+htmlspecialchars(actionHandler.getCdata('name', 0, room));
          s_cat.appendChild(s_room);
          if (stringToNumber(s_room.value)==stringToNumber(SettingObjects[RoomListSettingID].Value)) {
            s_room.selected=true;
          }
        }
        if (room_nr>1 || room!=null) {
          rs.appendChild(s_cat);
        }
      }
    }
  }
}
