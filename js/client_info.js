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
 * Initialize client info
 * @param   int       user_id       User ID
 */
function initClientInfo(user_id) {
  // Get client info
  getClientInfo(user_id);
  // Get focus
  window.focus();
}


/**
 * Get client info
 * @param   int   user_id     User ID
 */
function getClientInfo(user_id) {
  if (typeof(user_id)=='number' && user_id>0) {
    sendData('_CALLBACK_getClientInfo('+user_id+')', formlink, 'POST', 'ajax='+urlencode('get_client_info')+'&s_id='+urlencode(s_id)+'&user_id='+urlencode(user_id));
  }
}
function _CALLBACK_getClientInfo(user_id) {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  var client_data=actionHandler.getElement('client_data');
  var ip='';
  var host='';
  var agent='';
  var os='';
  var language='';
  var session_start='';
  if (status=='-1') {
    // Session is invalid
    window.close();
    document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (message=='OK') {
      // IP address
      $('client_ip').innerHTML=htmlspecialchars(actionHandler.getCdata('ip', 0, client_data, ''));

      // Host name
      $('client_host').innerHTML=htmlspecialchars(actionHandler.getCdata('host', 0, client_data, ''));

      // Agent name and version
      $('client_agent').innerHTML=htmlspecialchars(actionHandler.getCdata('agent', 0, client_data, ''));

      // OS
      $('client_os').innerHTML=htmlspecialchars(actionHandler.getCdata('os', 0, client_data, ''));

      // Language
      $('client_language').innerHTML=htmlspecialchars(actionHandler.getCdata('language', 0, client_data, ''));

      // Session start time
      $('client_session_start').innerHTML=htmlspecialchars(actionHandler.getCdata('session_start', 0, client_data, ''));

      // Display table
      $('client_table').style.display='';
    } else if (message!=null) {
      alert(message);
      window.close();
    }
  }
  toggleProgressBar(false);
}


/**
 * Get ping
 * @param   string    ip    IP address or hostname
 */
function getPing(ip) {
  sendData('_CALLBACK_getPing()', formlink, 'POST', 'ajax='+urlencode('get_ping')+'&s_id='+urlencode(s_id)+'&ip='+urlencode(ip), false, false);
}
function _CALLBACK_getPing() {
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  var ping_data=null;
  var ping=0;
  var ping_nr=0;
  var count=0;
  var ping_total=0.0;
  if (status=='-1') {
    // Session is invalid
    window.close();
    opener.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else if (status==0) {
    // Success
    if (null!=(ping_data=actionHandler.getElement('ping_data', 0))) {
      while(null!=(ping=actionHandler.getCdata('ping', ping_nr++, ping_data))) {
        ping=stringToNumber(ping);
        if (ping>0) {
          ping_total+=ping;
          count++;
        }
      }
      if (count>0 && ping_total>0) {
        $('client_ping').innerHTML=stringToNumber(ping_total/count, '.')+getLng('milliseconds_short');
      } else {
        $('client_ping').innerHTML=htmlspecialchars(getLng('failed'));
      }
    }
  } else {
    // Ping failed
    $('client_ping').innerHTML=htmlspecialchars(getLng('failed'));
  }
  $('client_ping').innerHTML+='&nbsp;&nbsp;&nbsp;';
  toggleProgressBar(false);
}
