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
 * Timeout handler for window.onresize event timeout
 * @var int
 */
var windowResizeTimeoutHandler=0;

/**
 * Initial control area height
 * @var int
 */
var initialControlsHeight=0;

/**
 * Flag: if TRUE, then auto-scroll function is active in this window
 * @var boolean
 */
var AutoScroll=true;



/**
 * Init PM box
 * @param   int       user_id           ID of user to communicate with
 * @param   int       controls_height   Controls area height in pixel
 */
function initPMBox(user_id, controls_height) {
  if (   typeof(user_id)!='number' || user_id<=0
      || typeof(window.opener)!='object' || window.opener==null
      || typeof(window.opener.pmOpened)!='object' && typeof(window.opener.pmOpened)!='function'
      || typeof(window.opener.pmClosed)!='object' && typeof(window.opener.pmClosed)!='function'
      ) {
    window.close();
    return false;
  }

  // Get client data
  getUserDataPM(user_id);

  initialControlsHeight=controls_height;

  // Set "onunload" handler
  window.onunload=function() {
    try {
      window.opener.pmClosed(window);
    } catch (e) {}
  }
  document.body.onunload=window.onunload;

  // Log by opener window
  window.tgt_user_id=stringToNumber(user_id);
  opener.pmOpened(window);

  // Messages area backgroud color
  $('chatroom_messages').style.backgroundColor='#'+window.opener.messagesAreaBGColor;

  // Prepare areas
  setAreas();

  // Set onResize window handler
  window.onresize=function() {
    clearTimeout(windowResizeTimeoutHandler);
    windowResizeTimeoutHandler=setTimeout('setAreas()', 200);
    $('main_input_textarea').click();
  };

  // Set message history handler
  $('main_input_textarea').msgHistorie=new Array();
  $('main_input_textarea').msgHistoriePtr=0;
  $('main_input_textarea').addMsgHistorie=function(msg) {
    msg=trimString(msg);
    if (msg!='') {
      this.msgHistoriePtr=0;
      if (this.msgHistorie.length==0 || this.msgHistorie[this.msgHistorie.length-1]!=msg) {
        this.msgHistorie.reverse();
        this.msgHistorie.splice(99, 1);
        this.msgHistorie.reverse();
        this.msgHistorie.push(msg);
      }
    }
  }
  $('main_input_textarea').fromMsgHistorie=function(direction) {
    if (this.msgHistorie.length) {
      this.msgHistoriePtr+=direction;
      if (direction>0 && this.msgHistoriePtr>=this.msgHistorie.length) {
        this.msgHistoriePtr=0;
      } else if (direction==-1 && this.msgHistoriePtr<0) {
        this.msgHistoriePtr=this.msgHistorie.length-1;
      }
      return this.msgHistorie[this.msgHistoriePtr];
    } else {
      return this.value;
    }
  }
  // Set onkeyup handler for input area
  $('main_input_textarea').onkeydown=function(e) {
    var kk=0;
    if(!e) {
      if(window.event) {
        e=window.event;
      }
    }
    if (e) {
      if (typeof(e.keyCode)=='number') {
        // DOM-compatible
        kk=e.keyCode;
      } else if(typeof(e.which)=='number') {
        // NS4
        kk=e.keyCode;
      } else if(typeof(e.charCode)=='number') {
        // Other NS and Mozilla versions
        kk=e.keyCode;
      }
      if (kk==13) {
        $('mainSendMessageButton').click();
        return false;
      } else if (kk==38) {
        // Arrow up
        this.value=this.fromMsgHistorie(-1);
      } else if (kk==40) {
        // Arrow down
        this.value=this.fromMsgHistorie(1);
      }
    }
    if (this.value.length>opener.messageLengthMax) {
      this.value=this.value.substring(0, opener.messageLengthMax);
    }
    return true;
  };
  // Set onmouseup handler for input area
  $('main_input_textarea').onmouseup=function(e) {
    if (this.value.length>opener.messageLengthMax) {
      this.value=this.value.substring(0, opener.messageLengthMax);
    }
    return false;
  }

  // Display fonts selection
  var fonts_list_span=$('available_fonts_list', opener.document);
  var sel=$('message_font_select');
  if (fonts_list_span && sel) {
    sel.options.length=0;
    var fonts_array=fonts_list_span.innerHTML.split('|');
    for (var i=0; i<fonts_array.length; i++) {
      fonts_array[i]=trimString(fonts_array[i]);
      if (fonts_array[i]!='') {
        sel.options[sel.options.length]=new Option(fonts_array[i], fonts_array[i]);
        sel.options[sel.options.length-1].style.fontFamily=fonts_array[i];
        if (opener.defaultFontFamily==fonts_array[i]) {
          sel.selectedIndex=sel.options.length-1;
        }
      }
    }
    if (sel.options.length>0) {
      $('main_input_textarea').style.fontFamily=sel.options[sel.selectedIndex].value;
      sel.onchange=function() {
        this.style.fontFamily=this.value;
        $('main_input_textarea').style.fontFamily=this.value;
        if (isOpera) {
          // Opera hack
          $('main_input_textarea').style.display='none';
          setTimeout('$(\'main_input_textarea\').style.display=\'\'; setAreas();', 1);
        } else {
          setAreas();
        }
      }
      sel.onclick=sel.onchange;
      sel.onblur=function() {
        this.onchange;
        if (isOpera) {
          // Opera hack
          setTimeout('$(\'main_input_textarea\').focus()', 1);
        } else {
          $('main_input_textarea').focus();
        }
      };
      sel.onkeyup=sel.onchange;
      // Display font size selection
      var font_sizes=$('available_font_sizes_list', opener.document).innerHTML.split('|');
      sel=$('message_fontsize_select');
      sel.options.length=0;
      for (var i=0; i<font_sizes.length; i++) {
        font_sizes[i]=trimString(font_sizes[i]);
        if (font_sizes[i]!='') {
          $('message_fontsize_select_col').style.display='';
          sel.options[sel.options.length]=new Option(font_sizes[i], font_sizes[i]);
          if (opener.defaultFontSize==font_sizes[i]) {
            sel.selectedIndex=sel.options.length-1;
          }
        }
      }
      if (sel.options.length>0) {
        $('main_input_textarea').style.fontSize=sel.options[sel.selectedIndex].value+'px';
        sel.onchange=function() {
          $('main_input_textarea').style.fontSize=this.value+'px';
          if (isOpera) {
            // Opera hack
            $('main_input_textarea').style.display='none';
            setTimeout('$(\'main_input_textarea\').style.display=\'\'; setAreas();', 1);
          } else {
            setAreas();
          }
        }
        sel.onclick=sel.onchange;
        sel.onblur=function() {
          this.onchange;
          if (isOpera) {
            // Opera hack
            setTimeout('$(\'main_input_textarea\').focus()', 1);
          } else {
            $('main_input_textarea').focus();
          }
        };
        sel.onkeyup=sel.onchange;
      }
    }
  }

  // Set color to button
  $('message_colors_btn').style.backgroundColor='#'+opener.outgoingMessageColor;
  // Set color to input area
  $('main_input_textarea').style.color='#'+opener.outgoingMessageColor;
  // Set focus to input area
  $('main_input_textarea').focus();
  if (!isMozilla) {
    // Assign "onfocus" window event
    window.onfocus=function() { $('main_input_textarea').focus(); }
  }

  // Activate auto-scroll
  window.opener.setAutoScroll(true, window);
}


