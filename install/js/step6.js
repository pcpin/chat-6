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

function initAdminAccountForm() {
  if (typeof(window.parent.admin_account)!='object') {
    window.parent.admin_account=new Array();
    window.parent.admin_account['create']=true;
    window.parent.admin_account['username']='';
    window.parent.admin_account['password']='';
    window.parent.admin_account['password2']='';
    window.parent.admin_account['email']='';
  }

  $('admin_account_username').value=window.parent.admin_account['username'];
  $('admin_account_password').value=window.parent.admin_account['password'];
  $('admin_account_password2').value=window.parent.admin_account['password2'];
  $('admin_account_email').value=window.parent.admin_account['email'];


  if (window.parent.import_selection['users']) {
    $('no_new_admin_account_row').style.display='';
    $('no_new_admin_account').checked=!window.parent.admin_account['create'];
    $('no_new_admin_account').onclick();
  }
}

function setNoAdminAccount(state) {
  window.parent.admin_account['create']=!state;
  $('admin_account_username').disabled=state;
  $('admin_account_password').disabled=state;
  $('admin_account_email').disabled=state;
}

function setAdminUsername(obj) {
  obj.value=trimString(obj.value);
  window.parent.admin_account['username']=obj.value;
}

function setAdminPassword(obj) {
  window.parent.admin_account['password']=obj.value;
}

function setAdminPassword2(obj) {
  window.parent.admin_account['password2']=obj.value;
}

function setAdminEmail(obj) {
  obj.value=trimString(obj.value);
  window.parent.admin_account['email']=obj.value;
}

function validateAdminAccount() {
  var errors=new Array();
  if (window.parent.admin_account['create']) {
    // Validate admin account
    
    if (window.parent.admin_account['username'].length<3) {
      errors.push('Adminitrator username too short');
    }
    if (window.parent.admin_account['password'] != window.parent.admin_account['password2']) {
      errors.push('Adminitrator passwords are not ident');
    } else if (window.parent.admin_account['password'].length<3) {
      errors.push('Adminitrator password too short');
    }
    if (!checkEmail(window.parent.admin_account['email'])) {
      errors.push('Adminitrator E-Mail address seems to be invalid');
    }

    if (errors.length) {
      alert(errors.join("\n"));
    } else if (window.parent.import_selection['users']) {
      // Users will be imported. Check administrator username and email address.
      sendData('_CALLBACK_validateAdminAccount()', './install/ajax/check_admin_account.php', 'POST', 'host='+urlencode(window.parent.db_data['host'])
                                                                                                    +'&user='+urlencode(window.parent.db_data['user'])
                                                                                                    +'&password='+urlencode(window.parent.db_data['password'])
                                                                                                    +'&database='+urlencode(window.parent.db_data['database'])
                                                                                                    +'&prefix='+urlencode(window.parent.db_data['prefix'])
                                                                                                    +'&admin_username='+urlencode(window.parent.admin_account['username'])
                                                                                                    +'&admin_email='+urlencode(window.parent.admin_account['email'])
                                                                                                    );
    } else {
      // No users will be imported
      window.location.href='./install.php?step=7&ts='+unixTimeStamp();
    }
  } else {
    // No new account will be created
    window.location.href='./install.php?step=7&ts='+unixTimeStamp();
  }
}
function _CALLBACK_validateAdminAccount() {
  toggleProgressBar(false);
  $('contents_div').style.display='';
//debug(actionHandler.getResponseString()); return false;

  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  if (status!='0') {
    alert(message);
  } else {
    window.location.href='./install.php?step=7&ts='+unixTimeStamp();
  }
}