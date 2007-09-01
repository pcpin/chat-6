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
 * Initialize window
 */
function initImageWindow() {
  $('b_img').onclick=function() {
    window.close();
  };
  $('b_img').style.cursor='pointer';
  $('b_img').title=getLng('close_window');

  document.body.style.padding="0px";
  document.body.style.margin="0px";
  

  window.onblur=function() {
    focus();
  };
}


/**
 * Image loaded
 */
function imageLoaded(img_obj) {
  setCssClass(document.body, 'body');
  var width_padding=stringToNumber(document.body.style.paddingLeft.substring(0, document.body.style.paddingLeft.length-2))
                   +stringToNumber(document.body.style.paddingRight.substring(0, document.body.style.paddingRight.length-2));
  var height_padding=stringToNumber(document.body.style.paddingTop.substring(0, document.body.style.paddingTop.length-2))
                    +stringToNumber(document.body.style.paddingBottom.substring(0, document.body.style.paddingBottom.length-2));
  window.resizeBy(img_obj.width-getWinWidth()+width_padding+1, img_obj.height-getWinHeight()+height_padding+1);
  img_obj.style.position='absolute';
  moveToCenter(img_obj);
  window.onresize=function() {
    moveToCenter($('b_img'));
  }
}
