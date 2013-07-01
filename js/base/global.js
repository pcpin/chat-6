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

// User agent and OS
var usrAgnt=navigator.userAgent.toLowerCase();
var isWin=(usrAgnt.indexOf('win')!=-1);
var usrAgntVer=parseInt(navigator.appVersion);
var isIE=(usrAgnt.indexOf('msie')!=-1);
var isOpera=(usrAgnt.indexOf('opera')!=-1);
var isMozilla=(navigator.product=='Gecko');

/**
 * Current mouse X-coordinate
 * @var int
 */
var mouseX=0;

/**
 * Current mouse X-coordinate
 * @var int
 */
var mouseY=0;

// Current session ID
var s_id='';

// Current IP address as seen by the server
var currentIP='';

// Current user ID
var currentUserId=0;

// Current Formlink
var formlink='';

// Admin formlink
var adminFormlink='';

// Main area formlink
var mainFormlink='';

// Exit URL
var exit_url='';

// Flag: TRUE if user is admin, FALSE otherwise
var isAdmin=false;

// Date format
var dateFormat='Y-m-d H:i:s';

// Language expressions
var lngExpressions=new Array();

// Default XMLHttpRequest handler for *NOT* simultaneously executed requests
var actionHandler=new PCPIN_XmlHttpRequest();

// Flag: if true, then Progress bar is currently On
var progressBarState=false;

/**
 * "File upload" window handler
 * @var object
 */
var uploadWindow=null;

/**
 * "Create new user room" window handler
 * @var object
 */
var newUserRoomWindow=null;

/**
 * Chache used by setCssClass() function
 * @var object
 */
var setCss_cache=new Array();

/**
 * LogOut window handler
 * @var object
 */
var logOutWindow=null;

/**
 * Flag: TRUE if server supports GD2
 * @var boolean
 */
var ImgResizeSupported=false;

/**
 * Function handler that will be called each time mouse moves
 * @var object
 */
var MouseMoveFunc=null;

/**
 * Object handler the MouseMoveFunc() will be applied on
 * @var object
 */
var MouseMoveFuncObj=null;

/**
 * Flag: if TRUE, then "Slave mode" is active
 * @var boolean
 */
var SlaveMode=false;

/**
 * Received abuses
 * @var object
 */
var receivedAbuses=new Array();

/**
 * Flag: TRUE if client does not need to send "page unloaded" event to server
 * @var boolean
 */
var SkipPageUnloadedMsg=isMozilla;

/**
 * Current room ID
 * @var int
 */
var currentRoomID=0;





/**
 * JS to CSS and vice versa map
 * NOTE: This array is *NOT* complete!!!
 * @var object
 */
var CSS_to_JS_map=new Array();
CSS_to_JS_map['background']='background';
CSS_to_JS_map['background-attachment']='backgroundAttachment';
CSS_to_JS_map['background-color']='backgroundColor';
CSS_to_JS_map['background-image']='backgroundImage';
CSS_to_JS_map['background-position']='backgroundPosition';
CSS_to_JS_map['background-repeat']='backgroundRepeat';
CSS_to_JS_map['border']='border';
CSS_to_JS_map['border-bottom']='borderBottom';
CSS_to_JS_map['border-bottom-color']='borderBottomColor';
CSS_to_JS_map['border-bottom-style']='borderBottomStyle';
CSS_to_JS_map['border-bottom-width']='borderBottomWidth';
CSS_to_JS_map['border-color']='borderColor';
CSS_to_JS_map['border-left']='borderLeft';
CSS_to_JS_map['border-left-color']='borderLeftColor';
CSS_to_JS_map['border-left-style']='borderLeftStyle';
CSS_to_JS_map['border-left-width']='borderLeftWidth';
CSS_to_JS_map['border-right']='borderRight';
CSS_to_JS_map['border-right-color']='borderRightColor';
CSS_to_JS_map['border-right-style']='borderRightStyle';
CSS_to_JS_map['border-right-width']='borderRightWidth';
CSS_to_JS_map['border-style']='borderStyle';
CSS_to_JS_map['border-top']='borderTop';
CSS_to_JS_map['border-top-color']='borderTopColor';
CSS_to_JS_map['border-top-style']='borderTopStyle';
CSS_to_JS_map['border-top-width']='borderTopWidth';
CSS_to_JS_map['border-width']='borderWidth';
CSS_to_JS_map['clear']='clear';
CSS_to_JS_map['clip']='clip';
CSS_to_JS_map['color']='color';
CSS_to_JS_map['cursor']='cursor';
CSS_to_JS_map['display']='display';
CSS_to_JS_map['filter']='filter';
CSS_to_JS_map['font']='font';
CSS_to_JS_map['font-family']='fontFamily';
CSS_to_JS_map['font-size']='fontSize';
CSS_to_JS_map['font-style']='fontStyle';
CSS_to_JS_map['font-variant']='fontVariant';
CSS_to_JS_map['font-weight']='fontWeight';
CSS_to_JS_map['height']='height';
CSS_to_JS_map['left']='left';
CSS_to_JS_map['letter-spacing']='letterSpacing';
CSS_to_JS_map['line-height']='lineHeight';
CSS_to_JS_map['list-style']='listStyle';
CSS_to_JS_map['list-style-image']='listStyleImage';
CSS_to_JS_map['list-style-position']='listStylePosition';
CSS_to_JS_map['list-style-type']='listStyleType';
CSS_to_JS_map['margin']='margin';
CSS_to_JS_map['margin-bottom']='marginBottom';
CSS_to_JS_map['margin-left']='marginLeft';
CSS_to_JS_map['margin-right']='marginRight';
CSS_to_JS_map['margin-top']='marginTop';
CSS_to_JS_map['overflow']='overflow';
CSS_to_JS_map['padding']='padding';
CSS_to_JS_map['padding-bottom']='paddingBottom';
CSS_to_JS_map['padding-left']='paddingLeft';
CSS_to_JS_map['padding-right']='paddingRight';
CSS_to_JS_map['padding-top']='paddingTop';
CSS_to_JS_map['page-break-after']='pageBreakAfter';
CSS_to_JS_map['page-break-before']='pageBreakBefore';
CSS_to_JS_map['position']='position';
CSS_to_JS_map['float']='styleFloat';
CSS_to_JS_map['text-align']='textAlign';
CSS_to_JS_map['text-decoration']='textDecoration';
CSS_to_JS_map['text-decoration: blink']='textDecorationBlink';
CSS_to_JS_map['text-decoration: line-through']='textDecorationLineThrough';
CSS_to_JS_map['text-decoration: none']='textDecorationNone';
CSS_to_JS_map['text-decoration: overline']='textDecorationOverline';
CSS_to_JS_map['text-decoration: underline']='textDecorationUnderline';
CSS_to_JS_map['text-indent']='textIndent';
CSS_to_JS_map['text-transform']='textTransform';
CSS_to_JS_map['top']='top';
CSS_to_JS_map['vertical-align']='verticalAlign';
CSS_to_JS_map['visibility']='visibility';
CSS_to_JS_map['width']='width';
CSS_to_JS_map['z-index']='zIndex';


