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
 * Define sound to play after MP3 player initialisation ("welcome" sound)
 * See mp3_player.js
 */
PCPIN_MP3_Player_PlayLockedAfterInit='./sounds/welcome.mp3';

/** 
 * Default player volume (must be between 0 and 100)
 * @var int
 */
PCPIN_MP3_PlayerDefaultVolume=75;

/**
 * XmlHttpRequest handler for periodic updates
 * @var object
 */
var ajaxUpdater=new PCPIN_XmlHttpRequest();

/**
 * XmlHttpRequest handler for misc. purposes
 * @var object
 */
var ajaxMiscHandler=new PCPIN_XmlHttpRequest();

/**
 * XmlHttpRequest handler for banner data
 * @var object
 */
var ajaxBannersHandler=new PCPIN_XmlHttpRequest();

/**
 * Updater interval in seconds
 * @var int
 */
var updaterInterval=10;

/**
 * Userlist area width in pixels
 * @var int
 */
var userlistWidth=0;

/**
 * Userlist area position (-1: Left, 0: Off, 1:Right)
 * @var int
 */
var userlistPosition=0;

/**
 * Controls area height in pixels
 * @var int
 */
var controlsHeight=0;
var controlsHeightInit=0;

/**
 * Timeout handler for startUpdater() calls
 * @var int
 */
var updaterTimeoutHandler=0;

/**
 * Flag: if TRUE, then updater request has been sent and no response came yet
 * @var boolean
 */
var updaterBusy=false;

/**
 * Timeout handler for window.onresize event timeout
 * @var int
 */
var windowResizeTimeoutHandler=0;

/**
 * Flag: TRUE if room welcome message has been displayed
 * @var boolean
 */
var welcomeMessageDisplayed=false;

/**
 * Outgoing messages queue
 * @var object
 */
var outgoingMessages=new Array();

/**
 * Maximum allowed message length
 * @var int
 */
var messageLengthMax=0;

/**
 * Default message color
 * @var string
 */
var defaultMessageColor='';

/**
 * Messages area background color
 * @var string
 */
var messagesAreaBGColor='';

/**
 * Current color for outgoing messages
 * @var string
 */
var outgoingMessageColor='';

/**
 * Array of PM window handlers
 * @var object
 */
var pmHandlers=new Array();

/**
 * Default font family
 * @var string
 */
var defaultFontFamily='';

/**
 * Default font size
 * @var string
 */
var defaultFontSize='';

/**
 * Flag: TRUE if user is currently in "stealth" mode
 * @var boolean
 */
var stealthActivated=false;

/**
 * "Call moderator" window handler
 * @var object
 */
var moderatorCallWindow=null;

/**
 * Flag: if TRUE, then gender icons will be displayed in userlist
 * @var boolean
 */
var userlistGender=false;

/**
 * Flag: if TRUE, then avatar thumbs will be displayed in userlist
 * @var boolean
 */
var userlistAvatar=false;

/**
 * Avatar thumb width
 * @var int
 */
var userlistAvatarWidth=10;

/**
 * Avatar thumb height
 * @var int
 */
var userlistAvatarHeight=10;

/**
 * Flag: if TRUE, then "Admin" and "Moderator" flags will be displayed in userlist
 * @var boolean
 */
var userlistPrivileged=false;

/**
 * Flag: if TRUE, then message timestamp will be also displayed
 * @var boolean
 */
var displayTimeStamp=false;

/**
 * Flag: if TRUE, then sound effects will be allowed (if not blocked by server)
 * @var boolean
 */
var allowSounds=false;

/**
 * Array with message timestamp spans' ids
 * @var object
 */
var timestampSpans=new Array();

/**
 * Message timestamp spans' ids array index
 * @var int
 */
var timestampSpansIndex=0;

/**
 * How many message attachments are allowed?
 * @var int
 */
var MsgAttachmentsLimit=0;

/**
 * Currently active attached files
 * @var object
 */
var MsgAttachments=new Array();

/**
 * Flag: if TRUE, then auto-scroll function is active
 * @var boolean
 */
var AutoScroll=true;

/**
 * Top banner height in pixel
 * @var int
 */
var TopBannerHeight=0;

/**
 * Bottom banner height in pixel
 * @var int
 */
var BottomBannerHeight=0;

/**
 * Top/bottom banner refresh rate
 * @var int
 */
var BannerRefreshRate=0;

/**
 * Popup banner period
 * @var int
 */
var PopupBannerPeriod=0;

/**
 * Message banner period
 * @var int
 */
var MsgBannerPeriod=0;

/**
 * Array with display positions of available banners as KEY and last display time as VAL.
 * @var object
 */
var DisplayBannersData=null;

/**
 * Flag: TRUE, if top banner is enabled
 * @var boolean
 */
var TopBannerEnabled=false;

/**
 * Flag: TRUE, if bottom banner is enabled
 * @var boolean
 */
var BottomBannerEnabled=false;

/**
 * How many messages left before new banner in messages area will be displayed
 * @var int
 */
var MsgBannerMessagesLeft=0;

/**
 * Popup banner interval handler
 * @var int
 */
var PopupBannerInterval=0;

/**
 * Top banner refresh interval handler
 * @var int
 */
var TopBannerInterval=0;

/**
 * Bottom banner refresh interval handler
 * @var int
 */
var BottomBannerInterval=0;

/**
 * Smilie list position
 * @var int
 */
var SmiliesPosition=1;

/**
 * Smilies row height (in tool bar area) in pixel
 * @var int
 */
var SmiliesRowHeight=0;

/**
 * Border width of all areas as an array
 * @var object
 */
var AreaBorders=new Array();

/**
 * Handler for fixSmilieRow() interval
 * @var int
 */
var FixSmilieRowInterval=null;

/**
 * Smiliebox container div handler
 * @var object
 */
var SmilieBoxContainer=null;

/**
 * Room background image ID
 * @var int
 */
var BackgroundImageID=0;

/**
 * Room background image width
 * @var int
 */
var BackgroundImageWidth=0;

/**
 * Room background image height
 * @var int
 */
var BackgroundImageHeight=0;

/**
 * Handler for chatroom messages area
 * @var object
 */
var ChatroomMessages=null;

/**
 * Handler for chatroom controls area
 * @var object
 */
var ChatroomControls=null;

/**
 * Handler for chatroom userlist area
 * @var object
 */
var ChatroomUserlist=null;

/**
 * Handler for main input text area
 * @var object
 */
var MainInputTextArea=null;

/**
 * HTTP communication indicator area
 * @var object
 */
var CommunicationIndicator=null;

/**
 * HTTP communication indicator image
 * @var object
 */
var CommunicationIndicatorImg=null;

/**
 * Flag: TRUE, then next updater request will be executed without delay
 * @var boolean
 */
var UpdaterSkipDelay=false;

/**
 * Flag: TRUE, then next updater request will return full data
 * @var boolean
 */
var UpdaterGetFullData=false;

/**
 * Minimum time period in seconds between two messages sent by the same user
 * @var int
 */
var MessageDelay=0;

/**
 * Time of the last posted message
 * @var int
 */
var lastPostedMessageTime=0;




/**
 * Initialize client in the chat room
 * @var     int       room_id                 Current room ID
 * @var     int       updater_interval        Updater interval in seconds
 * @param   int       userlist_width          Userlist area width in pixels
 * @param   int       userlist_position       Userlist area position (-1: Left, 0: Off, 1:Right)
 * @param   int       controls_height         Controls area height in pixels
 * @param   int       message_length_max      Maximum allowed message length
 * @param   string    date_format             Date format
 * @param   boolean   stealth_activated       Flag: TRUE if user is currently in "stealth" mode
 * @param   boolean   userlist_gender         Flag: if TRUE, then gender icons will be displayed in userlist
 * @param   boolean   userlist_avatar         Flag: if TRUE, then avatar thumbs will be displayed in userlist
 * @param   boolean   userlist_privileged     Flag: if TRUE, then "Admin" and "Moderator" flags will be displayed in userlist
 * @param   boolean   display_time_stamp      Flag: if TRUE, then message timestamp will be also displayed
 * @param   boolean   allow_sounds            Flag: if TRUE, then sounds will be active
 * @param   string    message_color           Preferenced messages color
 * @param   string    default_room_bgcolor    Background color for messages area
 * @param   boolean   msg_attachments_limit   How many message attachments are allowed? 0: Disable feature.
 * @param   int       top_banner_height       Top banner height in pixel
 * @param   int       bottom_banner_height    Bottom banner height in pixel
 * @param   int       banner_refresh_rate     Top/bottom banner refresh rate in seconds
 * @param   int       popup_banner_period     Popup banner display period
 * @param   int       msg_banner_period       Message banner display period
 * @param   int       smilies_position        Smilie list position (0=Toolbar area, 1=Separate window)
 * @param   int       smilies_row_height      If smilies_position==0: Smilies area height in pixel
 * @param   int       msg_delay               Minimum time period in seconds between two messages sent by the same user
 */
