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
 * ID of an object to set color on (if not empty)
 * @var string
 */
var colorbox_tgt_obj_id='';

/**
 * Name of CSS attribute of colorbox_tgt_element_id to assign color to
 * @var string
 */
var colorbox_tgt_css_attr='';

/**
 * Opener object ID
 * @var string
 */
var colorbox_opener_id='';

/**
 * Name of CSS attribute of opener object to assign color to
 * @var string
 */
var colorbox_opener_css_attr='';

/**
 * Name of the global variable to assign color to
 * @var string
 */
var colorbox_tgt_tgt_var='';

/**
 * Function to call from colorClicked()
 * @var object
 */
var colorbox_callback_func=null;


/**
 * Display color selection box and set clicked color to supplied object as CSS property
 * @param   string    tgt_obj_id      ID of an object to set color on. If empty, clicked color code will be returmed as string.
 * @param   string    css_attr        Name of CSS attribute from tgt_obj to set clicked color to
 * @param   object    openerObj       Opener object
 * @param   string    tgt_var         Name of the global variable to set clicked color to
 * @param   boolean   center          Optional. If TRUE, then colorbox will be displayed at the center of the window
 * @param   string    opener_css      Optional. If specified, then opener object' CSS property will be also updated with picked color.
 *                                    Opener object MUST have an ID in this case!
 * @param   boolean   show_input      Optional. If TRUE: Text input field will be also displayed in color box. Default: FALSE.
 * @param   string    initial_color   Optional. Default value for text input field
 * @param   boolean   show_header     Optional. If TRUE: Colorbox header row will be also displayed
 * @param   int       top             Optional. If center!=TRUE: top color box position. NULL: ignore
 * @param   int       left            Optional. If center!=TRUE: left color box position. NULL: ignore
 */
function openColorBox(tgt_obj_id, css_attr, openerObj, tgt_var, center, opener_css, show_input, initial_color, colors_header_row, top, left) {
  var openerTop=getTopPos(openerObj);
  var openerLeft=getLeftPos(openerObj);
  var color_selection_box=$('color_selection_box');
  var colorbox_areas=$$('area', color_selection_box);
  var name_='';
  if (typeof(tgt_obj_id)!='string') {
    tgt_obj_id='';
  }
  if (typeof(css_attr)!='string') {
    css_attr='';
  }
  if (typeof(tgt_var)!='string') {
    tgt_var='';
  }
  if (typeof(center)!='boolean') {
    center=false;
  }
  if (typeof(colors_header_row)!='boolean') {
    colors_header_row=true;
  }
  if ((typeof(colorbox_areas)=='object' || typeof(colorbox_areas)=='function') && colorbox_areas) {
    for (var i=0; i<colorbox_areas.length; i++) {
      name_=colorbox_areas[i].getAttribute('name');
      if (name_ && 0==name_.indexOf('colorbox_code_')) {
        colorbox_areas[i].onclick=function(e) {
          closeColorBox(this.getAttribute('name').substring(14), true);
        }
      }
    }
    color_selection_box.style.display='';
    if (center==true) {
      moveToCenter(color_selection_box);
    } else if (typeof(top)=='number' && top>=0 && typeof(left)=='number' && left>=0) {
      color_selection_box.style.top=top+'px';
      color_selection_box.style.left=left+'px';
    } else {
      color_selection_box.style.top=(openerTop-color_selection_box.scrollHeight-1)+'px';
      color_selection_box.style.left=(openerLeft+1)+'px';
    }
    colorbox_tgt_obj_id=tgt_obj_id;
    colorbox_tgt_css_attr=css_attr;
    colorbox_tgt_tgt_var=tgt_var;
    if (typeof(opener_css)=='string' && openerObj.id!='') {
      colorbox_opener_id=openerObj.id;
      colorbox_opener_css_attr=opener_css;
    } else {
      colorbox_opener_id='';
      colorbox_opener_css_attr='';
    }
    if (typeof(show_input)=='boolean' && show_input) {
      $('colorbox_selected_color_input_row').style.display='';
      if (typeof(initial_color)=='string') {
        initial_color=colorRgbToHex(initial_color, null);
        if (initial_color!='') {
          $('colorbox_selected_color_input').value=initial_color.substring(0, 6);
        }
      }
    } else {
      $('colorbox_selected_color_input_row').style.display='none';
    }
    $('colors_header_row').style.display=colors_header_row? '' : 'none';
    color_selection_box.style.display='none';
    setTimeout("$('color_selection_box').style.display=''", 10);
  }
}

