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


function initLanguagesForm() {
  var inputs=$$('INPUT');
  if (typeof(window.parent.languages)!='object') {
    window.parent.languages=new Array();
  }
  window.parent.language_names=new Array();
  window.parent.language_files=new Array();
  $('contents_div').style.display='none';
  getLanguages();
}

function getLanguages() {
  sendData('_CALLBACK_getLanguages()',
           './install/ajax/get_languages.php',
           'POST',
            'host='+urlencode(window.parent.db_data['host'])
           +'&user='+urlencode(window.parent.db_data['user'])
           +'&password='+urlencode(window.parent.db_data['password'])
           +'&database='+urlencode(window.parent.db_data['database'])
           +'&prefix='+urlencode(window.parent.db_data['prefix'])
           );
}
function _CALLBACK_getLanguages() {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  var html='';
  var languages=actionHandler.getElement('languages');
  var language=null;
  var language_nr=0;
  var name='';
  var iso_name='';
  var local_name='';
  var filename='';
  if (status!='0') {
    alert(message);
  } else {
    if (languages) {
      while (null!=(language=actionHandler.getElement('language', language_nr, languages))) {
        iso_name=actionHandler.getCdata('iso_name', 0, language);
        name=actionHandler.getCdata('name', 0, language);
        local_name=actionHandler.getCdata('local_name', 0, language);
        filename=actionHandler.getCdata('filename', 0, language);
        html+='<br /><label for="languages_chkbox_'+htmlspecialchars(iso_name)+'" title="'+htmlspecialchars(name+' ('+local_name+')')+'"><input onclick="setLanguage(this)" type="checkbox" id="languages_chkbox_'+htmlspecialchars(iso_name)+'" title="'+htmlspecialchars(name+' ('+local_name+')')+'" /> '+htmlspecialchars(name+' ('+local_name+')')+'</label>';
        window.parent.language_names[iso_name]=name+' ('+local_name+')';
        window.parent.language_files[iso_name]=filename;
        language_nr++;
      }
    }
  }
  if (language_nr==0) {
    $('no_languages_found').style.display='';
    $('continue_btn').style.display='none';
    $('language_selection_header').style.display='none';
  } else {
    $('no_languages_found').style.display='none';
    $('continue_btn').style.display='';
    $('language_selection_header').style.display='';
    $('languages_cell').innerHTML=html+'<br /><br />';
    var tmp=new Array();
    for (var i=0; i<window.parent.languages.length; i++) {
      if ($('languages_chkbox_'+window.parent.languages[i])) {
        $('languages_chkbox_'+window.parent.languages[i]).click();
        tmp.push(window.parent.languages[i]);
      }
    }
    window.parent.languages=tmp;
  }
  $('contents_div').style.display='';
  toggleProgressBar(false);
}

function setLanguage(obj) {
  var tmp=new Array();
  var id=obj.id.substring(17);
  if (obj.checked) {
    for (var i=0; i<window.parent.languages.length; i++) {
      if (window.parent.languages[i]==id) {
        return false;
      }
    }
    window.parent.languages.push(id);
  } else {
    for (var i=0; i<window.parent.languages.length; i++) {
      if (window.parent.languages[i]!=id) {
        tmp.push(window.parent.languages[i]);
      }
    }
    window.parent.languages=tmp;
  }
}


function validateLanguages() {
  var errors=new Array();
  if (window.parent.languages.length==0) {
    errors.push('Please select at least one language');
  }
  if (errors.length) {
    alert(errors.join("\n"));
  } else {
    window.location.href='./install.php?step=7&ts='+unixTimeStamp();
  }
}