/**
 * Disable text selection
 */
function disableSelection() {
  if (window.sidebar) {
    document.onmousedown=function() { return false; }
  } else {
    document.onselectstart=function() { return false; }
  }
}


/**
 * Enable text selection
 */
function enableSelection() {
  if (window.sidebar) {
    document.onmousedown=function() { return true; }
  } else {
    document.onselectstart=function() { return true; }
  }
}


/**
 * Start mouse position capture
 */
function startMousePosCapture() {
  if (window.event) {
    // IE & Co.
    document.onmousemove=function() {
      mouseX=window.event.x+document.documentElement.scrollLeft;
      mouseY=window.event.y+document.documentElement.scrollTop;
      if (MouseMoveFunc) {
        MouseMoveFunc();
      }
      return true;
    }
  } else {
    // Mozilla
    document.onmousemove=function(e) {
      mouseX=e.pageX;
      mouseY=e.pageY;
      if (MouseMoveFunc) {
        MouseMoveFunc();
      }
      return true;
    }
  }
}

/**
 * Stop mouse position capture
 */
function stopMousePosCapture() {
  document.onmousemove=function(e) { return true; }
}



/**
 * [document.]getElementById() wrapper
 * @param     string    elementId   ID of the element
 * @param     object    targetDoc   OPTIONAL target document handler. Default: document
 * @return    object    Specified object or NULL if noting found
 */
function $(elementId, targetDoc) {
  if (typeof(targetDoc)!='object' || targetDoc==null || !targetDoc.getElementById) {
    return document.getElementById(elementId);
  } else {
    return targetDoc.getElementById(elementId);
  }
}

/**
 * [document.]getElementsByTagName() wrapper
 * @param   string    tag       Tag name
 * @param   object    parent_   Parent element. Default: document
 */
function $$(tag, parent_) {
  var elements=null;
  if (typeof(tag)=='string' && tag!='') {
    if (typeof(parent_)!='object' || parent_==null) {
      parent_=document;
    }
    try {
      elements=parent_.getElementsByTagName(tag);
    } catch (e) {
      elements=null;
    }
  }
  return elements;
}


/**
 * Set new session ID
 * @param   string    new_s_id    New session ID
 */
function setSid(new_s_id) {
  if (typeof(new_s_id)=='string') {
    s_id=new_s_id;
  }
}


/**
 * Set "Slave mode" flag
 * @param   boolean     slave_mode    Slave mode flag
 */
function setSlaveMode(slave_mode) {
  if (typeof(slave_mode)=='boolean') {
    SlaveMode=slave_mode;
  }
}


/**
 * Set current IP address
 * @param   string    ip    IP address
 */
function setIP(ip) {
  if (typeof(ip)=='string' && ip!='') {
    currentIP=ip;
  }
}


/**
 * Set new formlink
 * @param   string    new_formlink    New formlink
 */
function setFormLink(new_formlink) {
  if (typeof(new_formlink)=='string') {
    formlink=new_formlink;
  }
}


/**
 * Set new admin formlink
 * @param   string    new_formlink    New admin formlink
 */
function setAdminFormLink(new_formlink) {
  if (typeof(new_formlink)=='string') {
    adminFormlink=new_formlink;
  }
}


/**
 * Set new main area formlink
 * @param   string    new_formlink    New main area formlink
 */
function setMainFormLink(new_formlink) {
  if (typeof(new_formlink)=='string') {
    mainFormlink=new_formlink;
  }
}


/**
 * Set admin flag
 * @param   boolean   img_resize_supported    TRUE, if server supports image resizements
 */
function setImgResizeFlag(img_resize_supported) {
  ImgResizeSupported=(typeof(img_resize_supported)=='boolean' && img_resize_supported);
}