/**
 * Apply clicked color and hide color box
 * @param   string    color_code    Code of clicked color
 * @param   boolean   keep_open     Optional. If TRUE, then colorbox will stay visible
 */
function closeColorBox(color_code, keep_open) {
  var clicked_color='';
  if (typeof(color_code)=='string') {
    var reg=new RegExp(/^[abcdefABCDEF0-9]{6}$/);
    if (null!=color_code.match(reg)) {
      clicked_color=color_code.toLowerCase();
    }
  }
  if (clicked_color!='') {
    colorClicked(clicked_color);
    if ($('colorbox_selected_color_input').value!=clicked_color) {
      $('colorbox_selected_color_input').value=clicked_color;
    }
    if (colorbox_tgt_obj_id!='' && colorbox_tgt_css_attr!='') {
      if (clicked_color!='') {
        eval('try { $(\''+colorbox_tgt_obj_id+'\').style.'+cssToJs(colorbox_tgt_css_attr)+'=\'#'+clicked_color+'\'; } catch (e) {}');
      }
      eval('try { $(\''+colorbox_tgt_obj_id+'\').focus(); } catch (e) {}');
    }
    if (colorbox_tgt_tgt_var!='' && clicked_color!='') {
      eval('try { '+colorbox_tgt_tgt_var+'=\''+clicked_color+'\'; } catch (e) {}');
    }
    if (colorbox_opener_id!='' && colorbox_opener_css_attr!='') {
      eval('try { $(\''+colorbox_opener_id+'\').style.'+cssToJs(colorbox_opener_css_attr)+'=\'#'+clicked_color+'\'; } catch (e) {}');
    }
  }
  if (typeof(keep_open)!='boolean' || !keep_open) {
    $('color_selection_box').style.display='none';
  }
}

