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
 * Init window
 * @param   int       login_length_min      Minimum allowed username length
 * @param   int       login_length_max      Maximum allowed username length
 */
function initNewUserForm(login_length_min, login_length_max) {
  if (typeof(login_length_min)=='number' && login_length_min>0) LoginLengthMin=login_length_min;
  if (typeof(login_length_max)=='number' && login_length_max>0) LoginLengthMax=login_length_max;
  $('new_user_name').value='';
  $('new_user_email').value='';
  $('new_user_password0').value='';
  $('new_user_password1').value='';
  $('new_user_name').maxLength=LoginLengthMax;
  $('new_user_name').focus();
}


/**
 * Add new user
 */
function addNewUser() {
  var errors=new Array();

  $('new_user_name').value=trimString($('new_user_name').value);
  $('new_user_email').value=trimString($('new_user_email').value);

  if ($('new_user_name').value=='') {
    errors.push(getLng('username_empty'));
  } else if ($('new_user_name').value.length<LoginLengthMin || $('new_user_name').value.length>LoginLengthMax) {
    errors.push((getLng('username_length_error').split('[MIN]').join(LoginLengthMin)).split('[MAX]').join(LoginLengthMax));
  }

  if (!checkEmail($('new_user_email').value)) {
    errors.push(getLng('email_invalid'));
  }

  if ($('new_user_password0').value=='') {
    errors.push(getLng('password_empty'));
  } else if ($('new_user_password0').value.length<3) {
    errors.push(getLng('password_too_short'));
  } else if ($('new_user_password0').value != $('new_user_password1').value) {
    errors.push(getLng('passwords_not_ident'));
  }

  if (errors.length>0) {
    alert('- '+errors.join("\n- "));
  } else {
    sendData('_CALLBACK_addNewUser()', formlink, 'POST', 'ajax=add_new_user'
                                                        +'&s_id='+urlencode(s_id)
                                                        +'&login='+urlencode($('new_user_name').value)
                                                        +'&email='+urlencode($('new_user_email').value)
                                                        +'&password='+urlencode($('new_user_password0').value)
                                                        );
  }
  return false;
}
function _CALLBACK_addNewUser() {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  if (actionHandler.status==0) {
    // Register successfull
    alert(actionHandler.message, null, null, 'initNewUserForm()');
  } else {
    // Register failed
    alert(actionHandler.message);
  }
}