/**
 * Set admin flag
 * @param   boolean   is_admin    TRUE, if user is Admin, FALSE otherwise
 */
function setAdminFlag(is_admin) {
  isAdmin=typeof(is_admin)=='boolean' && is_admin;
}


/**
 * Set new exit URL
 * @param   string    new_exit_url    New exit URL
 */
function setExitURL(new_exit_url) {
  if (typeof(new_exit_url)=='string') {
    exit_url=new_exit_url;
  }
}


/**
 * Set current user ID
 * @param   int   user_id   User ID
 */
function setUserId(user_id) {
  if (typeof(user_id)=='number') {
    currentUserId=user_id;
  }
}


/**
 * Add new language expression
 * @param   string    lng_id    Expression ID
 * @param   string    lng_val   Expression value
 */
function setLng(lng_id, lng_val) {
  if (typeof(lng_id)=='string' && lng_id!='' && typeof('lng_val')=='string') {
    lngExpressions[lng_id]=lng_val;
  }
}


/**
 * Set new date format
 * @param   string    date_format   Date format as used by PHP date() function
 */
function setDateFormat(date_format) {
  if (typeof(date_format)=='string' && date_format!='') {
    dateFormat=date_format;
  }
}


/**
 * Set current room ID
 * @param   int   id    ID of room the user currently in
 */
function setCurrentRoomID(id) {
  if (typeof(id)=='number' && id>0) {
    currentRoomID=id;
  }
}


/**
 * Get language expression
 * @param   string    lng_id    Language expression ID
 * @return  string
 */
function getLng(lng_id) {
  if(typeof(lng_id)=='string' && typeof(lngExpressions[lng_id])=='string') {
    return lngExpressions[lng_id];
  } else {
    return lng_id;
  }
}


/**
 * Display progress bar and send HTTP request using default XMLHttpRequest handler
 * @param   string    callbackFunc      CallBack function
 * @param   string    url               URL (with GET parameters, if any)
 * @param   string    method            HTTP-Method: "GET" or "POST". Default: "POST"
 * @param   string    data              POST data: "var_name=value&var_name=value..."
 *                                      IMPORTANT: value must be urlencoded; use existing function urlencode()
 * @param   boolean   skipProgressBar   Optional. If TRUE: "Progress bar" will be not displayed
 * @param   boolean   doSync            Optional. If TRUE (default): Request will be executed in synchronous mode
 * @param   int       sendWait          Optional. Time to wait (in milliseconds) before send the data to server.
 *                                      Default value 50 allows browser to display progress bar before it "freezes"
 *                                      due to synchronous connection type.
 */
function sendData(callbackFunc, url, method, data, skipProgressBar, doSync, sendWait) {
  if (typeof(method)!='string') {
    method='POST';
  } else {
    method=method.toUpperCase();
  }
  if (typeof(data)!='string' || method=='GET') {
    data='';
  }
  if (typeof(doSync)!='boolean') {
    doSync=true;
  }
  if (typeof(sendWait)!='number') {
    sendWait=50;
  }
  if(typeof(url)=='string' && url!=''){
    if (typeof(skipProgressBar)!='boolean' || !skipProgressBar) {
      toggleProgressBar(true);
    }
    if (true||sendWait>0) {
      setTimeout('actionHandler.sendData("'+callbackFunc+'", "'+method+'", "'+url+'", "'+data+'", '+(doSync? 'true' : 'false')+')', sendWait);
    } else {
      actionHandler.sendData(callbackFunc, method, url, data, doSync);
    }
  }
}


/**
 * Toggle Progress bar on/off
 * @param   boolean   newState    Desired state (false: Hide, true: Show)
 */
function toggleProgressBar(newState){
  var pb=$('progressBar');
  if(pb!=null){
    if(!progressBarState && newState){
      // Show Progress bar
      pb.style.display='';
      moveToCenter(pb);
      progressBarState=true;
    } else if(progressBarState && !newState){
      // Hide Progress bar
      pb.style.display='none';
      progressBarState=false;
    }
  }
}


/**
 * Check opener window
 * If opener window is a PCPIN Chat main window and the session is timed out,
 * then the self.window will be closed and the opener window will be redirected to login page
 * @param   boolean   force   If TRUE, then session ID check will be NOT performed
 */
function checkOpener(force) {
  // Check parent window
  if (typeof(force)!='boolean') {
    force=false;
  }
  try {
    if (window.opener && window.opener.appName_=='pcpin_chat' && (force || window.opener.s_id && window.opener.s_id!=s_id)) {
      // Session timed out. Reload parent window
      window.opener.document.location.href=formlink;
      // ... and close current window
      window.close();
    }
  } catch (e) {}
}

/**
 * Assign specified CSS class to an element
 * @param   object    targetElement     Target element
 * @param   string    cssName           CSS class name
 * @param   boolean   skipCache         If TRUE, then no chached CSS data will be used. Default is FALSE.
 */