function applyColorCode(text_box_id, color, previous_colored) {
  var newColored='';
  var sel=null;
  var range=null;
  var cstring_prepend='';
  var cstring_change='';
  var cstring_append='';
  var range_txt_orig='';
  var rand='';
  var sStart=0;
  var sEnd=0;
  var sStartTxt='';
  var sSelTxt='';
  var sEndTxt='';
  var parts=null;
  var last_color='';
  try {
    var pBox=$(text_box_id);
    if (!isMozilla) {
      pBox.focus();
      sel=document.selection;
      range=sel.createRange();
      range.collapse;
      if (range!=null && (sel.type=='Text' || sel.type=='None')) {
        if (range.text.length>0) {
          // Apply color to selection
          sSelTxt=range.text;
          do {
            rand=(Math.random().toString()).substring(2);
            if (-1==pBox.value.indexOf(rand)) {
              range.text=rand+range.text;
              break;
            }
          } while (true);
          sStartTxt=pBox.value.substring(0, pBox.value.indexOf(rand));
          sEndTxt=pBox.value.substring(sStartTxt.length+rand.length+sSelTxt.length);
          pBox.value=sStartTxt+sSelTxt+sEndTxt;
          cstring_prepend=findInColored(previous_colored, sStartTxt, 0);
          cstring_change=findInColored(previous_colored, sSelTxt, coloredToPlain(cstring_prepend, false).length);
          cstring_append=findInColored(previous_colored, sEndTxt, coloredToPlain(cstring_prepend+cstring_change, false).length);
        } else {
          // Apply color from cursor position
          do {
            rand=(Math.random().toString()).substring(2);
            if (-1==pBox.value.indexOf(rand)) {
              range.text=rand+range.text;
              break;
            }
          } while (true);
          sStartTxt=pBox.value.substring(0, pBox.value.indexOf(rand));
          sSelTxt=pBox.value.substring(sStartTxt.length+rand.length);
          pBox.value=sStartTxt+sSelTxt+sEndTxt;
          cstring_prepend=findInColored(previous_colored, sStartTxt, 0);
          cstring_change=findInColored(previous_colored, sSelTxt, coloredToPlain(cstring_prepend, false).length);
          cstring_append=findInColored(previous_colored, sEndTxt, coloredToPlain(cstring_prepend+cstring_change, false).length);
        }
      }
    } else {
      // Mozilla
      sStart=pBox.selectionStart;
      sEnd=pBox.selectionEnd;
      sStartTxt=pBox.value.substring(0, sStart);
      sSelTxt=pBox.value.substring(sStart, sEnd);
      sEndTxt=pBox.value.substring(sEnd, pBox.value.length);
      if (sStart!=sEnd) {
        // Apply color to selection
        cstring_prepend=findInColored(previous_colored, sStartTxt, 0);
        cstring_change=findInColored(previous_colored, sSelTxt, coloredToPlain(cstring_prepend, false).length);
        cstring_append=findInColored(previous_colored, sEndTxt, coloredToPlain(cstring_prepend+cstring_change, false).length);
      } else {
        // Apply color from cursor position
        sSelTxt=sEndTxt;
        sEndTxt='';
        cstring_prepend=findInColored(previous_colored, sStartTxt, 0);
        cstring_change=findInColored(previous_colored, sSelTxt, coloredToPlain(cstring_prepend, false).length);
        cstring_append=findInColored(previous_colored, sEndTxt, coloredToPlain(cstring_prepend+cstring_change, false).length);
      }
    }
    if (cstring_prepend.length>0 || cstring_change.length>0 || cstring_append.length>0) {
      // Get last color code
      parts=(cstring_prepend+cstring_change).split('^');
      last_color='';
      for (var i=parts.length-1; i>0; i--) {
        if (parts[i].length>6) {
          last_color=parts[i].substr(0, 6);
          break;
        }
      }
      if (last_color!='') {
        cstring_append='^'+last_color+cstring_append;
      }
      // Remove all color codes from selected text and apply the new color
      cstring_change='^'+color+coloredToPlain(cstring_change, false);
      newColored=cstring_prepend+cstring_change+cstring_append;
    }
  } catch (e) {}
  return newColored;
}


/**
 * Find a first occurance of plain string in colored string and return it's colored equivalent
 * @param   string    colored   Colored string
 * @param   string    needle    String to find
 * @param   int       offset    Offset to start search from (plain)
 */
function findInColored(colored, needle, offset) {
  var found='';
  var plain='';
  var plain_map='';
  var plain_start=0;
  if (colored!='' && needle!='') {
    if (typeof(offset)!='number') {
      offset=0;
    }
    plain=coloredToPlain(colored, false, false);
    plain_map=coloredToPlain(colored, false, true);
    plain_start=plain.indexOf(needle, offset);
    if (typeof(plain_map[plain_start+needle.length])!='undefined') {
      found=colored.substring((plain_start>0)? plain_map[plain_start] : 0, plain_map[plain_start+needle.length]);
    } else {
      found=colored.substring((plain_start>0)? plain_map[plain_start] : 0);
    }
  }
  return found;
}

/**
 * Function to call each time color was clicked.
 * @param   string    color   Clicked color (e.g. 'ffee33')
 */
function colorClicked(color) {
  if (colorbox_callback_func) {
    colorbox_callback_func(color);
  }
}