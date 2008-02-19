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


function initDbForm() {
  if (typeof(window.parent.db_data)!='object') {
    window.parent.db_data=new Array();
    window.parent.db_data['host']='localhost';
    window.parent.db_data['user']='';
    window.parent.db_data['password']='';
    window.parent.db_data['database']='';
    window.parent.db_data['prefix']='pcpin_';
  }

  $('db_host').value=window.parent.db_data['host'];
  $('db_user').value=window.parent.db_data['user'];
  $('db_password').value=window.parent.db_data['password'];
  $('db_database').value=window.parent.db_data['database'];
  $('db_prefix').value=window.parent.db_data['prefix'];

}


function storeDbData() {
  window.parent.db_data['host']=$('db_host').value;
  window.parent.db_data['user']=$('db_user').value;
  window.parent.db_data['password']=$('db_password').value;
  window.parent.db_data['database']=$('db_database').value;
  window.parent.db_data['prefix']=$('db_prefix').value;

  // Check database connection
  $('contents_div').style.display='none';
  sendData('_CALLBACK_storeDbData()', './install/ajax/check_db.php', 'POST', 'host='+urlencode(window.parent.db_data['host'])
                                                                            +'&user='+urlencode(window.parent.db_data['user'])
                                                                            +'&password='+urlencode(window.parent.db_data['password'])
                                                                            +'&database='+urlencode(window.parent.db_data['database'])
                                                                            +'&prefix='+urlencode(window.parent.db_data['prefix'])
                                                                            );
}
function _CALLBACK_storeDbData() {
//debug(actionHandler.getResponseString()); return false;

  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');

  if (status=='0') {
    // Success
    window.location.href='./install.php?step=5&ts='+unixTimeStamp();
  } else {
    toggleProgressBar(false);
    $('contents_div').style.display='';
    if (status=='10') {
      // Failed to write database config file
      $('db_config_write_error').style.display='';
    } else {
      alert(message);
    }
  }
}

function downloadDbConfig() {
  var form=$('download_db_config_form');
  form.action='./install/db.inc.php';
  form.host.value=window.parent.db_data['host'];
  form.user.value=window.parent.db_data['user'];
  form.password.value=window.parent.db_data['password'];
  form.database.value=window.parent.db_data['database'];
  form.prefix.value=window.parent.db_data['prefix'];
  form.submit();
}