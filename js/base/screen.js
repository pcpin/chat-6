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
 * Screen width, in pixels.
 * @var int
 */
var winWidth=getWinWidth();

/**
 * Screen height, in pixels.
 * @var int
 */
var winHeight=getWinHeight();

/**
 * Obtain the width in pixels of the given window
 * @param   object    wh    Window handler. Default: current window
 * @return  int   Window width in pixels
 */
function getWinWidth(wh) {
  var winWidth=-1;
  if (typeof(wh)=='undefined') {
    wh=window;
  }
  if (typeof(wh.innerWidth)=='number') {
    // Non-IE browser
    winWidth=wh.innerWidth;
  } else if (wh.document.documentElement && (wh.document.documentElement.clientWidth || wh.document.documentElement.clientHeight)) {
    // IE 6+ browser in 'standards compliant mode'
    winWidth=wh.document.documentElement.clientWidth;
  } else if (wh.document.body && (wh.document.body.clientWidth || wh.document.body.clientHeight)) {
    // IE 4 compatible browser
    winWidth=wh.document.body.clientWidth;
  }
  return winWidth;
}

/**
 * Obtain the height in pixels of the given window
 * @param   object    wh    Window handler. Default: current window
 * @return  int   Window height in pixels
 */
function getWinHeight(wh) {
  var winHeight=-1;
  if (typeof(wh)=='undefined') {
    wh=window;
  }
  if (typeof(wh.innerHeight)=='number') {
    // Non-IE browser
    winHeight=wh.innerHeight;
  } else if (wh.document.documentElement && (wh.document.documentElement.clientHeight || wh.document.documentElement.clientHeight)) {
    // IE 6+ browser in 'standards compliant mode'
    winHeight=wh.document.documentElement.clientHeight;
  } else if (wh.document.body && (wh.document.body.clientHeight || wh.document.body.clientHeight)) {
    // IE 4 compatible browser
    winHeight=wh.document.body.clientHeight;
  }
  return winHeight;
}


/**
 * Obtain the width of the document in the given window
 * @param   object    wh    Window handler. Default: current window
 * @return  int   Document width
 */
function getUsedWidth(wh) {
  var usedWidth=-1;
  if (typeof(wh)=='undefined') {
    wh=window;
  }
  try {
    usedWidth=wh.document.documentElement.scrollWidth;
  } catch (e) {}
  return usedWidth;
}


/**
 * Obtain the height of the document in the given window
 * @param   object    wh    Window handler. Default: current window
 * @return  int   Document height
 */
function getUsedHeight(wh) {
  var usedHeight=-1;
  if (typeof(wh)=='undefined') {
    wh=window;
  }
  try {
    usedHeight=wh.document.documentElement.scrollHeight;
  } catch (e) {}
  return usedHeight;
}


/**
 * Get absolute top position of static-positioned element
 * @param   object    tgt_element   Element
 * @return  int
 */
function getTopPos(tgt_element) {
  var pos=0;
  if (typeof(tgt_element)=='object' && tgt_element && tgt_element.offsetParent) {
    pos=tgt_element.offsetTop;
    while (tgt_element=tgt_element.offsetParent) {
      pos+=tgt_element.offsetTop;
    }
    if (typeof(pos)!='number') pos=0;
  }
  return pos;
}

/**
 * Get absolute top position of static-positioned element
 * @param   object    tgt_element   Element
 * @return  int
 */
function getLeftPos(tgt_element) {
  var pos=0;
  if (typeof(tgt_element)=='object' && tgt_element && tgt_element.offsetParent) {
    pos=tgt_element.offsetLeft;
    while (tgt_element=tgt_element.offsetParent) {
      pos+=tgt_element.offsetLeft;
    }
    if (typeof(pos)!='number') pos=0;
  }
  return pos;
}


/**
 * Resize window to fit the document
 * @param   int       add           Optional. If not empty, then this value will be added to window width
 * @param   boolean   allow_reduce  Optional. If not TRUE window height will be reduced, if needed
 */
function resizeForDocumentHeight(add, allow_reduce) {
  if (typeof(add)!='number') add=0;
  if (typeof(allow_reduce)!='boolean') allow_reduce=true;
  var used_height=getUsedHeight(document.body);
  var resize_by=0;
  if (used_height>0) {
    if (allow_reduce|| used_height>getWinHeight()) {
      resize_by=used_height-getWinHeight()+add;
    }
  } else {
    $('last_element_dummy').style.display='';
    used_height=getTopPos($('last_element_dummy'));
    $('last_element_dummy').style.display='none';
    if (allow_reduce || used_height>getWinHeight()) {
      resize_by=used_height-getWinHeight()+add;
    }
  }
  try {
    if (resize_by+getWinHeight()>window.opener.getWinHeight()-10) {
      resize_by=window.opener.getWinHeight()-getWinHeight()-10;
    }
  } catch (e) {}
  if (resize_by>0) {
    window.resizeBy(0, resize_by);
  }
}