function setCssClass(targetElement, cssName, skipCache) {
  if (typeof(targetElement)=='object' && targetElement && targetElement.style && typeof(cssName)=='string' && cssName!='') {
    var css_text='';
    var css_rules_array=null;
    var css_rules=null;
    var tmp=null;
    var selector_text_array=null;
    var selector_text='';
    var css_found=false;
    var cssName_curr='';
    var cssName_2='.'+cssName;
    var cssName_3='#'+cssName;
    var property_name='';
    var property_value='';
    var original_value=null;
    cssName=cssName.toLowerCase();
    if ((typeof(skipCache)!='boolean' || skipCache==false) && typeof(setCss_cache[cssName])=='object' && setCss_cache[cssName]) {
      css_found=true;
      css_rules_array=setCss_cache[cssName];
    } else {
      for (var stylesheet_nr=0; stylesheet_nr<document.styleSheets.length; stylesheet_nr++) {
        if (document.styleSheets[stylesheet_nr].cssText) {
          // IE
          css_text=document.styleSheets[stylesheet_nr].cssText;
        } else {
          // Others
          css_rules=document.styleSheets[stylesheet_nr].cssRules;
          for (var rule_nr=0; rule_nr<css_rules.length; rule_nr++) {
            css_text+=css_rules[rule_nr].cssText+"\n";
          }
        }
        tmp=css_text.split('}');
        for (var i=0; i<tmp.length; i++) {
          selector_text=trimString(tmp[i].substring(0, tmp[i].indexOf('{')));
          if (selector_text!='') {
            selector_text_array=selector_text.split(',');
          }
          for (var ii=0; ii<selector_text_array.length; ii++) {
            cssName_curr=trimString(selector_text_array[ii]).toLowerCase();
            if (cssName_curr==cssName || cssName_curr==cssName_2 || cssName_curr==cssName_3) {
              setCss_cache[cssName]=(trimString(tmp[i].substring(tmp[i].indexOf('{')+1))).split(';');
              for (var nn=0; nn<setCss_cache[cssName].length; nn++) {
                if (-1!=setCss_cache[cssName][nn].indexOf('"')) {
                  setCss_cache[cssName][nn]=setCss_cache[cssName][nn].split('"').join('\\"');
                }
              }
              css_rules_array=setCss_cache[cssName];
              if (isIE) {
                for (var iii = 0; iii < setCss_cache[cssName].length; iii ++) {
                  setCss_cache[cssName][iii] = setCss_cache[cssName][iii].split('\\"').join('"');
                }
              }
              css_found=true;
              break;
            }
          }
          if (css_found==true) {
            break;
          }
        }
        if (css_found) {
          break;
        }
      }
    }
    if (css_found && css_rules_array!=null) {
      for (var iii=0; iii<css_rules_array.length; iii++) {
        if (css_rules_array[iii]!='') {
          tmp=css_rules_array[iii].split(':');
          property_name=cssToJs(trimString(tmp.shift()).toLowerCase());
          property_value=trimString(tmp.join(':'));
          if (property_name!='') {
            if (isIE && null!=(original_value=targetElement.style.getAttribute(property_name))) {
              if (original_value!=property_value) {
                targetElement.style.setAttribute(property_name, property_value);
              }
            } else {
              eval('targetElement.style.'+property_name+'="'+property_value+'";');
            }
          }
        }
      }
    }
  }
}


/**
 * Return event's keycode (ASCII)
 * @param   object  e         Event
 * @param   object  e_scope   Event register scope (default: window)
 * @return  int
 */
function getKC(e, e_scope) {
  var kc=0;
  if (!e) {
    if (!e_scope) {
      // Default event register scope
      e_scope=window;
    }
    if (e_scope && e_scope.event) {
      // The browser did not pass the event information to the function,
      // so we will have to obtain it from the event register
      e=e_scope.event;
    }
  }
  if (e) {
    if (typeof(e.keyCode)=='number') {
      // DOM-compatible
      kc=e.keyCode;
    } else if (typeof(e.which)=='number') {
      // NS4
      kc=e.which;
    } else if( typeof(e.charCode)=='number') {
      // Other NS and Mozilla versions
      kc=e.charCode;
    }
  }
  return kc;
}

/**
 * Shows some text in a new PopUp window
 * @param   string    text    text to display
 */
function debug(text) {
  var dw=window.open('dummy.html');
  dw.document.open();
  dw.document.write(text);
  dw.document.close();
}

/**
 * Convert string with color codes into HTML colored string
 * @param   string    colored     String with color codes
 * @param   string    tag         HTML tag to use (default: SPAN)
 * @return  string
 */
function coloredToHTML(colored, tag) {
  var html='';
  var color='';
  var parts=colored.split('^');
  if (typeof(tag)!='string' || tag=='') {
    tag='span';
  }
  if (parts.length==1) {
    html=htmlspecialchars(parts[0]).split(' ').join('&nbsp;');
  } else {
    for (var i=0; i<parts.length; i++) {
      if (parts[i].length>6) {
        html+='<'+tag+' style="color:#'+parts[i].substring(0, 6)+'">'
            + htmlspecialchars(parts[i].substring(6)).split(' ').join('&nbsp;')
            + '</'+tag+'>';
      }
    }
  }
  return html;
}


/**
 * Remove color codes from the string and return it as plain text
 * @param   string    colored             String with color codes
 * @param   boolean   escape_html_chars   If TRUE (default), then HTML chars will be escaped
 * @param   boolean   make_map            If TRUE, then an array with plain letters' string position
 *                                        mapped to colored leters' string position will be returned.
 * @return  mixed
 */
