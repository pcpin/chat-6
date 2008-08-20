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
 * Main input text area handler
 * @var object
 */
var MainInputTextArea=null;

/**
 * Chat messages area handler
 * @var object
 */
var ChatMessagesArea=null;

/**
 * Controls area handler
 * @var object
 */
var ChatControlsArea=null;


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

  MainInputTextArea=$('main_input_textarea');
  ChatMessagesArea=$('chatroom_messages');
  ChatControlsArea=$('chatroom_controls');

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
  ChatMessagesArea.style.backgroundColor='#'+window.opener.messagesAreaBGColor;

  // Prepare areas
  setAreas();

  // Set onResize window handler
  window.onresize=function() {
    clearTimeout(windowResizeTimeoutHandler);
    windowResizeTimeoutHandler=setTimeout('setAreas()', 200);
    MainInputTextArea.click();
  };
  // Set message history handler
  MainInputTextArea.msgHistorie=new Array();
  MainInputTextArea.msgHistoriePtr=0;
  MainInputTextArea.gotFromHistory=false;
  MainInputTextArea.addMsgHistorie=function() {
    var msg=trimString(this.value);
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
  MainInputTextArea.fromMsgHistorie=function(direction) {
    if (this.msgHistorie.length) {
      this.msgHistoriePtr+=direction;
      if (direction>0 && this.msgHistoriePtr>=this.msgHistorie.length) {
        this.msgHistoriePtr=0;
      } else if (direction==-1 && this.msgHistoriePtr<0) {
        this.msgHistoriePtr=this.msgHistorie.length-1;
      }
      this.value=this.msgHistorie[this.msgHistoriePtr];
      this.gotFromHistory=true;
    }
  }
  // Set onkeydown handler for input area
  MainInputTextArea.onkeydown=function(e) {
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
        kk=e.which;
      } else if(typeof(e.charCode)=='number') {
        // Other NS and Mozilla versions
        kk=e.charCode;
      }
      if (kk==13 && !e.shiftKey) {
        // [Enter] sends a message, [Shift]+[Enter] inserts a line break
        $('mainSendMessageButton').click();
        return false;
      } else if (kk==38 && e.shiftKey) {
        // [Shift]+[CursorUp]
        if (trimString(this.value)!='' && !this.gotFromHistory) {
          // Store not sent text, if non-empty and not stored yet
          this.addMsgHistorie();
          this.fromMsgHistorie(-1);
        }
        // Get value from history
        this.fromMsgHistorie(-1);
        return false;
      } else if (kk==40 && e.shiftKey) {
        // [Shift]+[CursorDown]
        if (trimString(this.value)!='' && !this.gotFromHistory) {
          // Store not sent text, if non-empty and not stored yet
          this.addMsgHistorie();
        }
        // Get value from history
        this.fromMsgHistorie(1);
        return false;
      }
    }
    if (this.value.length>window.opener.messageLengthMax) {
      this.value=this.value.substring(0, window.opener.messageLengthMax);
    }
    return true;
  };
  // Set onkeyup handler for input area (Opera Behaviour)
  if (isOpera) {
    MainInputTextArea.onkeyup=function(e) {
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
          kk=e.which;
        } else if(typeof(e.charCode)=='number') {
          // Other NS and Mozilla versions
          kk=e.charCode;
        }
        if (kk==13 && !e.shiftKey) {
          this.value='';
          return false;
        }
      }
      return true;
    };
  }
  // Set onmouseup handler for input area
  MainInputTextArea.onmouseup=function(e) {
    this.value=this.value.substring(0, messageLengthMax);
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
      MainInputTextArea.style.fontFamily=sel.options[sel.selectedIndex].value;
      sel.onchange=function() {
        this.style.fontFamily=this.value;
        MainInputTextArea.style.fontFamily=this.value;
        if (isOpera) {
          // Opera hack
          MainInputTextArea.style.display='none';
          setTimeout('MainInputTextArea.style.display=\'\'; setAreas();', 1);
        } else {
          setAreas();
        }
      }
      sel.onclick=sel.onchange;
      sel.onblur=function() {
        this.onchange;
        if (isOpera) {
          // Opera hack
          setTimeout('MainInputTextArea.focus()', 1);
        } else {
          MainInputTextArea.focus();
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
        MainInputTextArea.style.fontSize=sel.options[sel.selectedIndex].value+'px';
        sel.onchange=function() {
          MainInputTextArea.style.fontSize=this.value+'px';
          if (isOpera) {
            // Opera hack
            MainInputTextArea.style.display='none';
            setTimeout('MainInputTextArea.style.display=\'\'; setAreas();', 1);
          } else {
            setAreas();
          }
        }
        sel.onclick=sel.onchange;
        sel.onblur=function() {
          this.onchange;
          if (isOpera) {
            // Opera hack
            setTimeout('MainInputTextArea.focus()', 1);
          } else {
            MainInputTextArea.focus();
          }
        };
        sel.onkeyup=sel.onchange;
      }
    }
  }

  // Set color to button
  $('message_colors_btn').style.backgroundColor='#'+opener.outgoingMessageColor;
  // Set color to input area
  MainInputTextArea.style.color='#'+opener.outgoingMessageColor;
  // Set focus to input area
  MainInputTextArea.focus();
  if (!isMozilla) {
    // Assign "onfocus" window event
    window.onfocus=function() { MainInputTextArea.focus(); }
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
    ChatMessagesArea.style.top='0px';
    ChatControlsArea.style.height=initialControlsHeight+'px';
    ChatControlsArea.style.left='0px';
    ChatMessagesArea.style.width=(winWidth+(isIE? 2 : 0))+'px';
    ChatMessagesArea.style.left='0px';
    ChatMessagesArea.style.height=(winHeight-initialControlsHeight+1+(isIE? 2 : 0))+'px';
    ChatControlsArea.style.top=(winHeight-initialControlsHeight+(isIE? 2 : 0))+'px';
    ChatControlsArea.style.width=(winWidth+(isIE? 2 : 1))+'px';
    MainInputTextArea.style.width=(winWidth-$('mainSendMessageButton').scrollWidth-35)+'px';
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
    sendData('_CALLBACK_getUserDataPM('+user_id+')', formlink, 'POST', 'ajax=get_memberlist&s_id='+urlencode(s_id)+'&user_ids='+urlencode(user_id)+'&load_custom_fields=1');
  }
}
function _CALLBACK_getUserDataPM(user_id) {
//debug(actionHandler.getResponseString()); return false;
  if (actionHandler.status==-1) {
    // Session is invalid
    window.close();
    opener.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (actionHandler.data['member'].length==0) {
      window.close();
    } else {
      var profile_data=actionHandler.data['member'][0];
      var gender='-';
      for (var iii=0; iii<profile_data['custom_field'].length; iii++) {
        if (profile_data['custom_field'][iii]['name'][0]=='gender') {
          gender=profile_data['custom_field'][iii]['field_value'][0];
          break;
        }
      }
      UserList.addRecord(user_id,
                         profile_data['nickname'][0],
                         profile_data['online_status'][0],
                         profile_data['online_status_message'][0],
                         '1'==profile_data['muted_locally'][0],
                         '1'==profile_data['global_muted'][0],
                         profile_data['global_muted_until'][0],
                         profile_data['ip_address'][0],
                         gender,
                         parseInt(profile_data['avatar_bid'][0])
                         );
    }
  }
  toggleProgressBar(false);
}