function initChatRoom(room_id,
                      updater_interval,
                      userlist_width,
                      userlist_position,
                      controls_height,
                      message_length_max,
                      default_font_family,
                      default_font_size,
                      stealth_activated,
                      userlist_gender,
                      userlist_avatar,
                      userlist_privileged,
                      display_time_stamp,
                      allow_sounds,
                      message_color,
                      default_room_bgcolor,
                      msg_attachments_limit,
                      top_banner_height,
                      bottom_banner_height,
                      banner_refresh_rate,
                      popup_banner_period,
                      msg_banner_period,
                      smilies_position,
                      smilies_row_height,
                      msg_delay
                      ) {
  if (   typeof(room_id)=='number' && room_id>0
      && typeof(updater_interval)=='number' && updater_interval>0
      && typeof(userlist_width)=='number' && userlist_width>0
      && typeof(userlist_position)=='number'
      ) {

    updaterInterval=updater_interval;
    userlistPosition=userlist_position;
    userlistWidth=userlistPosition!=0? userlist_width : 0;
    messageLengthMax=message_length_max;
    defaultFontFamily=default_font_family;
    defaultFontSize=default_font_size;
    stealthActivated=stealth_activated;
    userlistGender=userlist_gender;
    userlistAvatar=userlist_avatar;
    userlistPrivileged=userlist_privileged;
    displayTimeStamp=!display_time_stamp; // See below
    allowSounds=!allow_sounds; // See below
    outgoingMessageColor=message_color;
    messagesAreaBGColor=default_room_bgcolor;
    MsgAttachmentsLimit=msg_attachments_limit;
    TopBannerHeight=top_banner_height;
    BottomBannerHeight=bottom_banner_height;
    BannerRefreshRate=banner_refresh_rate;
    PopupBannerPeriod=popup_banner_period;
    MsgBannerPeriod=msg_banner_period;
    SmiliesPosition=smilies_position;
    SmiliesRowHeight=SmiliesPosition==0? smilies_row_height : 0;
    MessageDelay=msg_delay;
    controlsHeight=controls_height+SmiliesRowHeight;
    controlsHeightInit=controlsHeight;
    ChatroomMessages=$('chatroom_messages');
    ChatroomControls=$('chatroom_controls');
    ChatroomUserlist=$('chatroom_userlist');
    MainInputTextArea=$('main_input_textarea');
    CommunicationIndicator=$('CommunicationIndicator');
    CommunicationIndicatorImg=$('CommunicationIndicatorImg');
    // Initialize "Timestamp" button
    invertTimeStampView();
    // Initialize "Sounds On/Off" button
    toggleSounds();
    // Set CSS classes
    setCssClass(ChatroomMessages, '#chatroom_messages');
    setCssClass(ChatroomControls, '#chatroom_controls');
    setCssClass(ChatroomUserlist, '#chatroom_userlist');
    // Get border width of all areas
    AreaBorders['chatroom_messages']=getObjectBorders(ChatroomMessages);
    AreaBorders['chatroom_controls']=getObjectBorders(ChatroomControls);
    AreaBorders['chatroom_userlist']=getObjectBorders(ChatroomUserlist);
    // Messages area background color
    ChatroomMessages.style.backgroundColor='#'+messagesAreaBGColor;
    // Disable scrollbars
    $$('HTML')[0].style.overflow='hidden';
    $$('BODY')[0].style.overflow='hidden';
    // Set initial size and position of all areas in chat room window
    setAreas();
    // Set onResize window handler
    window.onresize=function(force) {
      clearTimeout(windowResizeTimeoutHandler);
      if (typeof(force)=='boolean' && force==true) {
        setAreas();
      } else {
        windowResizeTimeoutHandler=setTimeout('setAreas()', 200);
      }
      if (MainInputTextArea.click) {
        MainInputTextArea.click();
      }
      MainInputTextArea.focus();
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
      if (this.value.length>messageLengthMax) {
        this.value=this.value.substring(0, messageLengthMax);
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
      if (this.value.length>messageLengthMax) {
        this.value=this.value.substring(0, messageLengthMax);
      }
      return false;
    }

    // Display fonts selection
    var fonts_list_span=$('available_fonts_list');
    var sel=$('message_font_select');
    if (fonts_list_span && sel) {
      sel.options.length=0;
      var fonts_array=fonts_list_span.innerHTML.split('|');
      for (var i=0; i<fonts_array.length; i++) {
        fonts_array[i]=trimString(fonts_array[i]);
        if (fonts_array[i]!='') {
          sel.options[sel.options.length]=new Option(fonts_array[i], fonts_array[i]);
          sel.options[sel.options.length-1].style.fontFamily=fonts_array[i];
          if (default_font_family==fonts_array[i]) {
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
            setTimeout('MainInputTextArea.style.display=\'\'', 1);
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
        var font_sizes=$('available_font_sizes_list').innerHTML.split('|');
        sel=$('message_fontsize_select');
        sel.options.length=0;
        for (var i=0; i<font_sizes.length; i++) {
          font_sizes[i]=trimString(font_sizes[i]);
          if (font_sizes[i]!='') {
            $('message_fontsize_select_col').style.display='';
            sel.options[sel.options.length]=new Option(font_sizes[i], font_sizes[i]);
            if (default_font_size==font_sizes[i]) {
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
              setTimeout('MainInputTextArea.style.display=\'\'', 1);
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
    // Set "onunload" handler
    window.onunload=function() {
      // Send "Page unloaded" signal to server
      if (!SkipPageUnloadedMsg) {
        openWindow(formlink+'?inc='+urlencode('page_unloaded')+'&s_id='+urlencode(s_id), '', 1, 1, false, false, false, false, false, false, false, false, false, false, 0, 0);
      }
      // Close all PM boxes
      for (var i in pmHandlers) {
        if (pmHandlers[i]) {
          try {
            pmHandlers[i].close();
          } catch (e) {}
        }
      }
      // Close File upload window
      try {
        if (uploadWindow) {
          uploadWindow.close();
        }
      } catch (e) {}
      // Close "Call moderator" window
      try {
        if (moderatorCallWindow) {
          moderatorCallWindow.close();
        }
      } catch (e) {}
    }
    // Set focus to input area
    MainInputTextArea.focus();
    if (isIE || isOpera) {
      // Assign "onfocus" window event
      window.onfocus=function() { try { MainInputTextArea.focus(); } catch (e) {} }
    }
    // Start periodic updates
    startUpdater(true, true, true);
  }
  // Clear attached files
  displayAttachments();
  // Activate auto-scroll
  setAutoScroll(true);
  // Disable sounds
  if (!allowSounds) {
    if (typeof(PCPIN_MP3_Player)!='undefined' && PCPIN_MP3_Player) {
      PCPIN_MP3_Player.setVolume(0);
      setTimeout('PCPIN_MP3_Player.setVolume(0)', 500);
    } else {
      PCPIN_MP3_Player_PlayLockedAfterInit='';
    }
  }

  setTimeout('window.onresize()', 150);
}


/**
 * Set initial size and position of all areas in chat room window, apply some CSS attributes
 */
function setAreas() {
  var top_offset=0;
  var bottom_offset=0;
  var controlsHeight_local=controlsHeight;
  var userlist_width=0;
  var SmiliesRowHeight_local=SmiliesRowHeight;
  var chatroom_top_banner=$('chatroom_top_banner');
  var chatroom_bottom_banner=$('chatroom_bottom_banner');
  var smilie_selection_box=$('smilie_selection_box');

  try {
    if ($('attached_files').style.display!='none') {
      controlsHeight_local+=$('attached_files').scrollHeight;
    }
    winWidth=getWinWidth()-2;
    winHeight=getWinHeight()-2;

    if (TopBannerEnabled) {
      top_offset=TopBannerHeight;
      chatroom_top_banner.style.display='';
    } else {
      chatroom_top_banner.style.display='none';
    }

    if (BottomBannerEnabled) {
      bottom_offset=BottomBannerHeight;
      chatroom_bottom_banner.style.display='';
    } else {
      chatroom_bottom_banner.style.display='none';
    }

    switch (userlistPosition) {

      case -1:
        // Left
        ChatroomUserlist.style.left='0px';
        ChatroomMessages.style.width=(winWidth-userlistWidth-AreaBorders['chatroom_messages'][1]-AreaBorders['chatroom_messages'][3]+2)+'px';
        ChatroomMessages.style.left=userlistWidth+'px';
        ChatroomControls.style.left=userlistWidth+'px';
        ChatroomControls.style.width=(winWidth-userlistWidth-AreaBorders['chatroom_controls'][1]-AreaBorders['chatroom_controls'][3]+2)+'px';
        CommunicationIndicator.style.top=(winHeight-CommunicationIndicatorImg.height-1)+'px';
        CommunicationIndicator.style.left='1px';
      break;

      case  0:
        // Hide userlist
        userlistWidth=0;
        ChatroomUserlist.style.display='none';
        ChatroomMessages.style.width=(winWidth-AreaBorders['chatroom_messages'][1]-AreaBorders['chatroom_messages'][3]+2)+'px';
        ChatroomMessages.style.left='0px';
        ChatroomControls.style.left='0px';
        ChatroomControls.style.width=(winWidth-AreaBorders['chatroom_controls'][1]-AreaBorders['chatroom_controls'][3]+2)+'px';
        CommunicationIndicator.style.top='1px';
        CommunicationIndicator.style.left=(winWidth-CommunicationIndicatorImg.width-1)+'px';
      break;

      case 1:
      default:
        // Right (default)
        ChatroomUserlist.style.left=(winWidth-userlistWidth+2)+'px';
        ChatroomMessages.style.width=(winWidth-userlistWidth-AreaBorders['chatroom_messages'][1]-AreaBorders['chatroom_messages'][3]+2)+'px';
        ChatroomMessages.style.left='0px';
        ChatroomControls.style.left='0px';
        ChatroomControls.style.width=(winWidth-userlistWidth-AreaBorders['chatroom_controls'][1]-AreaBorders['chatroom_controls'][3]+2)+'px';
        CommunicationIndicator.style.top=(winHeight-CommunicationIndicatorImg.height-1)+'px';
        CommunicationIndicator.style.left=(winWidth-CommunicationIndicatorImg.width-1)+'px';
      break;

    }

    if (SmiliesPosition==0 && (winWidth-userlistWidth-10)<smilie_selection_box.scrollWidth) {
      SmiliesRowHeight_local+=20;
      controlsHeight_local+=20;
    }

    ChatroomMessages.style.fontFamily=defaultFontFamily;
    ChatroomMessages.style.fontSize=defaultFontSize;
    ChatroomMessages.style.height=(winHeight-controlsHeight_local-top_offset-bottom_offset-AreaBorders['chatroom_messages'][0]-AreaBorders['chatroom_messages'][2]+1)+'px';
    ChatroomMessages.style.top=top_offset+'px';
    ChatroomUserlist.style.width=(userlistWidth-AreaBorders['chatroom_userlist'][1]-AreaBorders['chatroom_userlist'][3])+'px';
    ChatroomUserlist.style.height=(winHeight-top_offset-bottom_offset-AreaBorders['chatroom_userlist'][0]-AreaBorders['chatroom_userlist'][2]+2)+'px';
    ChatroomUserlist.style.top=top_offset+'px';
    ChatroomControls.style.height=(controlsHeight_local-AreaBorders['chatroom_controls'][0]-AreaBorders['chatroom_controls'][2]+1)+'px';
    ChatroomControls.style.top=(winHeight-controlsHeight_local+1-bottom_offset)+'px';
    MainInputTextArea.style.width=(winWidth-userlistWidth-$('mainSendMessageButton').scrollWidth-30+(isMozilla? 16 : 0)+(isOpera? 18 : 0))+'px';
    MainInputTextArea.style.height=(controlsHeight-SmiliesRowHeight-38)+'px';
    // Banners
    chatroom_top_banner.style.width=(winWidth+2)+'px';
    chatroom_top_banner.style.top='0px';
    chatroom_top_banner.style.left='0px';
    chatroom_top_banner.style.height=TopBannerHeight+'px';
    chatroom_bottom_banner.style.width=(winWidth+2)+'px';
    chatroom_bottom_banner.style.top=(winHeight-BottomBannerHeight+2)+'px';
    chatroom_bottom_banner.style.left='0px';
    chatroom_bottom_banner.style.height=BottomBannerHeight+'px';
    userlist_width=parseInt(ChatroomUserlist.style.width);
    if ($('chatroom_userlist_room_selection')) {
      if (userlist_width>50) {
        $('chatroom_userlist_room_selection').style.width=(userlist_width-40)+'px';
      } else {
        $('chatroom_userlist_room_selection').style.width=userlist_width+'px';
      }
    }

    // Smilies row
    if (SmiliesPosition==0) {
      $('smilies_btn').style.display='none';
      openSmilieBox('main_input_textarea', null, null, true);
      smilie_selection_box.style.border='0px';
      smilie_selection_box.style.top=(getTopPos(MainInputTextArea)+controlsHeight-SmiliesRowHeight+(isIE? 2 : 0)-32)+'px';
      smilie_selection_box.style.left=getLeftPos(MainInputTextArea)+'px';
      smilie_selection_box.style.width=(winWidth-userlistWidth-10)+'px';
      smilie_selection_box.style.backgroundColor='transparent';
      smilie_selection_box.style.overflowY='hidden';
      smilie_selection_box.style.overflowX='auto';
      smilie_selection_box.style.height=SmiliesRowHeight_local+'px';
    } else {
      $('smilies_btn').style.display='';
    }
    // Adjust background image position
    fixBackgroundImagePos();
    if (SmiliesPosition==0) {
      clearInterval(FixSmilieRowInterval);
      FixSmilieRowInterval=setInterval('fixSmilieRow()', 500);
    }
  } catch (e) {}
}


/**
 * Fix smilies row in toolbar
 */
function fixSmilieRow() {
  if (SmiliesPosition==0) {
    if (SmilieBoxContainer==null) {
      SmilieBoxContainer=$('smilie_selection_box');
    }
    if (SmilieBoxContainer!=null) {
      var containerHeight=SmilieBoxContainer.scrollHeight;
      var bottom_overhead=winHeight-getTopPos(SmilieBoxContainer)-containerHeight;
      if (bottom_overhead<0) {
        SmiliesRowHeight=containerHeight;
        controlsHeight=controlsHeightInit+SmiliesRowHeight;
        window.onresize();
      }
    }
  }
}


/**
 * Start periodic updates
 * @param   boolean   now               If TRUE, then no pause will be performed before submitting the request
 * @param   boolean   full_request      If TRUE, then full room information and full userlist will be requested
 * @param   boolean   first_request     If TRUE, then full room information and full userlist will be requested,
 *                                      returned messages count will be limited by 'init_display_messages_count'
 * @param   int       get_last_msgs     If > 0, then last X messages will be requested from server
 *                                      (including already viewed messages)
 * @param   boolean   show_progressbar  If TRUE, then request will be executed in synchronous mode,
 *                                      with progress bar displayed
 */
function startUpdater(now, full_request, first_request, get_last_msgs, show_progressbar) {
  if (typeof(now)!='boolean') {
    now=false;
  }
  if (typeof(first_request)!='boolean') {
    first_request=false;
  } else if (first_request==true) {
    full_request=true;
  }
  if (typeof(full_request)!='boolean') {
    full_request=false;
  }
  if (typeof(get_last_msgs)!='number') {
    get_last_msgs=0;
  }
  if (typeof(show_progressbar)!='boolean') {
    show_progressbar=false;
  }
  clearTimeout(updaterTimeoutHandler);
  if (updaterInterval>0) {
    if (now) {
      sendUpdaterRequest(full_request, first_request, get_last_msgs, show_progressbar);
    } else {
      updaterTimeoutHandler=setTimeout('sendUpdaterRequest('+(full_request? 'true' : 'false')+', '+(first_request? 'true' : 'false')+', '+get_last_msgs+', '+(show_progressbar? 'true' : 'false')+')', now? 1 : updaterInterval*1000);
    }
  }
}


/**
 * Send updater request to server.
 * DO NOT CALL THIS FUNCTION DIRECTLY, use startUpdater() instead!
 * @param   boolean   full_request      If TRUE, then full room information and full userlist will be requested
 * @param   boolean   first_request     If TRUE, then full room information and full userlist will be requested,
 *                                      returned messages count will be limited by 'init_display_messages_count'
 * @param   int       get_last_msgs     If > 0, then last X messages will be requested from server
 *                                      (including already viewed messages)
 * @param   boolean   show_progressbar  If TRUE, then request will be executed in synchronous mode,
 *                                      with progress bar displayed
 */
function sendUpdaterRequest(full_request, first_request, get_last_msgs, show_progressbar) {
  if (false==updaterBusy) {
    updaterBusy=true;
    UpdaterSkipDelay=false;
    var outgoingMessages=MessageQueue.getAllRecordsOut();
    var postMessages=new Array();
    var msg_tpl='';
    if (outgoingMessages.length) {
      // Clear outgoing messages cueue
      for (var i=0; i<outgoingMessages.length; i++) {
        postMessages[postMessages.length]='new_messages['+i+'][type]='+urlencode(outgoingMessages[i].type);
        postMessages[postMessages.length]='new_messages['+i+'][offline]='+urlencode(outgoingMessages[i].offline);
        postMessages[postMessages.length]='new_messages['+i+'][date]='+urlencode(outgoingMessages[i].date);
        postMessages[postMessages.length]='new_messages['+i+'][target_user_id]='+urlencode(outgoingMessages[i].target_user_id);
        postMessages[postMessages.length]='new_messages['+i+'][target_room_id]='+urlencode(outgoingMessages[i].target_room_id);
        postMessages[postMessages.length]='new_messages['+i+'][privacy]='+urlencode(outgoingMessages[i].privacy);
        postMessages[postMessages.length]='new_messages['+i+'][body]='+urlencode(outgoingMessages[i].body);
        var css_properties='';
        for (var ii in outgoingMessages[i].css_properties) {
          if (css_properties.length>0) {
            css_properties+=';';
          }
          css_properties+=ii+':'+outgoingMessages[i].css_properties[ii];
        }
        postMessages[postMessages.length]='new_messages['+i+'][css_properties]='+urlencode(css_properties);
      }
      // Remove attachments from local array
      if (MsgAttachments.length>0) {
        MsgAttachments=new Array();
        displayAttachments();
      }
    }
    full_request=full_request||UpdaterGetFullData;
    UpdaterGetFullData=false;
    if (show_progressbar) {
      toggleProgressBar(true);
      setTimeout('ajaxUpdater.sendData(\'_CALLBACK_sendUpdaterRequest('+(show_progressbar? 'true' : 'false')+')\', \'POST\', formlink, \'ajax='+urlencode('chat_updater')+'&s_id='+urlencode(s_id)+'&room_id='+urlencode(currentRoomID)+(full_request? '&pref_timestamp='+(displayTimeStamp? '1' : '0')+'&pref_allow_sounds='+(allowSounds? '1' : '0')+'&pref_message_color='+urlencode(outgoingMessageColor)+'&full_request=1' : '')+(first_request? '&first_request=1' : '')+'&get_last_msgs='+get_last_msgs+((postMessages.length>0)? '&'+postMessages.join('&') : '')+'\', '+(show_progressbar? 'true' : 'false')+');', 10);
    } else {
      ajaxUpdater.sendData('_CALLBACK_sendUpdaterRequest('+(show_progressbar? 'true' : 'false')+')', 'POST', formlink, 'ajax='+urlencode('chat_updater')+'&s_id='+urlencode(s_id)+'&room_id='+urlencode(currentRoomID)+'&pref_timestamp='+(displayTimeStamp? '1' : '0')+'&pref_allow_sounds='+(allowSounds? '1' : '0')+'&pref_message_color='+urlencode(outgoingMessageColor)+(full_request? '&full_request=1' : '')+(first_request? '&first_request=1' : '')+'&get_last_msgs='+get_last_msgs+((postMessages.length>0)? '&'+postMessages.join('&') : ''), show_progressbar);
    }
  } else {
    UpdaterSkipDelay=true;
  }
}
/**
 * Callback handler for updater requests
 */
function _CALLBACK_sendUpdaterRequest(show_progressbar) {
//debug(ajaxUpdater.getResponseString()); return false;
//debug(ajaxUpdater.getResponseString());

  var room=null;
  var user=null;
  var user_nr=0;
  var userlist_refresh_needed=false;
  var banner_display_position=null;
  var banner_display_position_nr=0;
  var DisplayBannersData_old=null;
  var dummy_form=$('dummyform');
  var message_nr=0;
  var attachments=null;
  var attachment_nr=0;
  var attachment_id=0;
  var invitation_nr=0;

  try {
    switch (ajaxUpdater.status) {

      case  -1:
        // Session is invalid
        document.location.href=formlink+'?session_timeout';
        return false;
      break;

      case  100:
        // Session owner is not in a room
        dummy_form.s_id.value=s_id;
        dummy_form.inc.value='room_selection';
        dummy_form.ts.value=unixTimeStamp();
        dummy_form.submit();
        return false;
      break;

      case  200:
        // Session owner is in another room now
        dummy_form.s_id.value=s_id;
        dummy_form.inc.value='chat_room';
        dummy_form.ts.value=unixTimeStamp();
        dummy_form.submit();
        return false;
      break;

      case  300:
        // Room does not exists (anymore)
        dummy_form.s_id.value=s_id;
        dummy_form.inc.value='room_selection';
        dummy_form.ts.value=unixTimeStamp();
        dummy_form.submit();
        return false;
      break;

      case 0:
        // OK
        // Parse response:
        // ... category tree
        if (typeof(ajaxUpdater.data['category'])!='undefined') {
          // Search for current room data
          for (var i=0; i<ajaxUpdater.data['category'].length; i++) {
            for (var ii=0; ii<ajaxUpdater.data['category'][i]['room'].length; ii++) {
              room=ajaxUpdater.data['category'][i]['room'][ii];
              if (stringToNumber(room['id'][0])==currentRoomID) {
                // Data found
                // ... room name
                $('chatroom_userlist_room_name').innerHTML=htmlspecialchars(room['name'][0]);
                // ... message color
                defaultMessageColor=room['default_message_color'][0];
                if (outgoingMessageColor=='') {
                  outgoingMessageColor=defaultMessageColor;
                }
                MainInputTextArea.style.color='#'+outgoingMessageColor;
                $('message_colors_btn').style.backgroundColor='#'+outgoingMessageColor;
                // ... background image data
                BackgroundImageID=stringToNumber(room['background_image'][0]);
                BackgroundImageWidth=stringToNumber(room['background_image_width'][0]);
                BackgroundImageHeight=stringToNumber(room['background_image_height'][0]);
                fixBackgroundImagePos();
                // ... users
                userlist_refresh_needed=true;
                UserList.initialize();
                for (user_nr=0; user_nr<room['user'].length; user_nr++) {
                  user=room['user'][user_nr];
                  UserList.addRecord(stringToNumber(user['id'][0]),
                                     user['nickname'][0],
                                     user['online_status'][0],
                                     user['online_status_message'][0]!=''? user['online_status_message'][0] : getLng('online_status_'+user['online_status'][0]),
                                     '1'==user['muted_locally'][0],
                                     '1'==user['global_muted'][0],
                                     user['global_muted_until'][0],
                                     user['ip_address'][0],
                                     user['gender'][0],
                                     user['avatar_bid'][0],
                                     '1'==user['is_admin'][0],
                                     '1'==user['is_moderator'][0],
                                     null,
                                     null,
                                     null,
                                     null,
                                     null,
                                     null,
                                     null,
                                     null,
                                     null,
                                     null,
                                     null,
                                     '1'==user['is_guest'][0]
                                     );
                  if (stringToNumber(user['id'][0])==currentUserId && '1'==user['global_muted'][0]) {
                    gotGlobalUnMuted(true);
                  }
                }

                i=ajaxUpdater.data['category'].length;
                break;
              }
            }
          }
          updateRoomList(ajaxUpdater.data['category']);
        }
        // ... welcome message
        if (typeof(ajaxUpdater.data['welcome_message'])!='undefined' && false==welcomeMessageDisplayed) {
          welcomeMessageDisplayed=true;
          displayMessage(null, htmlspecialchars(ajaxUpdater.data['welcome_message'][0]), null, false);
        }
        // ... chat messages
        if (typeof(ajaxUpdater.data['chat_message'])!='undefined') {
          for (message_nr=0; message_nr<ajaxUpdater.data['chat_message'].length; message_nr++) {
            message=ajaxUpdater.data['chat_message'][message_nr];
            // Attachments?
            attachments=new Array();
            if ('1'==message['has_attachments'][0] && typeof(message['attachment'])!='undefined') {
              for (attachment_nr=0; attachment_nr<message['attachment'].length; attachment_nr++) {
                attachment_id=stringToNumber(message['attachment'][attachment_nr]['id'][0]);
                attachments[attachment_id]=new Array();
                attachments[attachment_id]['binaryfile_id']=stringToNumber(message['attachment'][attachment_nr]['binaryfile_id'][0]);
                attachments[attachment_id]['filename']=message['attachment'][attachment_nr]['filename'][0];
              }
            }
            // Process message
            processMessage(stringToNumber(message['id'][0]),
                           stringToNumber(message['type'][0]),
                           stringToNumber(message['offline'][0]),
                           stringToNumber(message['date'][0]),
                           stringToNumber(message['author_id'][0]),
                           message['author_nickname'][0],
                           stringToNumber(message['target_user_id'][0]),
                           stringToNumber(message['target_room_id'][0]),
                           stringToNumber(message['privacy'][0]),
                           message['body'][0],
                           message['css_properties'][0],
                           message['actor_nickname'][0],
                           attachments
                           );
          }
        }
        // ... invitations
        if (typeof(ajaxUpdater.data['invitation'])!='undefined') {
          for (invitation_nr=0; invitation_nr<ajaxUpdater.data['invitation'].length; invitation_nr++) {
            openWindow(formlink+'?s_id='+s_id+'&inc=invitation&invitation_id='+urlencode(ajaxUpdater.data['invitation'][invitation_nr]['id'][0]), 'invitation'+ajaxUpdater.data['invitation'][invitation_nr]['id'][0], 600, 300, false, false, false, false, true);
          }
        }
        // ... abuses
        if (typeof(ajaxUpdater.data['abuses'])!='undefined') {
          processAbuses(ajaxUpdater.data['abuses'][0]['abuse'], ajaxUpdater);
        }
      break;
    }
    // Display own online status
    showOwnOnlineStatus(UserList.getRecord(currentUserId).getOnlineStatus(), UserList.getRecord(currentUserId).getOnlineStatusMessage());
    if (true==userlist_refresh_needed) {
      // Refresh userlist
      redrawUserlist();
    }
    // Get banner display positions
    DisplayBannersData_old=DisplayBannersData;
    DisplayBannersData=null;
    if (typeof(ajaxUpdater.data['banner_display_position'])!='undefined') {
      for (banner_display_position_nr=0; banner_display_position_nr<ajaxUpdater.data['banner_display_position'].length; banner_display_position_nr++) {
        banner_display_position=ajaxUpdater.data['banner_display_position'][banner_display_position_nr];
        if (DisplayBannersData==null) {
          DisplayBannersData=new Array();
        }
        if (DisplayBannersData_old!=null && typeof(DisplayBannersData_old[banner_display_position])!='undefined') {
          // Banner already enabled
          DisplayBannersData[banner_display_position]=DisplayBannersData_old[banner_display_position];
        } else {
          DisplayBannersData[banner_display_position]=0;
          enableBanner(banner_display_position);
        }
      }
    }
    if (DisplayBannersData_old!=null) {
      for (var i in DisplayBannersData_old) {
        if (DisplayBannersData==null || typeof(DisplayBannersData[i])=='undefined') {
          // Banners of this display type ('top' or 'bottom') are not available anymore
          disableBanner(i);
        }
      }
    }
  } catch (e) {}
  // Start new updater round
  updaterBusy=false;
  if (typeof(show_progressbar)=='boolean' && show_progressbar==true) {
    toggleProgressBar(false);
  }
  startUpdater(outgoingMessages.length>0 || UpdaterSkipDelay);
}


/**
 * Adjust room background image position
 * @param   boolean   force   If TRUE, background image will be reloaded. Default: FALSE.
 */
function fixBackgroundImagePos() {
  if (BackgroundImageID>0) {
    if (BackgroundImageWidth>0 && BackgroundImageHeight>0) {
      ChatroomMessages.style.backgroundPosition=
         Math.round((parseInt(ChatroomMessages.style.width)-BackgroundImageWidth)/2+(!isIE && userlistPosition==-1? userlistWidth : 0))+'px '
        +Math.round((parseInt(ChatroomMessages.style.height)-BackgroundImageHeight)/2+(!isIE && TopBannerEnabled? parseInt(ChatroomMessages.style.top) : 0))+'px';
    }
  }
}


/**
 * Redraw userlist
 */
function redrawUserlist() {
  var records=UserList.getAllRecords();
  var urec_tpl=$('userlist_record_tpl').innerHTML;
  var urec='';
  var ulist_tbl_body=$('userlist_table_body');
  var newRow=null;
  var newCol=null;
  var status_img='';
  var status_title='';
  var muted_until=0;
  var gender='-';
  var avatar_bid=0;
  var ignored_img_suffix='';
  var your_profile_button=$('your_profile_button');
  var tmp=new Array();
  var tmp2='';

  // Delete current rows
  while (ulist_tbl_body.rows.length>0) {
    ulist_tbl_body.deleteRow(ulist_tbl_body.rows.length-1);
  }
  // Add new rows
  for (var user_id in records) {
    // Create new row
    newRow=document.createElement('TR');
    // Get user data
    gender=records[user_id].getGender();
    urec=urec_tpl;
    urec=urec.split('[ID]').join(user_id);
    // Gender icon
    if (userlistGender) {
      urec=urec.split('[GENDER_ICON]').join('<img src="./pic/gender_'+gender+'_10x10.gif" alt="'+htmlspecialchars(getLng('gender')+': '+getLng('gender_'+gender))+'" title="'+htmlspecialchars(getLng('gender')+': '+getLng('gender_'+gender))+'" border="0" />');
    } else {
      urec=urec.split('[GENDER_ICON]').join('');
    }
    // Avatar
    avatar_bid=records[user_id].getAvatarBID();
    if (userlistAvatar) {
      if (avatar_bid>0) {
        urec=urec.split('[AVATAR_THUMB]').join('<img style="cursor:pointer" onclick="showUserProfile('+user_id+')" src="'+htmlspecialchars(formlink)+'?b_x='+htmlspecialchars(userlistAvatarHeight)+'&amp;b_y='+htmlspecialchars(userlistAvatarWidth)+'&amp;b_id='+htmlspecialchars(avatar_bid)+'&amp;s_id='+htmlspecialchars(s_id)+'" onmouseover="showUserlistAvatarThumb(this, '+htmlspecialchars(avatar_bid)+')" onmouseout="hideUserlistAvatarThumb()" onclick="hideUserlistAvatarThumb()" alt="'+htmlspecialchars(getLng('avatar'))+'" title="'+htmlspecialchars(getLng('avatar'))+'" border="0" />');
      } else {
        urec=urec.split('[AVATAR_THUMB]').join('<img src="./pic/clearpixel_1x1.gif" width="'+htmlspecialchars(userlistAvatarWidth)+'" height="'+htmlspecialchars(userlistAvatarHeight)+'" alt="" title="" border="0" />');
      }
    } else {
      urec=urec.split('[AVATAR_THUMB]').join('');
    }
    if (stringToNumber(user_id)==currentUserId && your_profile_button) {
      // Update "Your profile" button
      tmp=your_profile_button.style.backgroundImage.split('b_id=');
      tmp2=parseInt(tmp[1]);
      if (tmp.length==2 && avatar_bid!=tmp2) {
        your_profile_button.style.backgroundImage=tmp[0]+'b_id='+avatar_bid+tmp[1].substring(numberToString(tmp2).length);
      }
    }
    // Nickname
    urec=urec.split('[NICKNAME_PLAIN]').join('"'+coloredToPlain(records[user_id].getNickname(), true)+'"');
    urec=urec.split('[NICKNAME_COLORED]').join(coloredToHTML(records[user_id].getNickname()));
    // Online status col
    newCol=document.createElement('TD');
    if (true==records[user_id].getMutedLocally()) {
      ignored_img_suffix='ignored_';
    } else {
      ignored_img_suffix='';
    }
    if (true==records[user_id].getGlobalMuted()) {
      status_img='./pic/online_status_muted_'+ignored_img_suffix+'10x10.gif';
      muted_until=records[user_id].getGlobalMutedUntil();
      if (muted_until==0) {
        status_title=getLng('permanently_globalmuted')+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
      } else {
        status_title=getLng('globalmuted_until').split('[EXPIRATION_DATE]').join(date(dateFormat, muted_until))+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
      }
    } else {
      status_img='./pic/online_status_'+records[user_id].getOnlineStatus()+'_'+ignored_img_suffix+'10x10.gif';
      status_title=records[user_id].getOnlineStatusMessage()+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
    }
    status_title=htmlspecialchars(status_title);
    urec=urec.split('[ONLINE_STATUS_ICON]').join('<img id="user_status_image_'+user_id+'" src="'+status_img+'" alt="'+status_title+'" title="'+status_title+'" />');
    // Admin
    if (userlistPrivileged && records[user_id].getIsAdmin()) {
      urec=urec.split('_admin_section').join(' onclick="alert(getLng(\'user_is_admin\').split(\'[USER]\').join(\''+htmlspecialchars(coloredToPlain(records[user_id].getNickname(), false))+'\')); return false;" ');
    } else {
      urec=urec.split('_admin_section').join(' style="display:none" ');
    }
    // Moderator
    if (userlistPrivileged && records[user_id].getIsModerator()) {
      urec=urec.split('_moderator_section').join(' onclick="alert(getLng(\'user_is_this_room_moderator\').split(\'[USER]\').join(\''+htmlspecialchars(coloredToPlain(records[user_id].getNickname(), false))+'\')); return false;" ');
    } else {
      urec=urec.split('_moderator_section').join(' style="display:none" ');
    }

    newCol.innerHTML=urec;
    setCssClass(newCol, '#chatroom_userlist_list');
    newRow.appendChild(newCol);
    // Append row to the table
    ulist_tbl_body.appendChild(newRow);
  }
}


/**
 * Incoming messages processor
 * All parameters are described in message.class.php
 * @param   string  id                  Message ID
 * @param   string  type                Message type
 * @param   string  offline             Offline flag
 * @param   string  date                Message date (UNIX timestamp)
 * @param   string  author_id           ID of message author
 * @param   string  author_nickname     Nickname of message author
 * @param   string  target_user_id      ID of message target user
 * @param   string  target_room_id      ID of message target room
 * @param   string  privacy             Privacy level
 * @param   string  body                Message body
 * @param   string  css_properties      Message CSS properties
 * @param   string  actor_nickname      Nickname of the "actor" user
 * @param   array   attachments         Message attachments
 */
function processMessage(id, type, offline, date, author_id, author_nickname, target_user_id, target_room_id, privacy, body, css_properties, actor_nickname, attachments) {
  try {
    var parts=body.split('/');
    var tmp=null;
    var tmp2=null;
    var userlist_refresh_needed=false;
    var kicked_user_id=0;
    var kicker_user_id=0;
    var kick_reason='';
    var banned_user_id=0;
    var banner_user_id=0;
    var ban_duration=0;
    var ban_reason='';
    var globalmuted_user_id=0;
    var globalmuter_user_id=0;
    var globalmute_duration=0;
    var globalmute_reason='';
    var globalmuted_user_id=0;
    var globalmuter_user_id=0;
    var nickname='';
    var displayMsgBanner=false;
    var opposite_user_id=0;

    if (typeof(actor_nickname)!='string') {
      actor_nickname='';
    }

    switch (type) {

      case  102: // User X changed online status
        tmp=UserList.getRecord(parts.shift());
        if (tmp) {
          tmp.setOnlineStatus(parts.shift());
          tmp.setOnlineStatusMessage(parts.shift());
          showOwnOnlineStatus(tmp.getOnlineStatus(), tmp.getOnlineStatusMessage());
          redrawUserlist();
        }
      break;

      case  111: // User X entered room Y (this room)
        tmp=UserList.getRecord(parts.shift());
        if (tmp) {
          nickname=tmp.getNickname();
        } else if (actor_nickname!='') {
          nickname=actor_nickname;
        }
        if (nickname!='') {
          body=parseMessage(getLng('user_entered_this_room'));
          body=body.split('[USER]').join(coloredToHTML(nickname));
          displayMessage(null, body, css_properties, true, date);
        }
        // Play system message sound
        playSound('system_message.mp3');
      break;

      case  115: // User X left room Y (this room)
        tmp=UserList.getRecord(parts.shift());
        if (tmp) {
          nickname=tmp.getNickname();
        } else if (actor_nickname!='') {
          nickname=actor_nickname;
        }
        if (nickname!='') {
          body=parseMessage(getLng('user_left_this_room'));
          body=body.split('[USER]').join(coloredToHTML(nickname));
          displayMessage(null, body, css_properties, true, date);
        }
        // Play system message sound
        playSound('system_message.mp3');
      break;

      case 3001: // User X posted a message
        if (author_nickname=='') {
          tmp=UserList.getRecord(parts.shift());
          if (tmp) {
            author_nickname=tmp.getNickname();
          } else if (actor_nickname!='') {
            author_nickname=actor_nickname;
          }
        }
        if (author_nickname!='') {
          if ((privacy==0 || privacy==1) && target_user_id>0 && UserList.getRecord(target_user_id)) {
            // "Said to" or "Whispered to" message
            // Check for opened PM boxes
            var pm_box_found=false;
            for (var pmh in pmHandlers) {
              if (pmHandlers[pmh]) {
                pm_box_found=true;
                break;
              }
            }
            displayMessage(author_nickname, parseMessage(body), css_properties, true, date, window, author_id, !pm_box_found, attachments, privacy, target_user_id);
            // Messages counter for banner
            if (MsgBannerMessagesLeft>0) {
              if (MsgBannerMessagesLeft==1) {
                displayMsgBanner=true;
                MsgBannerMessagesLeft=MsgBannerPeriod;
              } else {
                MsgBannerMessagesLeft--;
              }
            }
            // Play "whispered" message sound
            playSound('whispered_message.mp3');
          } else if (privacy==2) {
            // Display message in PM window
            opposite_user_id=author_id==currentUserId? target_user_id : author_id;
            if (pmHandlers[opposite_user_id] && pmHandlers[opposite_user_id].alert) {
              // PM window already opened
              displayMessage(author_nickname, parseMessage(body), css_properties, true, date, pmHandlers[opposite_user_id], author_id, false, attachments);
              // Play "private" message sound
              playSound('private_message.mp3');
            } else {
              // Push the message back to queue
              MessageQueue.addRecordIn(id, type, offline, date, author_id, author_nickname, target_user_id, target_room_id, privacy, body, css_properties, actor_nickname, attachments);
              // Open PM window
              openPMbox(opposite_user_id);
            }
          } else {
            // Display message in main window
            displayMessage(author_nickname, parseMessage(body), css_properties, true, date, window, author_id, false, attachments);
            // Messages counter for banner
            if (MsgBannerMessagesLeft>0) {
              if (MsgBannerMessagesLeft==1) {
                displayMsgBanner=true;
                MsgBannerMessagesLeft=MsgBannerPeriod;
              } else {
                MsgBannerMessagesLeft--;
              }
            }
            // Play "public" message sound
            playSound('public_message.mp3');
          }
        }
      break;

      case  10001: // Clear messages area
        flushMessagesArea();
      break;

      case  10101: // User was kicked
        kicked_user_id=stringToNumber(parts.shift());
        kicker_user_id=stringToNumber(parts.shift());
        kick_reason=parts.join('/');
        tmp=UserList.getRecord(kicked_user_id);
        if (actor_nickname=='') {
          tmp2=UserList.getRecord(kicker_user_id);
          if (tmp2) {
            actor_nickname=tmp2.getNickname();
          }
        }
        if (tmp && actor_nickname!='') {
          if (kick_reason!='') {
            body=parseMessage(getLng('user_kicked_with_reason'));
          } else {
            body=parseMessage(getLng('user_kicked_without_reason'));
          }
          body=body.split('[KICKED_USER]').join(coloredToHTML(tmp.getNickname()));
          body=body.split('[KICKER_USER]').join(coloredToHTML(actor_nickname));
          body=body.split('[REASON]').join(kick_reason);
          displayMessage(null, body, css_properties, true, date);
        }
      break;

      case  10105: // User was banned
      case  10106: // User+IP were banned
        banned_user_id=stringToNumber(parts.shift());
        banner_user_id=stringToNumber(parts.shift());
        ban_duration=stringToNumber(parts.shift());
        ban_reason=parts.join('/');
        tmp=UserList.getRecord(banned_user_id);
        if (actor_nickname=='') {
          tmp2=UserList.getRecord(banner_user_id);
          if (tmp2) {
            actor_nickname=tmp2.getNickname();
          }
        }
        if (tmp && actor_nickname!='') {
          if (ban_reason!='') {
            if (ban_duration>0) {
              body=parseMessage(getLng('user_banned_with_reason'));
            } else {
              body=parseMessage(getLng('user_banned_permanently_with_reason'));
            }
          } else {
            if (ban_duration>0) {
              body=parseMessage(getLng('user_banned_without_reason'));
            } else {
              body=parseMessage(getLng('user_banned_permanently_without_reason'));
            }
          }
          body=body.split('[MINUTES]').join(ban_duration);
          body=body.split('[BANNED_USER]').join(coloredToHTML(UserList.getRecord(banned_user_id).getNickname()));
          body=body.split('[BANNER_USER]').join(coloredToHTML(actor_nickname));
          body=body.split('[REASON]').join(ban_reason);
          displayMessage(null, body, css_properties, true, date);
        }
      break;

      case  10110: // User was global muted
        globalmuted_user_id=stringToNumber(parts.shift());
        globalmuter_user_id=stringToNumber(parts.shift());
        globalmute_duration=stringToNumber(parts.shift());
        globalmute_reason=parts.join('/');
        tmp=UserList.getRecord(globalmuted_user_id);
        if (actor_nickname=='') {
          tmp2=UserList.getRecord(globalmuter_user_id);
          if (tmp2) {
            actor_nickname=tmp2.getNickname();
          }
        }
        if (tmp) {
          if (globalmute_duration>0) {
            tmp.setGlobalMuted(1, unixTimeStamp()+globalmute_duration*60);
          } else {
            tmp.setGlobalMuted(1, 0);
          }
          if (tmp.ID==currentUserId) {
            gotGlobalUnMuted(true);
          }
          redrawUserlist();
        }
        if (actor_nickname!='') {
          if (globalmute_duration>0) {
            if (globalmute_reason!='') {
              body=parseMessage(getLng('user_globalmuted_with_reason'));
            } else {
              body=parseMessage(getLng('user_globalmuted_without_reason'));
            }
          } else {
            if (globalmute_reason!='') {
              body=parseMessage(getLng('user_globalmuted_permanently_with_reason'));
            } else {
              body=parseMessage(getLng('user_globalmuted_permanently_without_reason'));
            }
          }
          body=body.split('[MINUTES]').join(globalmute_duration);
          body=body.split('[GLOBALMUTED_USER]').join(coloredToHTML(UserList.getRecord(globalmuted_user_id).getNickname()));
          body=body.split('[GLOBALMUTER_USER]').join(coloredToHTML(actor_nickname));
          body=body.split('[REASON]').join(globalmute_reason);
          displayMessage(null, body, css_properties, true, date);
        }
      break;

      case  10111: // User was global unmuted
        globalmuted_user_id=stringToNumber(parts.shift());
        globalmuter_user_id=stringToNumber(parts.shift());
        tmp=UserList.getRecord(globalmuted_user_id);
        if (tmp) {
          tmp.setGlobalMuted(0, 0);
          if (tmp.ID==currentUserId) {
            gotGlobalUnMuted(false);
          }
          redrawUserlist();
        }
      break;

      case  10200: // Reload userlist
        UpdaterGetFullData=true;
      break;

    }
  } catch (e) {}
  if (displayMsgBanner) {
    loadMsgBanner();
  }
}

/**
 * Convert message body into HTML string
 * @param   string    body    Message body
 * @return  string
 */
function parseMessage(body) {
  var parsed='';
  var msg_parts=null;
  var found=false;
  var pos=0;
  var tmp='';
  var converted_parts=new Array();
  if (typeof(body)=='string' && body!='') {
    msg_parts=body.split(' ');

    // Convert URLs
    for (var i=0; i<msg_parts.length; i++) {
      if (msg_parts[i]!=' ') {
        tmp=msg_parts[i].toLowerCase();
        if (tmp.indexOf(':')>0) {
          tmp=tmp.split(':', 2);
          if (tmp.length==2 && tmp[1]!='') {
            switch (tmp[0]) {

              // URI://...
              case 'acap' :
              case 'cap' :
              case 'crid' :
              case 'dict' :
              case 'dns' :
              case 'ed2k' :
              case 'file' :
              case 'ftp' :
              case 'gopher' :
              case 'http' :
              case 'https' :
              case 'irc' :
              case 'ircs' :
              case 'lastfm' :
              case 'mms' :
              case 'nntp' :
              case 'rsync' :
              case 'sftp' :
              case 'smb' :
              case 'snmp' :
              case 'ssh' :
              case 'telnet' :
                if (tmp[1].length>2 && 0==tmp[1].indexOf('//')) {
                  // OK
                  msg_parts[i]='<a target="_blank" href="'+formlink+'?external_url='+urlencode(msg_parts[i])+'" title="'+msg_parts[i]+'">'+htmlspecialchars(msg_parts[i])+'</a>';
                  converted_parts[i]=1;
                }
              break;

              // URI://...
              case 'aaa' :
              case 'aaas' :
              case 'about' :
              case 'aim' :
              case 'callto' :
              case 'cid' :
              case 'data' :
              case 'dav' :
              case 'fax' :
              case 'feed' :
              case 'go' :
              case 'imap' :
              case 'imaps' :
              case 'ldap' :
              case 'mailto' :
              case 'mailto' :
              case 'mid' :
              case 'msnim' :
              case 'news' :
              case 'nfs' :
              case 'pop' :
              case 'pop3' :
              case 'pops' :
              case 'pop3s' :
              case 'pres' :
              case 'sip' :
              case 'sips' :
              case 'skype' :
              case 'tel' :
              case 'urn' :
              case 'wais' :
              case 'xmpp' :
              case 'ymsgr' :
                if (tmp[1].length>1) {
                  // OK
                  msg_parts[i]='<a target="_blank" href="'+formlink+'?external_url='+urlencode(msg_parts[i])+'" title="'+htmlspecialchars(msg_parts[i])+'">'+htmlspecialchars(msg_parts[i])+'</a>';
                  converted_parts[i]=1;
                }
              break;

            }
          }
        }
        if (typeof(converted_parts[i])=='undefined' && 0==msg_parts[i].indexOf('www.') && msg_parts[i].length>=9) {
          // HTTP link begins with "www."
          msg_parts[i]='<a target="_blank" href="'+formlink+'?external_url='+urlencode('http://'+msg_parts[i])+'" title="'+htmlspecialchars(msg_parts[i])+'">'+htmlspecialchars(msg_parts[i])+'</a>';
          converted_parts[i]=1;
        }
      }
    }

    // Convert smilies
    for (var i=0; i<msg_parts.length; i++) {
      if (typeof(converted_parts[i])=='undefined') {
        found=false;
        if (msg_parts[i]!='') {
          for (var code in SmilieList.SmilieList) {
            if (msg_parts[i]==code) {
              msg_parts[i]='<img src="'+SmilieList.SmilieList[code].source+'" alt="'+code+'" title="'+code+'" />';
              found=true;
              break;
            }
          }
          if (found==false) {
            msg_parts[i]=htmlspecialchars(msg_parts[i]);
          }
        }
      }

      body=msg_parts.join(' ');
    }
    parsed=body;
  }
  return parsed;
}


/**
 * Display a message
 * @param   string    author            Message author
 * @param   string    message           Message body
 * @param   string    css_properties    Message CSS properties
 * @param   boolean   show_date         If TRUE (default), then date will be displayed before message
 * @param   int       timestamp         Message post time (UNIX timestamp)
 * @param   object    tgt_window        Window handler to display message in. Default: main chat window.
 * @param   int       author_id         Optional. ID of message author
 * @param   boolean   do_focus          Optional. If TRUE, then window will get focus after displaying the message
 * @param   array     attachments       Optional. Message attachments
 * @param   int       privacy           Privacy level
 * @param   string    target_user_id    Optional. ID of message target user
 */
function displayMessage(author, message, css_properties, show_date, timestamp, tgt_window, author_id, do_focus, attachments, privacy, target_user_id) {
  if (typeof(tgt_window)=='undefined') {
    tgt_window=window;
  }
  var tgt_doc=tgt_window.document;
  var author_span=null;
  var date_span=null;
  var msg_span=null;
  var sp=null;
  var pair=null;
  var css_attr_name='';
  var css_attr_value='';
  var css_array=null;
  var now=new Date();

  if (typeof(message)=='string' && message!='' || attachments && attachments.length>0) {
    if ((typeof(show_date)!='boolean' || show_date) && typeof(timestamp)=='number' && timestamp>0) {
      // Message timestamp
      date_span=tgt_doc.createElement('SPAN');
      date_span.id='date_span_'+(++timestampSpansIndex);
      timestampSpans.push(date_span.id);
      // Keep array small (100 ids max)
      if (timestampSpans.length>100) {
        timestampSpans.splice(0, timestampSpans.length-100);
      }
      date_span.innerHTML='['+htmlspecialchars(date(dateFormat, timestamp))+'] ';
      if (defaultMessageColor!='') {
        date_span.style.color='#'+defaultMessageColor;
      }
      $('chatroom_messages_contents', tgt_doc).appendChild(date_span);
      if (displayTimeStamp) {
        date_span.style.display='';
      } else {
        date_span.style.display='none';
      }
    }
    if (typeof(author)=='string' && author!='') {
      author_span=tgt_doc.createElement('SPAN');
      if (typeof(author_id)=='number') {
        author_span.innerHTML= '<a href=":" onclick="showUserOptionsBox('+author_id+', this.title); return false;" oncontextmenu="showUserOptionsBox('+author_id+'); return false;" title="'+coloredToPlain(author, true)+'">'
                              +coloredToHTML(author)
                              +'</a>';
      } else {
        author_span.innerHTML='<b>'+coloredToHTML(author)+'</b>';
      }
      if (typeof(privacy)=='number' && typeof(target_user_id)=='number' && target_user_id>0 && UserList.getRecord(target_user_id)) {
        if (privacy==1) {
          // "Whispered to" message
          author_span.innerHTML+=' '+getLng('whispered_message').split('[USER]').join('<a href=":" onclick="showUserOptionsBox('+target_user_id+', this.title); return false;" oncontextmenu="showUserOptionsBox('+target_user_id+'); return false;" title="'+coloredToPlain(UserList.getRecord(target_user_id).Nickname, true)+'">'
                                                                                      +coloredToHTML(UserList.getRecord(target_user_id).Nickname)
                                                                                      +'</a>'
                                                                                      );
        } else {
          // "Sayd to" message
          author_span.innerHTML+=' '+getLng('said_message').split('[USER]').join('<a href=":" onclick="showUserOptionsBox('+target_user_id+', this.title); return false;" oncontextmenu="showUserOptionsBox('+target_user_id+'); return false;" title="'+coloredToPlain(UserList.getRecord(target_user_id).Nickname, true)+'">'
                                                                                 +coloredToHTML(UserList.getRecord(target_user_id).Nickname)
                                                                                 +'</a>'
                                                                                 );
        }
      }
      if (defaultMessageColor!='') {
        author_span.style.color='#'+defaultMessageColor;
      }
      $('chatroom_messages_contents', tgt_doc).appendChild(author_span);
      sp=tgt_doc.createElement('SPAN');
      if (defaultMessageColor!='') {
        sp.style.color='#'+defaultMessageColor;
      }
      sp.innerHTML='&nbsp;:&nbsp;';
      $('chatroom_messages_contents', tgt_doc).appendChild(sp);
    }
    msg_span=tgt_doc.createElement('SPAN');
    if (defaultMessageColor!='') {
      msg_span.style.color='#'+defaultMessageColor;
    }
    // Parse CSS attributes
    if (typeof(css_properties)=='string' && css_properties!='') {
      pair=null;
      css_attr_name='';
      css_attr_value='';
      css_array=css_properties.split(';');
      msg_span.style.setCssProperty=function(name, value) {
        eval('this.'+name+'=\''+value+'\'');
      };
      for (var i=0; i<css_array.length; i++) {
        css_array[i]=trimString(css_array[i]);
        if (css_array[i]!='' && -1!=css_array[i].indexOf(':')) {
          pair=css_array[i].split(':');
          if (pair.length==2) {
            pair[0]=trimString(pair[0]);
            pair[1]=trimString(pair[1]);
            if (pair[0]!='' && pair[1]!='') {
              css_attr_name=cssToJs(pair[0]);
              if (css_attr_name!='') {
                msg_span.style.setCssProperty(css_attr_name, pair[1]);
                // Apply fontSize for all spans
                if (css_attr_name=='fontSize') {
                  author_span.style.fontSize=pair[1];
                  if (typeof(sp)!='undefined' && sp) {
                    sp.style.fontSize=pair[1];
                  }
                  if (date_span && typeof(date_span.style)=='object' && date_span.style) {
                    date_span.style.fontSize=pair[1];
                  }
                }
              }
            }
          }
        }
      }
      msg_span.style.setCssProperty=null;
    }
    // Display attachments
    for (var i in attachments) {
      message+='&nbsp;&nbsp;'
              +'<a href="'+formlink+'?s_id='+htmlspecialchars(s_id)+'&amp;b_id='+urlencode(attachments[i]['binaryfile_id'])+'&amp;filename='+urlencode(attachments[i]['filename'])+'" target="_blank" title="'+htmlspecialchars(getLng('attachment')+': '+attachments[i]['filename'])+'">'
              +'<img src="./pic/attachment_10x10.gif" title="'+htmlspecialchars(getLng('attachment'))+'" alt="'+htmlspecialchars(getLng('attachment'))+'" />'
              +htmlspecialchars(attachments[i]['filename'])
              +'</a>';
    }
    msg_span.innerHTML=nl2br(message)+'<br />';
    $('chatroom_messages_contents', tgt_doc).appendChild(msg_span);
    if (tgt_window.AutoScroll) {
      try {
        $('chatroom_messages', tgt_doc).scrollTop=$('chatroom_messages', tgt_doc).scrollHeight;
      } catch (e) {
        msg_span.scrollIntoView(false);
      }
    }
    if (typeof(do_focus)=='boolean' && do_focus==true) {
      tgt_window.focus();
    }
  }
}

/**
 * Post a message
 * @param   object    inputElement      Message input element
 * @param   string    type              Message type
 * @param   string    offline           Offline flag
 * @param   string    target_user_id    ID of message target user
 * @param   string    target_room_id    ID of message target room
 * @param   string    privacy           Privacy level
 * @param   boolean   no_focus          If TRUE, then message input element will not become a focus. Default: FALSE
 */
function postChatMessage(inputElement, type, offline, target_user_id, target_room_id, privacy, no_focus) {
  // Check flood protection
  if (MessageDelay>0 && unixTimeStamp()-lastPostedMessageTime<MessageDelay) {
    return false;
  }
  lastPostedMessageTime=unixTimeStamp();
  if (typeof(inputElement)=='object' && inputElement && typeof(inputElement.value)=='string') {
    var msg_body=trimString(inputElement.value);
    var tmp=null;
    if (inputElement.addMsgHistorie) {
      inputElement.addMsgHistorie();
    }
    inputElement.value='';
    var msg_src=parseCommands(msg_body);
    var ctl_msg_args=null;
    msg_src_txt=msg_src[0];
    if (typeof(msg_src[1])!='undefined' && msg_src[1]!=null) {
      type=msg_src[1];
    }
    if (typeof(msg_src[2])!='undefined' && msg_src[2]!=null) {
      target_room_id=msg_src[2];
    }
    if (typeof(msg_src[3])!='undefined' && msg_src[3]!=null) {
      target_user_id=msg_src[3];
    }
    if (typeof(msg_src[4])!='undefined' && msg_src[4]!=null) {
      privacy=msg_src[4];
    }
    if (msg_src_txt!='' || MsgAttachments && MsgAttachments.length>0) {
      if (typeof(type)=='undefined' || type==null) type=3001;
      if (typeof(offline)=='undefined' || offline==null) offline='n';
      if (typeof(target_user_id)=='undefined' || target_user_id==null) target_user_id=0;
      if (typeof(target_room_id)=='undefined' || target_room_id==null) target_room_id=currentRoomID;
      if (typeof(privacy)=='undefined' || privacy==null) privacy=0;
      // Get CSS properties
      var css_properties=new Array();
      var css_property='';
      if (type==3001 && inputElement.style) {
        // ... color
        css_property=inputElement.style.color.toLowerCase();
        if (css_property.substring(0, 4)=='rgb(') {
          css_property=css_property.substring(4);
          css_property=css_property.substring(0, css_property.length-1);
          tmp=css_property.split(',');
          if (tmp.length==3) {
            css_property='#'+decHex(trimString(tmp[0]), 2)+decHex(trimString(tmp[1]), 2)+decHex(trimString(tmp[2]), 2);
          } else {
            css_property='';
          }
        }
        if (css_property=='' || css_property.length!=6 && css_property.length!=7) {
          css_property='#'+outgoingMessageColor;
        } else if (css_property.length==6) {
          css_property='#'+css_property;
        }
        css_properties['color']=css_property;
        // ... font-weight
        css_property=inputElement.style.fontWeight;
        if (css_property!='') {
          css_properties['font-weight']=css_property;
        }
        // ... font-style
        css_property=inputElement.style.fontStyle;
        if (css_property!='') {
          css_properties['font-style']=css_property;
        }
        //  ... text-decoration
        css_property=inputElement.style.textDecoration;
        if (css_property!='') {
          css_properties['text-decoration']=css_property;
        }
        //  ... font-family
        css_property=inputElement.style.fontFamily;
        if (css_property!='') {
          css_properties['font-family']=css_property;
        }
        //  ... font-size
        css_property=inputElement.style.fontSize;
        if (css_property!='') {
          css_properties['font-size']=css_property;
        }
      }
      MessageQueue.addRecordOut(type, offline, unixTimeStamp(), target_user_id, target_room_id, privacy, msg_src_txt.substring(0, messageLengthMax), css_properties);
      // Send message to server
      startUpdater(true);
    }
    if (typeof(no_focus)!='boolean' || no_focus==true) {
      inputElement.focus();
    }
  }
}


/**
 * Clear messages area
 */
function flushMessagesArea() {
  $('chatroom_messages_contents').innerHTML='';
  timestampSpans=new Array();
}


/**
 * Show own online status
 * @param   int       online_status           Online status code
 * @param   string    online_status_message   Online status message
 */
function showOwnOnlineStatus(online_status, online_status_message) {
  var status_button=$('online_status_pulldown');
  if (status_button) {
    status_button.style.backgroundImage='url(./pic/online_status_'+online_status+'_16x16.gif)';
    if (typeof(online_status_message)!='string') {
      online_status_message='';
    }
    status_button.alt=getLng('online_status')+': '+online_status_message;
    status_button.title=getLng('online_status')+': '+online_status_message;
  }
}


/**
 * Display online status selection box and apply selected status
 * @param   object    openerObj   Opener object
 */
function openOnlineStatusBox(openerObj) {
  var online_status_code=UserList.getRecord(currentUserId).getOnlineStatus();
  var openerTop=getTopPos(openerObj);
  var openerLeft=getLeftPos(openerObj);
  var online_status_selection_box=$('online_status_selection_box');
  if (online_status_selection_box.style.display=='none') {
    disableSelection();
    document.onclick_original=document.onclick;
    document.onkeypress_original=document.onkeypress;
    online_status_selection_box.style.display='';
    online_status_selection_box.style.top=(openerTop-online_status_selection_box.scrollHeight)+'px';
    online_status_selection_box.style.left=(openerLeft+1)+'px';
    setTimeout('document.onclick=function() { closeOnlineStatusBox() }', 10);
    setTimeout('document.onkeypress=function() { closeOnlineStatusBox() }', 10);
    online_status_selection_box.style.display='none';
    setTimeout("$('online_status_selection_box').style.display='';", 10);
    $('online_status_1_pointer').src='./pic/clearpixel_1x1.gif';
    $('online_status_2_pointer').src='./pic/clearpixel_1x1.gif';
    $('online_status_3_pointer').src='./pic/clearpixel_1x1.gif';
    $('online_status_'+online_status_code+'_pointer').src='./pic/point_10x10.gif';
    setTimeout('fixOnlineStatusBox()', 15);
  }
}

/**
 * Fix online status selection box position
 */
function fixOnlineStatusBox() {
  var online_status_selection_box=$('online_status_selection_box');
  winWidth=getWinWidth();
  winHeight=getWinHeight();
  if (online_status_selection_box) {
    if (online_status_selection_box.scrollWidth+mouseX+5>winWidth) {
      online_status_selection_box.style.left=(winWidth-online_status_selection_box.scrollWidth-5)+'px';
    } else {
      online_status_selection_box.style.left=mouseX+'px';
    }
  }
}

/**
 * Hide online status selection box
 * @param   int       online_status           Online status code
 * @param   string    online_status_message   Online status message
 */
function closeOnlineStatusBox(online_status, online_status_message) {
  var online_status_code=UserList.getRecord(currentUserId).getOnlineStatus();
  enableSelection();
  document.onclick=document.onclick_original;
  document.onkeypress=document.onkeypress_original
  $('online_status_selection_box').style.display='none';
  if (typeof(online_status)!='undefined') {
    if (typeof(online_status_message)=='undefined') {
      online_status_message='';
    }
    if (online_status>0 && online_status_code!=online_status) {
      // Set new online status
      if (typeof(online_status_message)!='string' || online_status_message=='') {
        online_status_message=getLng('online_status_'+online_status);
      } else {
        online_status_message=trimString(online_status_message);
      }
      sendData('_CALLBACK_changeOnlineStatus()', formlink, 'POST', 'ajax=change_online_status&s_id='+urlencode(s_id)+'&online_status='+urlencode(online_status)+'&online_status_message='+urlencode(online_status_message));
    }
  }
}
function _CALLBACK_changeOnlineStatus() {
  toggleProgressBar(false);
  startUpdater(true);
}


/**
 * Display menu box with exit options
 * @param   object    openerObj   Opener object
 */
function openExitBox(openerObj) {
  var openerTop=getTopPos(openerObj);
  var openerLeft=getLeftPos(openerObj);
  var exit_selection_box=$('exit_selection_box');
  if (exit_selection_box.style.display=='none') {
    disableSelection();
    document.onclick_original=document.onclick;
    document.onkeypress_original=document.onkeypress;
    exit_selection_box.style.display='';
    exit_selection_box.style.top=(openerTop-exit_selection_box.scrollHeight)+'px';
    exit_selection_box.style.left=(openerLeft+1)+'px';
    setTimeout('document.onclick=function() { closeExitBox() }', 10);
    setTimeout('document.onkeypress=function() { closeExitBox() }', 10);
    exit_selection_box.style.display='none';
    setTimeout("$('exit_selection_box').style.display='';", 10);
    setTimeout('fixExitBox()', 15);
  }
}

/**
 * Fix exit options menu box position
 */
function fixExitBox() {
  var exit_selection_box=$('exit_selection_box');
  if (exit_selection_box) {
    winWidth=getWinWidth();
    winHeight=getWinHeight();
    if (exit_selection_box.scrollWidth+mouseX+5>winWidth) {
      exit_selection_box.style.left=(winWidth-exit_selection_box.scrollWidth-5)+'px';
    } else {
      exit_selection_box.style.left=mouseX+'px';
    }
  }
}

/**
 * Hide exit options menu box
 * @param   int       result      Optional. If not empty, -1: Leave current chat room, -2: Logout
 */
function closeExitBox(result) {
  enableSelection();
  document.onclick=document.onclick_original;
  document.onkeypress=document.onkeypress_original
  $('exit_selection_box').style.display='none';
  if (typeof(result)!='undefined') {
    if (result==-1) {
      // Leave this room
      confirm(getLng('sure_to_leave_room'), 0, 0, 'leaveRoom()');
      return false;
    } else if (result==-2) {
      // Log out
      confirm(getLng('sure_to_log_out'), 0, 0, 'SkipPageUnloadedMsg=true; logOut();');
      return false;
    }
  }
}


/**
 * Leave current chat room and load user profile page
 */
function leaveRoom() {
  SkipPageUnloadedMsg=true;
  var dummy_form=$('dummyform');
  dummy_form.s_id.value=s_id;
  dummy_form.inc.value='room_selection';
  dummy_form.ts.value=unixTimeStamp();
  dummy_form.submit();
}

/**
 * This function will be called each time current user will get global (un)muted
 * @param   boolean   action    TRUE, if user was muted, FALSE otherwise
 */
function gotGlobalUnMuted(action) {
  if (typeof(action)=='boolean') {
    if (action==true && (typeof($('mainSendMessageButton').muted)!='boolean' || false==$('mainSendMessageButton').muted)) {
      // User was muted
      var muted_until=UserList.getRecord(currentUserId).getGlobalMutedUntil();
      if (muted_until==0 || muted_until>unixTimeStamp()) {
        $('mainSendMessageButton').onclick_=$('mainSendMessageButton').onclick;
        $('mainSendMessageButton').muted=true;
        $('mainSendMessageButton').onclick=function () {
          var muted_until=UserList.getRecord(currentUserId).getGlobalMutedUntil();
          if (muted_until>0) {
            alert(getLng('you_are_muted_until').split('[EXPIRATION_DATE]').join(date(dateFormat, muted_until)));
          } else {
            alert(getLng('you_are_muted_permanently'));
          }
          MainInputTextArea.value='';
          return false;
        }
        MainInputTextArea.value='';
        MainInputTextArea.blur();
        if (muted_until>0) {
          alert(getLng('you_are_muted_until').split('[EXPIRATION_DATE]').join(date(dateFormat, muted_until)));
        } else {
          alert(getLng('you_are_muted_permanently'));
        }
      }
    } else if (action==false && typeof($('mainSendMessageButton').muted)=='boolean' && true==$('mainSendMessageButton').muted) {
      // User was unmuted
      $('mainSendMessageButton').onclick=$('mainSendMessageButton').onclick_;
      $('mainSendMessageButton').muted=false;
    }
  }
}


/**
 * Open PM box
 * @param   int       user_id         Target user ID
 */
function openPMbox(user_id) {
  if (typeof(user_id)=='string') {
    user_id=stringToNumber(user_id);
  }
  if (typeof(user_id)=='number' && user_id>0) {
    if (pmHandlers[user_id]) {
      setTimeout('try { pmHandlers['+user_id+'].focus(); } catch (e) { pmHandlers['+user_id+']=null; openPMbox('+user_id+'); }', 10);
    } else {
      openWindow(formlink+'?s_id='+s_id+'&inc=pm_box&target_user_id='+urlencode(user_id), 'pm_window_'+user_id, 440, 320, false, false, false, false, true);
    }
  }
}

/**
 * This function is called by opened PM box after it initialised
 * @param   object    pm_window     PM box window handler
 */
function pmOpened(pm_window) {
  if (typeof(pm_window)=='object' && pm_window && pm_window.tgt_user_id) {
    pmHandlers[pm_window.tgt_user_id]=pm_window;
    var pm_in=MessageQueue.getAllRecordsIn(true);
    for (var i in pm_in) {
      processMessage(pm_in[i].id,
                     pm_in[i].type,
                     pm_in[i].offline,
                     pm_in[i].date,
                     pm_in[i].author_id,
                     pm_in[i].author_nickname,
                     pm_in[i].target_user_id,
                     pm_in[i].target_room_id,
                     pm_in[i].privacy,
                     pm_in[i].body,
                     pm_in[i].css_properties,
                     pm_in[i].actor_nickname,
                     pm_in[i].attachments
                     );
    }
  }
}

/**
 * This function is called by opened PM box after it was closed
 * @param   object    pm_window     PM box window handler
 */
function pmClosed(pm_window) {
  if (typeof(pm_window)=='object' && pm_window && pm_window.tgt_user_id) {
    pmHandlers[pm_window.tgt_user_id]=null;
  }
}

/**
 * Update room list
 * @param   object    categories    Categories array as returned by AJAX interface
 */
function updateRoomList(categories) {
  var rs=$('chatroom_userlist_room_selection');
  if (typeof(categories)=='object' && categories && categories.length && rs) {
    var cat=null;
    var cat_nr=0;
    var room=null;
    var room_nr=0;
    var room_id=0;
    var s_cat=null;
    var s_room=null;
    rs.innerHTML='';
    for (cat_nr=0; cat_nr<categories.length; cat_nr++) {
      cat=categories[cat_nr];
      if (cat['room'].length) {
        s_cat=document.createElement('OPTGROUP');
        s_cat.label=cat['name'][0];
        for (room_nr=0; room_nr<cat['room'].length; room_nr++) {
          room=cat['room'][room_nr];
          room_id=stringToNumber(room['id'][0]);
          s_room=document.createElement('OPTION');
          s_room.value=room_id;
          s_room.password_protect='1'==room['password_protected'][0];
          s_room.innerHTML=(s_room.password_protect? '* ' : '')
                          +(room_id==currentRoomID? '&gt; ' : '')
                          +htmlspecialchars(room['name'][0])
                          +'&nbsp;['+room['users_count'][0]+']';
          s_cat.appendChild(s_room);
        }
        if (room_nr>1 || room!=null) {
          rs.appendChild(s_cat);
        }
      }
    }
    rs.style.width=(ChatroomUserlist.scrollWidth-35)+'px';
    rs.value=currentRoomID;
  }
}


/**
 * Switch to selected chat room
 * @param   int       id          Room ID
 * @param   boolean   ask_pass    Optional. If TRUE, then user will be prompted to enter room password. Default: FALSE
 * @param   string    password    Optional. Room password.
 */
function switchChatRoom(id, ask_pass, password) {
  if (typeof(id)=='string') {
    id=stringToNumber(id);
  }
  if (typeof(ask_pass)=='boolean' && ask_pass && !isAdmin) {
    showPasswordFieldBox(mouseX+15, mouseY+15, 'switchChatRoom('+id+', false, \'/RESULT/\')', '$(\'chatroom_userlist_room_selection\').value=numberToString(currentRoomID)', getLng('room_password'));
    return false;
  }
  if (typeof(password)!='string') {
    password='';
  }
  if (typeof(id)=='number' && id>0 && id!=currentRoomID) {
    sendData('_CALLBACK_switchChatRoom()', formlink, 'POST', 'ajax=enter_chat_room&s_id='+urlencode(s_id)+'&room_id='+urlencode(id)+'&stealth_mode='+urlencode(stealthActivated? 'y' : 'n')+'&password='+urlencode(base64encode(password)));
  }
}
function _CALLBACK_switchChatRoom() {
  var dummy_form=$('dummyform');
  switch (actionHandler.status) {

    case  -1:
      // Session is invalid
      SkipPageUnloadedMsg=true;
      document.location.href=formlink+'?session_timeout';
      return false;
    break;

    case 0:
      // Room changed. Load room page.
      SkipPageUnloadedMsg=true;
      dummy_form.s_id.value=s_id;
      dummy_form.inc.value='chat_room';
      dummy_form.ts.value=unixTimeStamp();
      dummy_form.submit();
      return false;
    break;

    case 400:
      // Error: Room does not exists
      alert(actionHandler.message);
      startUpdater(true, true);
    break;

    case 600:
      // Error: Wrong password
      alert(actionHandler.message);
      $('chatroom_userlist_room_selection').value=numberToString(currentRoomID);
    break;

    default:
      // Other error
      startUpdater(true, true);
    break;

  }
  // Reset window status resolution
  toggleProgressBar(false);
}


/**
 * Display help box
 * @param   object    openerObj   Opener object
 */
function showHelpBox(openerObj) {
  var openerTop=getTopPos(openerObj);
  var openerLeft=getLeftPos(openerObj);
  var help_box=$('help_box');
  if (help_box.style.display=='none') {
    disableSelection();
    document.onclick_original=document.onclick;
    document.onkeypress_original=document.onkeypress;
    help_box.style.display='';
    help_box.style.top=(openerTop-help_box.scrollHeight)+'px';
    help_box.style.left=(openerLeft+1)+'px';
    setTimeout('document.onclick=function() { closeHelpBox() }', 10);
    setTimeout('document.onkeypress=function() { closeHelpBox() }', 10);
    help_box.style.display='none';
    setTimeout("$('help_box').style.display=''", 10);
    setTimeout('fixHelpBox()', 15);
  }
}


/**
 * Fix online status selection box position
 */
function fixHelpBox() {
  var help_box=$('help_box');
  winWidth=getWinWidth();
  winHeight=getWinHeight();
  if (help_box) {
    if (help_box.scrollWidth+mouseX+5>winWidth) {
      help_box.style.left=(winWidth-help_box.scrollWidth-5)+'px';
    } else {
      help_box.style.left=mouseX+'px';
    }
  }
}


/**
 * Hide help box
 * @param   int       selected_help     Selected help code
 */
function closeHelpBox(selected_help) {
  enableSelection();
  document.onclick=document.onclick_original;
  document.onkeypress=document.onkeypress_original
  $('help_box').style.display='none'
  if (typeof(selected_help)!='undefined') {
    if (selected_help==0) {
      // About
      alert( "PCPIN Chat\n"
            +"Copyright "+base64decode('qQ==')+" 2007 - 2013 Kanstantin Reznichak\n"
            +"http://www.pcpin.com");
    } else if (selected_help==1) {
      // Chat commands
      _cmd_help();
    } else if (selected_help==2) {
      // Call moderator
      openModeratorCallBox();
    }
  }
}


/**
 * Open "Call moderator" box
 */
function openModeratorCallBox() {
  moderatorCallWindow=openWindow(formlink+'?s_id='+s_id+'&inc=call_moderator', 'moderatorCallWindow', 500, 400, false, false, false, false, true);
}


/**
 * Activate/deactivate message timestamps
 */
function invertTimeStampView() {
  displayTimeStamp=!displayTimeStamp;
  var new_display=displayTimeStamp? '' : 'none';
  for (var i in timestampSpans) {
    $(timestampSpans[i]).style.display=new_display;
  }
  MainInputTextArea.focus();
  $('invert_timestamp_btn').style.backgroundImage='url(./pic/'+(displayTimeStamp? 'timestamp_active_15x15.gif' : 'timestamp_inactive_15x15.gif')+')';
  $('invert_timestamp_btn').title=displayTimeStamp? getLng('hide_message_time') : getLng('show_message_time');
}


/** 
 * Activate/deactivate sound effects 
 */ 
function toggleSounds() {
  var btn=$('invert_sounds_btn');
  allowSounds=!allowSounds;
  if (typeof(PCPIN_MP3_Player)!='undefined' && PCPIN_MP3_Player) {
    if (!allowSounds) {
      PCPIN_MP3_Player.setVolume(0);
    } else {
      PCPIN_MP3_Player.setVolume(PCPIN_MP3_PlayerDefaultVolume);
    }
  }
  MainInputTextArea.focus();
  if (btn) {
    btn.style.backgroundImage='url(./pic/'+(allowSounds? 'sounds_active_15x15.gif' : 'sounds_inactive_15x15.gif')+')';
  }
}


/**
 * Display "Attach file" popup
 */
function addMsgAttachment() {
  try {
    if (uploadWindow && !uploadWindow.closed) {
      uploadWindow.close();
    }
  } catch (e) {}
  setTimeout("openWindow(formlink+'?s_id='+s_id+'&inc=upload&f_target=msg_attachment', 'file_upload', 400, 80, false, false, false, false, true);", 150);
}

/**
 * Parse response from upload window
 * @param   int       code            Response code
 * @param   string    message         Response message
 * @param   int       binaryfile_id   Binaryfile ID
 * @param   int       width           If file was an image: width
 * @param   int       height          If file was an image: height
 * @param   string    filename        Source file name
 */
function parseUploadResponse(code, message, binaryfile_id, width, height, filename) {
  if (typeof(code)!='undefined' && typeof(message)!='undefined') {
    switch (code) {

      case 0:
        // Success
        MsgAttachments.push(Array(filename, binaryfile_id));
        displayAttachments();
      break;

      case -1:
        // No file uploaded
        // do nothing ;)
      break;

      default:
        alert(message);
      break;

    }
  }
}

/**
 * Display currently active message attachments
 */
function displayAttachments() {
  var html='';
  for (var i in MsgAttachments) {
    html+='<a href=":" title="'+htmlspecialchars(getLng('delete'))+'" onclick="deleteAttachment('+htmlspecialchars(MsgAttachments[i][1])+'); return false;">'
         +'<img src="./pic/delete_9x9.gif" title="'+htmlspecialchars(getLng('delete'))+'" alt="'+htmlspecialchars(getLng('delete'))+'">'
         +'</a>'
         +'&nbsp;'
         +'<b>'+htmlspecialchars(MsgAttachments[i][0])+'</b>'
         +'&nbsp;&nbsp;&nbsp;';
  }
  html+=MsgAttachments.length>0? '<br />' : '';
  if (html!='') {
    $('attached_files').innerHTML=html;
    $('attached_files').style.display='';
  } else {
    $('attached_files').style.display='none';
  }
  if ($('msg_attachment_btn')) {
    if (MsgAttachments.length>=MsgAttachmentsLimit) {
      $('msg_attachment_btn').style.backgroundImage='url(./pic/attachment_disabled_15x15.gif)';
      $('msg_attachment_btn').disabled=true;
    } else {
      $('msg_attachment_btn').style.backgroundImage='url(./pic/attachment_15x15.gif)';
      $('msg_attachment_btn').disabled=false;
    }
  }
  window.onresize();
}

/**
 * Delete attachment
 * @param   int     binaryfile_id
 */
function deleteAttachment(binaryfile_id) {
  binaryfile_id=stringToNumber(binaryfile_id);
  var MsgAttachments_new=new Array();
  for (var i in MsgAttachments) {
    if (MsgAttachments[i][1]!=binaryfile_id) {
      MsgAttachments_new.push(MsgAttachments[i]);
    }
  }
  MsgAttachments=MsgAttachments_new;
  displayAttachments();
  ajaxMiscHandler.sendData('displayAttachments()', 'POST', formlink, 'ajax=delete_msg_attachment&s_id='+urlencode(s_id)+'&binaryfile_id='+urlencode(binaryfile_id));
}

/**
 * Turn auto-scroll ON/OFF
 * @param   boolean   new_state   Optional. New auto-scroll state. If not specified, then current status will be inverted.
 * @param   object    tgt_window  Optional. Target window. Default is the main window.
 */
function setAutoScroll(new_state, tgt_window) {
  if (typeof(tgt_window)!='object') {
    tgt_window=window;
  }
  if (typeof(new_state)=='boolean') {
    tgt_window.AutoScroll=new_state;
  } else {
    tgt_window.AutoScroll=!tgt_window.AutoScroll;
  }
  if (tgt_window.AutoScroll) {
    $('scroll_ctl_btn', tgt_window.document).style.backgroundImage='url(pic/scroll_active_5x18.gif)';
    $('scroll_ctl_btn', tgt_window.document).title=getLng('auto_scroll')+': '+getLng('on');
  } else {
    $('scroll_ctl_btn', tgt_window.document).style.backgroundImage='url(pic/scroll_inactive_5x18.gif)';
    $('scroll_ctl_btn', tgt_window.document).title=getLng('auto_scroll')+': '+getLng('off');
  }
}


/**
 * Enable top/bottom banner
 * @param   string    display_type    Display type ("t": top banner, "b": bottom banner)
 */
function enableBanner(display_type) {
  var banner_area=null;
  if (display_type=='t' && !TopBannerEnabled) {
    // Top banner
    banner_area=$('chatroom_top_banner');
    TopBannerEnabled=true;
    TopBannerInterval=setInterval("loadBanner($('"+banner_area.id+"'), 't');", BannerRefreshRate*1000);
  } else if (display_type=='b' && !BottomBannerEnabled) {
    // Bottom banner
    banner_area=$('chatroom_bottom_banner');
    BottomBannerEnabled=true;
    BottomBannerInterval=setInterval("loadBanner($('"+banner_area.id+"'), 'b');", BannerRefreshRate*1000);
  } else if (display_type=='m') {
    MsgBannerMessagesLeft=MsgBannerPeriod;
  } else if (display_type=='p') {
    PopupBannerInterval=setInterval('loadPopupBanner()', PopupBannerPeriod*1000);
  }
  if (banner_area) {
    loadBanner(banner_area, display_type);
    window.onresize(true);
  }
}


/**
 * Disable top/bottom banner
 * @param   string    display_type    Display type ("t": top banner, "b": bottom banner)
 */
function disableBanner(display_type) {
  var banner_area=null;
  if (display_type=='t') {
    // Top banner
    if (TopBannerEnabled) {
      banner_area=$('chatroom_top_banner');
      TopBannerEnabled=false;
      clearInterval(TopBannerInterval);
    }
  } else if (display_type=='b') {
    // Bottom banner
    if (BottomBannerEnabled) {
      banner_area=$('chatroom_bottom_banner');
      BottomBannerEnabled=false;
      clearInterval(BottomBannerInterval);
    }
  } else if (display_type=='m') {
    MsgBannerMessagesLeft=0;
  } else if (display_type=='p') {
    hidePopupBanner();
    clearInterval(PopupBannerInterval);
  }
  if (banner_area) {
    loadBanner(banner_area, display_type, 'dummy.html');
    window.onresize();
  }
}


/**
 * Load banner into the top / bottom area
 * @param   object    area            Banner area handler
 * @param   string    display_type    Display type ("t": top banner, "b": bottom banner)
 * @param   string    force_url       Optional URL to load into the banner area
 */
function loadBanner(area, display_type, force_url) {
  if (typeof(area)=='object') {
    if (typeof(force_url)=='string' && force_url!='') {
      area.src=force_url;
    } else {
      area.src=formlink+'?load_banner='+urlencode(display_type)+'&ts='+unixTimeStamp();
    }
  }
}


/**
 * Display a banner in messages area
 */
function loadMsgBanner() {
  ajaxBannersHandler.sendData('_CALLBACK_loadMsgBanner()', 'POST', formlink, 'ajax=load_banner&s_id='+urlencode(s_id)+'&display_position=m', false);
}
function _CALLBACK_loadMsgBanner() {
//debug(ajaxBannersHandler.getResponseString()); return false;
  var banner_data=null;
  var banner_span='';
  var cmc=$('chatroom_messages_contents');
  if (ajaxBannersHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  }
  if (typeof(ajaxBannersHandler.data['banner_data'])!='undefined') {
    banner_data=ajaxBannersHandler.data['banner_data'][0];
    banner_span=document.createElement('SPAN');
    banner_span.style.padding='0px';
    banner_span.style.margin='5px';
    banner_span.innerHTML='<iframe'
                         +' src="'+formlink+'?load_banner=m&banner_id='+htmlspecialchars(banner_data['id'][0])+'"'
                         +' scrolling="No"'
                         +' frameborder="0"'
                         +' width="'+htmlspecialchars(banner_data['width'][0])+'"'
                         +' height="'+htmlspecialchars(banner_data['height'][0])+'"'
                         +' style="padding:0px;margin:0px;"'
                         +'></iframe><br />';
  }
  cmc.appendChild(banner_span);
  if (AutoScroll) {
    try {
      ChatroomMessages.scrollTop=ChatroomMessages.scrollHeight;
    } catch (e) {
      banner_span.scrollIntoView(false);
    }
  }
}


/**
 * Display a popup banner
 */
function loadPopupBanner() {
  ajaxBannersHandler.sendData('_CALLBACK_loadPopupBanner()', 'POST', formlink, 'ajax=load_banner&s_id='+urlencode(s_id)+'&display_position=p', false);
}
function _CALLBACK_loadPopupBanner() {
//debug(ajaxBannersHandler.getResponseString()); return false;
  var banner_data=null;
  var width=0;
  var height=0;
  var banner_popup=$('banner_popup');
  var banner_popup_frame=$('banner_popup_frame');

  if (ajaxBannersHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  }
  if (typeof(ajaxBannersHandler.data['banner_data'])!='undefined') {
    banner_data=ajaxBannersHandler.data['banner_data'][0];
    width=stringToNumber(banner_data['width'][0]);
    height=stringToNumber(banner_data['height'][0]);

    banner_popup.style.width=(width+14)+'px';
    banner_popup.style.height=(height+26)+'px';

    banner_popup_frame.style.width=width+'px';
    banner_popup_frame.style.height=height+'px';

    banner_popup_frame.src=formlink+'?load_banner=m&banner_id='+ajaxBannersHandler.data['banner_data'][0]['id'][0]+(isOpera? '&killCache='+unixTimeStamp() : '');
    banner_popup.style.display='';
    moveToCenter(banner_popup);
  }
}


/**
 * Hide popup banner
 */
function hidePopupBanner() {
  $('banner_popup').style.display='none';
}


/**
 * Load and play a sound
 * @param   string    file    MP3 file name within ./sounds directory
 * @param   boolean   lock    Optional. If TRUE, then player will ignore other sounds until supplied sound is playing. Default FALSE.
 */
function playSound(file, lock) {
  if (allowSounds && typeof(file)=='string' && file!='' && typeof(PCPIN_MP3_Player)!='undefined' && PCPIN_MP3_Player) {
    if (typeof(lock)!='boolean') {
      lock=false;
    }
    PCPIN_MP3_Player.loadUrl('./sounds/'+file);
    if (lock) {
      PCPIN_MP3_Player.playTrackLocked();
    } else {
      PCPIN_MP3_Player.playTrack();
    }
  }
}