function coloredToPlain(colored, escape_html_chars, make_map) {
  var plain;
  var pos=0;
  if (typeof(make_map)=='boolean' && make_map) {
    plain=new Array();
  } else {
    var make_map=false;
    plain='';
  }
  if (typeof(colored)=='string' && colored!='') {
    var parts=colored.split('^');
    if (parts.length==1) {
      if (make_map) {
        for (var ii=0; ii<parts[0].length; ii++) {
          plain[pos]=pos++;
        }
      } else {
        plain=parts[0];
      }
    } else {
      for (var i=0; i<parts.length; i++) {
        if (parts[i].length>6) {
          pos+=6;
          if (make_map) {
            for (var ii=6; ii<parts[i].length; ii++) {
              plain[plain.length]=pos++;
            }
          } else {
            plain+=parts[i].substring(6);
          }
        } else if (parts[i].length<6) {
          if (make_map) {
            for (var ii=0; ii<parts[i].length; ii++) {
              plain[plain.length]=pos++;
            }
          } else {
            plain+=parts[i];
          }
        } else {
          pos+=6;
        }
        pos++;
      }
    }
  }
  if (typeof(escape_html_chars)!='boolean' || escape_html_chars) {
    plain=htmlspecialchars(plain);
  }
  return plain;
}


/**
 * Remove double color codes from the string
 * @param   string    colored             String with color codes
 * @return  string
 */
function optimizeColored(colored) {
  var optimized='';
  var parts=null;
  if (typeof(colored)=='string' && colored!='') {
    parts=colored.split('^');
    optimized+=parts[0];
    for (var i=1; i<parts.length; i++) {
      if (parts[i].length>6) {
        if (trimString(parts[i].substring(6))!='') {
          optimized+='^'+parts[i];
        } else {
          optimized+=parts[i].substring(6);
        }
      }
    }
  }
  return optimized;
}


/**
 * Invert value of CSS attribute
 * @param   string    element_id      Element ID to invert CSS property
 * @param   string    attribute_name  CSS attribute name
 * @param   string    value_pair      Possible values as pair separated by "/" (slash)
 * @param   boolean   set_focus       If TRUE, then the element will become focus. Default: FALSE
 * @param   string    clicked_btn_id  Optional style button ID
 * @param   string    clicked_btn_act Optional style button activated texts separated by "/" (slash)
 */
function invertCssProperty(element_id, attribute_name, value_pair, set_focus, clicked_btn_id, clicked_btn_act) {
  if (   typeof(element_id)=='string' && element_id!=''
      && typeof(attribute_name)=='string' && attribute_name!=''
      && typeof(value_pair)=='string' && value_pair!='') {
    var tgt_element=$(element_id);
    var pair=value_pair.split('/');
    var txt_pair=null;
    var js_css_val='';
    if (typeof(tgt_element)=='object' && tgt_element && typeof(tgt_element.style)=='object' && tgt_element.style) {
      if (pair.length==2) {
        js_css_val=cssToJs(attribute_name);
        if (js_css_val!='') {
          eval('try { if ($(\''+element_id+'\').style.'+js_css_val+'==\''+pair[0]+'\') { $(\''+element_id+'\').style.'+js_css_val+'=\''+pair[1]+'\'; } else { $(\''+element_id+'\').style.'+js_css_val+'=\''+pair[0]+'\'; } } catch (e) {}');
          if (   typeof(clicked_btn_id)=='string' && clicked_btn_id!='' && $(clicked_btn_id)
              && typeof(clicked_btn_act)=='string' && clicked_btn_act!='') {
            txt_pair=clicked_btn_act.split('/');
            eval('try { if ($(\''+element_id+'\').style.'+js_css_val+'==\''+pair[0]+'\') { $(\''+clicked_btn_id+'\').innerHTML=\''+htmlspecialchars(txt_pair[0])+'\'; } else { $(\''+clicked_btn_id+'\').innerHTML=\''+htmlspecialchars(txt_pair[1])+'\'; } } catch (e) {}');
          }
          if (isOpera) {
            // Opera hack
            tgt_element.style.display='none';
            setTimeout('$(\''+element_id+'\').style.display=\'\'', 1);
          }
        }
      }
      if (typeof(set_focus)=='boolean' || set_focus==true) {
        if (isOpera) {
          // Opera hack
          setTimeout('try { $(\''+element_id+'\').focus() } catch(e) {}', 1);
        } else {
          try {
            tgt_element.focus();
          } catch (e) {}
        }
      }
    }
  }
}


/**
 * Convert CSS property name into JS CSS property name
 * @param   string    css_name    CSS property name
 * @return string
 */
function cssToJs(css_name) {
  var js_property_name='';
  if (typeof(CSS_to_JS_map[css_name])=='string') {
    js_property_name=CSS_to_JS_map[css_name];
  }
  return js_property_name;
}


/**
 * Insert some text into the text box at current caret position or replace selected text (if any)
 * @param   object    tgt_obj   Target object
 * @param   string    text      Text to insert
 */
function insertAtCaret(tgt_obj, text) {
  if (typeof(tgt_obj)=='object' && tgt_obj) {
    if (!isMozilla) {
      // Not Mozilla ;)
      sel=document.selection;
      range=sel.createRange();
      range.collapse;
      if (range!=null && (sel.type=='Text' || sel.type=='None')) {
        range.text=text;
      }
    } else {
      // Mozilla
      var sStart=tgt_obj.selectionStart;
      var sEnd=tgt_obj.selectionEnd;
      tgt_obj.focus();
      tgt_obj.value=tgt_obj.value.substring(0, sStart)+text+tgt_obj.value.substring(sEnd, tgt_obj.value.length);
      // Move caret
      tgt_obj.setSelectionRange(sStart+text.length, sStart+text.length);
    }
  }
}


/**
 * Log user out
 */