/**
 * Set initial size and position of all areas in chat room window
 */
function setAreas() {
  try {
    winWidth=getWinWidth()-2;
    winHeight=getWinHeight()-2;
    $('chatroom_messages').style.top='0px';
    $('chatroom_controls').style.height=initialControlsHeight+'px';
    $('chatroom_controls').style.left='0px';
    $('chatroom_messages').style.width=(winWidth+(isIE? 2 : 0))+'px';
    $('chatroom_messages').style.left='0px';
    $('chatroom_messages').style.height=(winHeight-initialControlsHeight+1+(isIE? 2 : 0))+'px';
    $('chatroom_controls').style.top=(winHeight-initialControlsHeight+(isIE? 2 : 0))+'px';
    $('chatroom_controls').style.width=(winWidth+(isIE? 2 : 1))+'px';
    $('main_input_textarea').style.width=(winWidth-$('mainSendMessageButton').scrollWidth-35)+'px';
    if (isIE) {
      $('scroll_ctl_btn').style.marginRight='5px';
    }
  } catch (e) {}
}


/**
 * Get user data
 * @param   int   user_id   User ID
 */
function getUserDataPM(user_id) {
  if (typeof(user_id)=='number' && user_id>0) {
    sendData('_CALLBACK_getUserDataPM('+user_id+')', formlink, 'POST', 'ajax='+urlencode('get_public_profile_data')+'&s_id='+urlencode(s_id)+'&user_id='+urlencode(user_id));
  }
}
function _CALLBACK_getUserDataPM(user_id) {
//debug(actionHandler.getResponseString()); return false;
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  var profile_data=actionHandler.getElement('profile_data');
  var nickname='';
  var avatar=null;
  var avatar_bid=0;

  if (status=='-1') {
    // Session is invalid
    window.close();
    opener.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (message=='OK') {
      if (null!=(avatar=actionHandler.getElement('avatar', 0, profile_data))) {
        avatar_bid=stringToNumber(actionHandler.getCdata('binaryfile_id', 0, avatar));
      }
      nickname=actionHandler.getCdata('nickname', 0, profile_data);
      UserList.addRecord(user_id,
                         nickname,
                         actionHandler.getCdata('online_status', 0, profile_data),
                         actionHandler.getCdata('online_status_message', 0, profile_data),
                         '1'==actionHandler.getCdata('muted_locally', 0, profile_data),
                         '1'==actionHandler.getCdata('global_muted', 0, profile_data),
                         actionHandler.getCdata('global_muted_until', 0, profile_data),
                         actionHandler.getCdata('ip_address', 0, profile_data),
                         actionHandler.getCdata('gender', 0, profile_data),
                         avatar_bid
                         );
    }
  }
  // Set window status
  setDefaultWindowStatus(getLng('private_message')+': '+coloredToPlain(nickname, false));
  toggleProgressBar(false);
}