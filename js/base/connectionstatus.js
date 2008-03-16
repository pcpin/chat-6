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
 * Hide connection box timeout handler
 * @var int
 */
var CloseConnectionStatusTimeout=0;


/**
 * Connection status
 * Values:
 *    0 : Idle/Completed
 *    1 : Sending
 *    2 : Receiving
 * @var int
 */
var ConnectionStatus=0;

/**
 * Update connection status
 * @param   int       connStatus    Connection status
 * @param   boolean   RS2correct    If false, then the brouser does not supports
 *                                  XMLHttpRequest.onreadystatechange(2) event correctly.
 */
function updateConnectionStatus(connStatus, RS2correct) {
  if (typeof(RS2correct)!='boolean') {
    RS2correct=false;
  }
  switch(connStatus) {
    case  0   :   // Idle/Completed
                  if (ConnectionStatus!=0) {
                    ConnectionStatus=connStatus;
                    showConnectionStatus();
                  }
      break;
    case  1   :   // Sending data
                  if (ConnectionStatus==0) {
                    ConnectionStatus=connStatus;
                    showConnectionStatus();
                  }
      break;
    case  2   :   // Receiving data
                  if (ConnectionStatus==1) {
                    ConnectionStatus=connStatus;
                    if(!RS2correct) {
                      setTimeout('showConnectionStatus();', 800);
                    } else {
                      showConnectionStatus(connStatus);
                    }
                  }
      break;
  }
}




/**
 * Display connection status
 */
function showConnectionStatus() {
  if (typeof(CommunicationIndicator)!='undefined' && CommunicationIndicator.style) {
    switch (ConnectionStatus) {

      default   :   
      case  0   :   // Idle/Completed
                    CloseConnectionStatusTimeout=setTimeout("CommunicationIndicator.style.display='none';", typeof(isOpera)=='boolean' && isOpera? 250 : 100);
      break;

      case  1   :   // Sending data
                    clearTimeout(CloseConnectionStatusTimeout);
                    CommunicationIndicator.style.display='';
      break;

      case  2   :   // Receiving data
                    clearTimeout(CloseConnectionStatusTimeout);
                    CommunicationIndicator.style.display='';
      break;

    }
  }
}