function logOut() {
  logOutWindow=openWindow(formlink+'?s_id='+s_id+'&inc=do_logout', 'logOutWindow', 400, 90, false, false, false, false, true);
  window.onfocus=function() {
    try {
      logOutWindow.focus();
    } catch (e) {}
  }
}


/**
 * Opens a new window
 * @param    string       w_url             The URL of the document to display. If no URL is specified, a new window with about:blank is displayed.
 * @param    string       w_name            The name of the window.
 * @param    int          w_width           The width of the window in pixels.
 * @param    int          w_height          The height of the window in pixels.
 * @param    boolean      w_directories
 * @param    boolean      w_fullscreen
 * @param    boolean      w_location
 * @param    boolean      w_menubar
 * @param    boolean      w_resizable
 * @param    boolean      w_status
 * @param    boolean      w_titlebar
 * @param    boolean      w_toolbar
 * @param    boolean      w_replace         Whether the w_url creates a new entry or replaces the current entry in the window's history list.
 * @param    boolean      w_scrollbars
 * @param    boolean      w_left
 * @param    boolean      w_top
 * @return    object    Window handler
 */
function openWindow(w_url,
                    w_name,
                    w_width,
                    w_height,
                    w_directories,
                    w_fullscreen,
                    w_location,
                    w_menubar,
                    w_resizable,
                    w_status,
                    w_titlebar,
                    w_toolbar,
                    w_replace,
                    w_scrollbars,
                    w_left,
                    w_top) {
  if (typeof(w_name)!='string') {
    w_name='';
  }
  var w_param_str= ((typeof(w_width)=='number')? 'width='+w_width+',' : '')
                  +((typeof(w_height)=='number')? 'height='+w_height+',' : '')
                  +'directories='+((typeof(w_directories)=='boolean' && w_directories)? 'yes' : 'no')+','
                  +'location='+((typeof(w_location)=='boolean' && w_location)? 'yes' : 'no')+','
                  +'menubar='+((typeof(w_menubar)=='boolean' && w_menubar)? 'yes' : 'no')+','
                  +'resizable='+((typeof(w_resizable)=='boolean' && w_resizable)? 'yes' : 'no')+','
                  +'status='+((typeof(w_status)=='boolean' && w_status)? 'yes' : 'no')+','
                  +'titlebar='+((typeof(w_titlebar)=='boolean' && w_titlebar)? 'yes' : 'no')+','
                  +'scrollbars='+((typeof(w_scrollbars)=='boolean' && w_scrollbars)? 'yes' : 'no')+','
                  +'toolbar='+((typeof(w_toolbar)=='boolean' && w_toolbar)? 'yes' : 'no')+','
                  +((typeof(w_left)=='number')? 'left='+w_left+',' : '')
                  +((typeof(w_top)=='number')? 'top='+w_top+',' : '')
                  ;
  if (typeof(w_fullscreen)=='boolean' && w_fullscreen) {
    w_param_str+='fullscreen=yes,';
  }
  w_param_str=w_param_str.substring(0, w_param_str.length-1);
  return window.open(w_url, w_name, w_param_str, typeof(w_replace)!='boolean' || w_replace);
}


/**
 * Display "Enter password" box
 * @param   int       posX              X-Position
 * @param   int       posX              Y-position
 * @param   string    callBackOk        Callback function if OK pressed
 * @param   string    callBackCancel    Callback function if CANCEL pressed
 * @param   string    box_title         String that will be displayed at title box title
 */
function showPasswordFieldBox(posX, posY, callBackOk, callBackCancel, box_title) {
  var password_field_box=$('password_field_box');
  $('password_field_box_input').value='';
  password_field_box.style.display='';
  password_field_box.style.left='0px';
  password_field_box.style.top='0px';
  if (typeof(box_title)=='string' && box_title!='') {
    $('password_field_box_title').innerHTML=htmlspecialchars(box_title);
  } else {
    $('password_field_box_title').innerHTML=htmlspecialchars(getLng('password'));
  }

  var maxAllowedLeft=getUsedWidth()-password_field_box.scrollWidth-10;
  var maxAllowedTop=getUsedHeight()-password_field_box.scrollHeight-10;
  if (posX>maxAllowedLeft) {
    posX=maxAllowedLeft;
  }
  if (posY>maxAllowedTop) {
    posY=maxAllowedTop;
  }
  password_field_box.style.left=posX+'px';
  password_field_box.style.top=posY+'px';

  if (typeof(callBackOk)=='string') {
    password_field_box.callback_ok=callBackOk;
  } else {
    password_field_box.callback_ok='';
  }

  if (typeof(callBackCancel)=='string') {
    password_field_box.callback_cancel=callBackCancel;
  } else {
    password_field_box.callback_cancel='';
  }
  $('password_field_box_input').onkeyup=function(e) {
    switch (getKC(e)) {

      case 13:
        hidePasswordFieldBox(true);
        return false;
      break;

      case 27:
        hidePasswordFieldBox(false);
        return false;
      break;

    }
  };
  setTimeout("$('password_field_box_input').select();", 100);
}

/**
 * Hide "Enter password" box
 */
