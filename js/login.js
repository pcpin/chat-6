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
 * Minimum allowed username length
 * @var int
 */
var LoginLengthMin=3;

/**
 * Maximum allowed username length
 * @var int
 */
var LoginLengthMax=10;

/**
 * Flag: TRUE, if called by admin.php
 * @var boolean
 */
var adminLogin=false;

/**
 * Init login form
 * @param   int       login_length_min      Minimum allowed username length
 * @param   int       login_length_max      Maximum allowed username length
 * @param   boolean   admin_login           TRUE, if called by admin.php
 */
function initLoginForm(login_length_min, login_length_max, admin_login) {
  LoginLengthMin=login_length_min;
  LoginLengthMax=login_length_max;
  if (typeof(admin_login)=='boolean') {
    adminLogin=admin_login;
  }

  // Check for parent frameset
  try {
    if (window.parent && window.parent.frames.length>0 && window.parent.appName_=='pcpin_chat' && window.parent.frames[0].name!='chat_summary_frame') {
      window.parent.document.location.href=formlink;
    }
  } catch (e) {}

  if (!pbl) {
    pbl=$('pbl');
  }

  window.onresize=function() {
    setTimeout("centerLoginTable()", 30);
    setTimeout("moveToCenter($('register_table')); fixPBL($('register_table'));", 30);
    setTimeout("moveToCenter($('reset_pw_table')); fixPBL($('reset_pw_table'));", 30);
  }

  // Display login form
  showLoginForm();
}


/**
 * Move login table and chat summary at the window center
 */
function centerLoginTable() {
  var login_tbl=$('login_table');
  var chat_summary=$('chat_summary');
  if (chat_summary) {
    moveToCenter(login_tbl, -8-Math.round((chat_summary.scrollHeight)/2));
    moveToCenter(chat_summary, 8+Math.round((login_tbl.scrollHeight)/2));
    fixPBL(login_tbl);
  } else {
    moveToCenter(login_tbl);
    fixPBL(login_tbl);
  }
}


/**
 * Display login form
 */
function showLoginForm() {
  hideRegisterForm();
  hideResetPasswordForm();

  $('login_table').style.display='';
  if ($('chat_summary')) {
    $('chat_summary').style.display='';
  }
  if (isIE) {
    $('login_password').style.width=($('login_username').scrollWidth+4)+'px';
  }
  centerLoginTable();

  $('login_username').select();
  $('login_username').focus();
}


/**
 * Hide login form
 */
function hideLoginForm() {
  $('login_table').style.display='none';
  if ($('chat_summary')) {
    $('chat_summary').style.display='none';
  }
  pbl.style.display='none';
}


/**
 * Log in
 */
function doLogin() {
  hideLoginForm();
  sendData('_CALLBACK_doLogin()', formlink, 'POST', 'ajax=do_login'
                                                   +'&login='+urlencode($('login_username').value)
                                                   +'&password='+urlencode($('login_password').value)
                                                   +'&time_zone_offset='+urlencode(date('Z'))
                                                   +'&language_id='+urlencode($('language_selection')? $('language_selection').value : 0)
                                                   +(adminLogin? '&admin_login=1' : '')
                                                   );
  return false;
}
function _CALLBACK_doLogin() {
  if (actionHandler.status==0) {
    // Login successfull
    var s_id=actionHandler.data['s_id'][0];
    var df=$('dummyform');
    if (df) {
      df.s_id.value=s_id;
      df.inc.value='room_selection';
      df.ts.value=unixTimeStamp();
      df.just_logged_in.value='1';
      df.submit();
    }
  } else {
    // Login failed
    $('login_password').value='';
    toggleProgressBar(false);
    alert(actionHandler.message, null, null, 'showLoginForm()');
  }
}


/**
 * Log in as Guest
 */
function doGuestLogin() {
  hideLoginForm();
  sendData('_CALLBACK_doLogin()', formlink, 'POST', 'ajax=do_login'
                                                   +'&guest_login=1'
                                                   +'&time_zone_offset='+urlencode(date('Z'))
                                                   +'&language_id='+urlencode($('language_selection')? $('language_selection').value : 0)
                                                   );
  return false;
}


/**
 * Display "Register" form
 * @param   boolean   no_reset    Optional. If TRUE, then input fields will be not cleared. Default: FALSE.
 */
