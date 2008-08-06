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

var importObjectNames5=new Array('users', 'smilies', 'settings', 'rooms', 'bad_words', 'ip_filter');
var importObjectNames6=new Array('users', 'smilies', 'settings', 'rooms', 'bad_words', 'ip_filter', 'avatar_gallery', 'banners', 'languages');

function checkPreviousInstallation() {
  if (typeof(window.parent.import_selection)!='object') {
    window.parent.import_selection=new Array();
    for (var i in importObjectNames5) {
      window.parent.import_selection[importObjectNames5[i]]=false;
    }
    for (var i in importObjectNames6) {
      window.parent.import_selection[importObjectNames6[i]]=false;
    }
  }
  $('contents_div').style.display='none';
  sendData('_CALLBACK_checkPreviousInstallation()', './install/ajax/check_previous_installtion.php', 'POST', 'host='+urlencode(window.parent.db_data['host'])
                                                                                                            +'&user='+urlencode(window.parent.db_data['user'])
                                                                                                            +'&password='+urlencode(window.parent.db_data['password'])
                                                                                                            +'&database='+urlencode(window.parent.db_data['database'])
                                                                                                            +'&prefix='+urlencode(window.parent.db_data['prefix'])
                                                                                                            );
}
function _CALLBACK_checkPreviousInstallation() {
  toggleProgressBar(false);
  $('contents_div').style.display='';
//debug(actionHandler.getResponseString()); return false;

  var message=actionHandler.message;
  var status=actionHandler.status;
  var version_str=actionHandler.getCdata('version');
  var version=stringToNumber(version_str);

  if (status=='0') {
    // Success
    if (version>0 && version<6) {
      // Previous installation detected
      if (version<5.10) {
        // Previous installation is too old
        $('previous_installation_too_old').style.display='';
        $('too_old_version').innerHTML=htmlspecialchars(version_str);
      } else {
        // Previous installation is OK
        $('previous_installation_ok').style.display='';
        $('onstalled_ok_version').innerHTML=htmlspecialchars(version_str);

        if (version<6) {
          // PCPIN Chat 5.xx
          for (var i in importObjectNames6) {
            $('keep_'+importObjectNames6[i]).checked=false;
            $('row_keep_'+importObjectNames6[i]).style.display='none';
          }
          for (var i in importObjectNames5) {
            $('row_keep_'+importObjectNames5[i]).style.display='';
            $('keep_'+importObjectNames5[i]).checked=window.parent.import_selection[importObjectNames5[i]];
          }
/*
        } else {
          // PCPIN Chat 6.xx
          for (var i in importObjectNames5) {
            $('keep_'+importObjectNames5[i]).checked=false;
            $('row_keep_'+importObjectNames5[i]).style.display='none';
          }
          for (var i in importObjectNames6) {
            $('row_keep_'+importObjectNames6[i]).style.display='';
            $('keep_'+importObjectNames6[i]).checked=window.parent.import_selection[importObjectNames6[i]];
          }
*/
        }

      }
    } else {
      // No previous installation detected
      $('no_previous_installations').style.display='';
    }
  } else {
    // Error
    alert(message);
  }
}


function setImportFlag(obj) {
  var show_images_warning=false;
  window.parent.import_selection[obj.id.substring(5)]=obj.checked;
  for (var i in importObjectNames5) {
    if (window.parent.import_selection[importObjectNames5[i]]) {
      if (   importObjectNames5[i]=='users'
          || importObjectNames5[i]=='smilies'
          || importObjectNames5[i]=='rooms'
          ) {
        show_images_warning=true;
        break;
      }
    }
  }
  if (show_images_warning) {
    document.getElementById('images_warning').style.display='';
  } else {
    document.getElementById('images_warning').style.display='none';
  }
}