function hidePasswordFieldBox(submitted) {
  var password_field_box=$('password_field_box');
  password_field_box.style.display='none';
  if (typeof(submitted)=='boolean' && submitted) {
    if (password_field_box.callback_ok!='') {
      try {
        password_field_box.callback_ok=password_field_box.callback_ok.split('/RESULT/').join($('password_field_box_input').value.split('\'').join('\\\''));
        eval(password_field_box.callback_ok);
      } catch (e) {}
    }
  } else {
    if (password_field_box.callback_cancel!='') {
      try {
        password_field_box.callback_cancel=password_field_box.callback_cancel.split('/RESULT/').join($('password_field_box_input').value.split('\'').join('\\\''));
        eval(password_field_box.callback_cancel);
      } catch (e) {}
    }
  }
  password_field_box.callback_ok='';
  password_field_box.callback_cancel='';
  $('password_field_box_input').value='';
}


/**
 * Open admin control panel window
 */
function openAdminWindow() {
  openWindow('./admin.php?s_id='+urlencode(s_id),
             'admin_area',
             screen.width-10,
             screen.height-10,
             false,
             false,
             false,
             false,
             true,
             false,
             false,
             false,
             false,
             true,
             0,
             0);
}

/**
 * Open memberlist window
 */
function openMemberlistWindow() {
  openWindow(formlink+'?s_id='+urlencode(s_id)+'&inc=memberlist',
             'memberlist',
             screen.width-10,
             screen.height-10,
             false,
             false,
             false,
             false,
             true,
             false,
             false,
             false,
             false,
             true,
             0,
             0);
}

/**
 * Show "Edit profile" window
 * @param   int       user_id   Optional. If called by Admin: ID of profile user
 * @param   string    action    Optional. Desired action.
 */
function openEditProfileWindow(user_id, action) {
  if (typeof(user_id)=='undefined') {
    user_id=currentUserId;
  } else {
    user_id=stringToNumber(user_id);
  }
  if (typeof(action)!='string' || action=='') {
    action='do_edit';
  }
  if (user_id>0) {
    openWindow(formlink+'?s_id='+urlencode(s_id)+'&inc=profile_main&profile_user_id='+urlencode(user_id)+'&'+action,
               'user_profile_'+user_id,
               900,
               600,
               false,
               false,
               false,
               false,
               true,
               false,
               false,
               false,
               false,
               true,
               0,
               0);
  }
}


/**
 * Show "Edit moderated rooms" window
 * @param   int   user_id   ID of profile user
 */
function openEditModeratorWindow(user_id) {
  if (typeof(user_id)!='undefined') {
    user_id=stringToNumber(user_id);
  }
  if (user_id>0) {
    openWindow(adminFormlink+'?s_id='+urlencode(s_id)+'&ainc=edit_moderator&popup=1&moderator_user_id='+urlencode(user_id),
               'edit_moderator_'+user_id,
               600,
               600,
               false,
               false,
               false,
               false,
               true,
               false,
               false,
               false,
               false,
               true,
               0,
               0);
  }
}


/**
 * Return border widths of an object as array (TOP, LEFT, BOTTOM, RIGHT)
 * @param   object    obj     Object to get borders from
 * @return  array
 */
function getObjectBorders(obj) {
  var borders=new Array(0, 0, 0, 0);
  var obj_style=null;
  if (typeof(obj)=='object' && obj && obj.style) {
    obj_style=obj.style;
    borders=new Array(parseInt(obj_style.borderTopWidth),
                      parseInt(obj_style.borderLeftWidth),
                      parseInt(obj_style.borderBottomWidth),
                      parseInt(obj_style.borderRightWidth)
                      );
  }
  return borders;
}


/**
 * Put an element to the center of the window
 * @param   object    element       Element to center
 * @param   int       top_offset    Optional. How many pixels to add to the top position. Can be negative or positive.
 * @param   int       left_offset   Optional. How many pixels to add to the left position. Can be negative or positive.
 */
function moveToCenter(element, top_offset, left_offset) {
  if (typeof(element)=='object' && element.style) {
    var offsetTop=document.body.scrollTop>document.documentElement.scrollTop? document.body.scrollTop : document.documentElement.scrollTop;
    var offsetLeft=document.body.scrollLeft>document.documentElement.scrollLeft? document.body.scrollLeft : document.documentElement.scrollLeft;
    if (typeof(top_offset)!='number') {
      top_offset=0;
    }
    if (typeof(left_offset)!='number') {
      left_offset=0;
    }
    element.style.top=Math.round(top_offset+getWinHeight()/2-element.scrollHeight/2+offsetTop)+'px';
    element.style.left=Math.round(left_offset+getWinWidth()/2-element.scrollWidth/2+offsetLeft)+'px';
  }
}


/**
 * Start Drang-and-Drop effect for an object
 * @param   string    id      Object ID
 */
function startDragNDrop(id) {
  var obj=null;
  if (typeof(id)=='string' && id!='' && null!=(obj=$(id))) {
    // No text shall be selected during movement
    disableSelection();
    // Declare functions
    obj.offsetX=mouseX-getLeftPos(obj);
    obj.offsetY=mouseY-getTopPos(obj);
    MouseMoveFuncObj=obj;
    MouseMoveFunc=function () {
      MouseMoveFuncObj.style.top=(mouseY-MouseMoveFuncObj.offsetY)+'px';
      MouseMoveFuncObj.style.left=(mouseX-MouseMoveFuncObj.offsetX)+'px';
    }
  }
}


/**
 * Stops Drang-and-Drop effect for an object
 * @param   string    id      Object ID
 */
function stopDragNDrop(id) {
  var obj=null;
  if (typeof(id)=='string' && id!='' && null!=(obj=$(id))) {
    // Text shall be possible again
    enableSelection();
    // Free data
    MouseMoveFunc=null;
    MouseMoveFuncObj=null;
  }
}