function showRegisterForm(no_reset) {
  hideLoginForm();
  $('register_table').style.display='';
  moveToCenter($('register_table'));
  fixPBL($('register_table'));

  if (typeof(no_reset)!='boolean' || !no_reset) {
    $('register_username').value='';
    $('register_email').value='';
    $('register_password1').value='';
    $('register_password2').value='';
  }

  $('register_username').focus();
}


/**
 * Hide "Register" form
 */
function hideRegisterForm() {
  $('register_table').style.display='none';
  pbl.style.display='none';
}


/**
 * Display "Reset password" form
 * @param   boolean   no_reset    Optional. If TRUE, then input fields will be not cleared. Default: FALSE.
 */
function showResetPasswordForm(no_reset) {
  hideLoginForm();
  if (typeof(no_reset)!='boolean' || !no_reset) {
    $('reset_pw_username').value='';
    $('reset_pw_email').value='';
  }
  $('reset_pw_table').style.display='';
  $('reset_pw_username').focus();
  $('reset_pw_username').select();
  moveToCenter($('reset_pw_table'));
  fixPBL($('reset_pw_table'));
}


/**
 * Hide "Reset password" form
 */
function hideResetPasswordForm() {
  $('reset_pw_table').style.display='none';
  pbl.style.display='none';
}


/**
 * Register
 */
function doRegister() {
  var errors=new Array();

  $('register_username').value=trimString($('register_username').value);
  $('register_email').value=trimString($('register_email').value);

  if ($('register_username').value=='') {
    errors.push(getLng('username_empty'));
  } else if ($('register_username').value.length<LoginLengthMin || $('register_username').value.length>LoginLengthMax) {
    errors.push((getLng('username_length_error').split('[MIN]').join(LoginLengthMin)).split('[MAX]').join(LoginLengthMax));
  }

  if (!checkEmail($('register_email').value)) {
    errors.push(getLng('email_invalid'));
  }

  // Validate passwords
  if ($('register_password1').value.length<3) {
    errors.push(getLng('password_too_short'));
  } else if ($('register_password1').value!=$('register_password2').value) {
    errors.push(getLng('passwords_not_ident'));
  }

  hideRegisterForm();
  if (errors.length>0) {
    alert('- '+errors.join("\n- "), null, null, 'showRegisterForm(true)');
  } else {
    sendData('_CALLBACK_doRegister()', formlink, 'POST', 'ajax=do_register'
                                                        +'&login='+urlencode($('register_username').value)
                                                        +'&password='+urlencode($('register_password1').value)
                                                        +'&email='+urlencode($('register_email').value)
                                                        +'&language_id='+urlencode($('language_selection')? $('language_selection').value : 0)
                                                        );
  }
  return false;
}
function _CALLBACK_doRegister() {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  if (actionHandler.status==0) {
    // Register successfull
    alert(actionHandler.message, null, null, 'showLoginForm()');
  } else {
    // Register failed
    alert(actionHandler.message, null, null, 'showRegisterForm(true)');
  }
}


/**
 * Reset password
 */
function doResetPassword() {
  var errors=new Array();
  $('reset_pw_username').value=trimString($('reset_pw_username').value);
  $('reset_pw_email').value=trimString($('reset_pw_email').value);

  if ($('reset_pw_username').value=='') {
    errors.push(getLng('username_empty'));
  }
  if (!checkEmail($('reset_pw_email').value)) {
    errors.push(getLng('email_invalid'));
  }
  hideResetPasswordForm();
  if (errors.length>0) {
    alert('- '+errors.join("\n- "), null, null, 'showResetPasswordForm(true)');
  } else {
    sendData('_CALLBACK_doResetPassword()', formlink, 'POST', 'ajax=do_reset_password'
                                                             +'&login='+urlencode($('reset_pw_username').value)
                                                             +'&email='+urlencode($('reset_pw_email').value)
                                                             );
  }
  return false;
}
function _CALLBACK_doResetPassword() {
//debug(actionHandler.getResponseString());
//return false;
  toggleProgressBar(false);
  if (actionHandler.status==0) {
    // Password reset successfull
    alert(actionHandler.message, null, null, 'showLoginForm()');
  } else {
    // An error
    alert(actionHandler.message, null, null, 'showResetPasswordForm(true)');
  }
}


// Please keep this unchanged. Thank you!
var pbl=null;

// Please keep this unchanged. Thank you!
function fixPBL(pobj) {
  if (pobj.style.display!='none') {
    pbl.style.display='';
    moveToCenter(pbl);
    setTimeout("pbl.style.top=($('"+pobj.id+"').scrollHeight+4+getTopPos($('"+pobj.id+"')))+'px';", 10);
  }
}