/**
 * Send abuse data to opened abuse window
 * @param   object    aw    Abuse window handler
 * @param   int       id    Abuse ID
 * @return object
 */
function getAbuseData(aw, id) {
  var abuse_data=null;
  if (typeof(aw)=='object' && aw) {
    if (typeof(id)=='number' && id>0 && receivedAbuses[id]) {
      abuse_data=receivedAbuses[id];
    } else {
      try {
        aw.close();
      } catch (e) {}
    }
  }
  return abuse_data;
}


/**
 * Process received abuses
 * @param   object    abuses
 */
function processAbuses(abuses) {
  var abuse=null;
  var abuse_id=0;
  var abuse_data=null;
  for (var abuse_nr=0; abuse_nr<abuses.length; abuse_nr++) {
    abuse_data=new Array();
    abuse_data['id']=stringToNumber(abuses[abuse_nr]['id'][0]);
    abuse_data['date']=abuses[abuse_nr]['date'][0];
    abuse_data['author_id']=stringToNumber(abuses[abuse_nr]['author_id'][0]);
    abuse_data['author_nickname']=abuses[abuse_nr]['author_nickname'][0];
    abuse_data['category']=abuses[abuse_nr]['category'][0];
    abuse_data['room_id']=stringToNumber(abuses[abuse_nr]['room_id'][0]);
    abuse_data['room_name']=abuses[abuse_nr]['room_name'][0];
    abuse_data['abuser_nickname']=abuses[abuse_nr]['abuser_nickname'][0];
    abuse_data['description']=abuses[abuse_nr]['description'][0];
    receivedAbuses[abuse_data['id']]=abuse_data;
    openWindow(formlink+'?s_id='+s_id+'&inc=abuse', 'abuse_'+abuse_data['id'], 600, 450, false, false, false, false, true);
  }
}


/**
 * Display enlarged avatar image
 * @param   object    src_obj       Event source object
 * @param   int       avatar_bid    User avatar image Binaryfile ID
 */
function showUserlistAvatarThumb(src_obj, avatar_bid) {
  var avatar_hover_thumbnail_img=$('avatar_hover_thumbnail_img');
  var width=85;
  var height=120;
  var src_top=0;
  var src_bottom=0;
  var src_left=0;
  var src_right=0;
  var tolerance=10;
  if (src_obj && avatar_hover_thumbnail_img) {
    src_top=getTopPos(src_obj);
    src_bottom=src_top+src_obj.height;
    src_left=getLeftPos(src_obj);
    src_right=src_left+src_obj.width;
    avatar_hover_thumbnail_img.src=formlink+'?b_x='+height+'&b_y='+width+'&b_id='+urlencode(avatar_bid)+'&s_id='+urlencode(s_id);
    avatar_hover_thumbnail_img.style.width=width+'px';
    avatar_hover_thumbnail_img.style.height=height+'px';
    avatar_hover_thumbnail_img.min_top=src_top-tolerance;
    avatar_hover_thumbnail_img.max_top=src_bottom+tolerance;
    avatar_hover_thumbnail_img.min_left=src_left-tolerance;
    avatar_hover_thumbnail_img.max_left=src_right+tolerance;
    avatar_hover_thumbnail_img.onload=function() {
      MouseMoveFuncObj=avatar_hover_thumbnail_img;
      MouseMoveFunc=function () {
        if (   mouseX>=MouseMoveFuncObj.min_left && mouseX<=MouseMoveFuncObj.max_left
            && mouseY>=MouseMoveFuncObj.min_top && mouseY<=MouseMoveFuncObj.max_top) {
          MouseMoveFuncObj.style.top=(mouseY+3)+'px';
          MouseMoveFuncObj.style.left=(mouseX+3)+'px';
        } else {
          hideUserlistAvatarThumb();
        }
      }
      this.style.display='';
      MouseMoveFunc();
    }
  }
}


/**
 * Hide enlarged avatar image
 */
function hideUserlistAvatarThumb() {
  var avatar_hover_thumbnail_img=$('avatar_hover_thumbnail_img');
  if (avatar_hover_thumbnail_img) {
    avatar_hover_thumbnail_img.style.display='none';
    MouseMoveFunc=null;
    MouseMoveFuncObj=null;
  }
}


/**
 * Convert *color style property value from format "rgb(x,y,z)" into format "#XXYYZZ"
 * @param   string    rgb_color   Color in format "rgb(x,y,z)"
 * @param   string    prefix      A string to prepend to the return value. Default is '#'
 * @return string
 */
function colorRgbToHex(rgb_color, prefix) {
  var ret=rgb_color;
  var rgb=Array();
  if (typeof(prefix)=='undefined') prefix='#';
  if (typeof(prefix)!='string') {
    prefix='';
  }
  if (typeof(rgb_color)=='string') {
    rgb_color=rgb_color.split(' ').join('');
    if (rgb_color.substring(0, 4)=='rgb(') {
      rgb_color=rgb_color.substring(4);
      rgb_color=rgb_color.substring(0, rgb_color.length-1);
      rgb=rgb_color.split(',');
      ret=prefix+decHex(rgb[0], 2)+decHex(rgb[1], 2)+decHex(rgb[2], 2);
    } else if (rgb_color.substring(0, 1)=='#') {
      ret=prefix+rgb_color.substring(1, rgb_color.length);
    }
  }
  return ret;
}
