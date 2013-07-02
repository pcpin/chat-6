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
 * ID of an object to insert smilie code into (if not empty)
 * @var string
 */
var smiliebox_tgt_obj_id='';

/**
 * Name of the global variable to assign smilie code
 * @var string
 */
var smiliebox_tgt_tgt_var='';

/**
 * Flag: if TRUE, then smiliebox will be NOT resized and moved automatically. Default is FALSE.
 * @var boolean
*/
var SmilieBoxNoResizeMove=false;


/**
 * Smilie object
 * @var object
 */
function Smilie(id, code, source) {

  /**
   * Smilie ID
   * @var string
   */
  this.id=id;

  /**
   * Smilie code
   * @var string
   */
  this.code=code;

  /**
   * Smilie image source HREF
   * @var string
   */
  this.source=source;

}


/**
 * Smilie list
 * @var object
 */
var SmilieList=new function() {

  /**
   * Aray with smilies
   * @var object
   */
  this.SmilieList=new Array();

  /**
   * Total smilies count
   * @var object
   */
  this.SmilieListLength=0;

  /**
   * Top-Position of smilie list opener object
   * @var int
   */
  this.OpenerTop=0;

  /**
   * Left-Position of smilie list opener object
   * @var int
   */
  this.OpenerLeft=0;

  /**
   * Loaded smilie images count
   * @var int
   */
  this.LoadedSmiliesCount=0;

  /**
   * Flag: if true, then position and width/height of smilielist can not be automatically adjusted
   * @var boolean
   */
  this.NoResizeMove=false;


  /**
   * Reset object
   * @param   int   opener_top    Top position of opener object
   * @param   int   opener_left   Left position of opener object
   */
  this.reset=function(opener_top, opener_left) {
    this.SmilieList=new Array();
    this.SmilieListLength=0;
    this.OpenerTop=opener_top;
    this.OpenerLeft=opener_left;
    this.LoadedSmiliesCount=0;
    this.NoResizeMove=false;
  }


  /**
   * Add new smilie to an array
   * @param   string    id        Smilie ID
   * @param   string    code      Smilie code
   * @param   string    source    Image source HREF
   */
  this.addSmilie=function(id, code, source) {
    var smilie_img;
    this.SmilieList[code]=new Smilie(id, code, source);
    this.SmilieListLength++;
    smilie_img = document.createElement('IMG');
    smilie_img.src = source;
    smilie_img.style.display = 'none';
    $('body_contents').appendChild(smilie_img);
  }


  /**
   * Set smilie list position and display it
   */
  this.SetPosition=function() {
    this.LoadedSmiliesCount++;
    var sb=$('smilie_selection_box');
    if (this.LoadedSmiliesCount==this.SmilieListLength && typeof(sb)=='object' && sb) {
      if (false==this.NoResizeMove) {
        sb.style.display='';
        var box_width=sb.scrollWidth;
        var box_height=sb.scrollHeight;
        if (this.OpenerTop<box_height+5) {
          sb.style.top='0px';
        } else {
          sb.style.top=(this.OpenerTop-box_height-5)+'px';
        }
        if (this.OpenerLeft+box_width>winWidth) {
          sb.style.left=(winWidth-box_width)+'px';
        } else {
          sb.style.left=this.OpenerLeft+'px';
        }
      }
      toggleProgressBar(false);
      sb.style.display='none';
      if (!SmilieBoxNoResizeMove) {
        setTimeout('setSmilieBoxSizes()', 100);
      } else {
        setTimeout("$('smilie_selection_box').style.display='';", 10);
      }
    }
  }
}


/**
 * Initialize smilie list
 * @param   int       opener_top    Top position of opener object
 * @param   int       opener_left   Left position of opener object
 */
function initSmilieList(opener_top, opener_left) {
  var smiliebox_smilies=$$('img', $('smilie_selection_box'));
  var added_smilies=new Array();
  if ((typeof(smiliebox_smilies)=='object' || typeof(smiliebox_smilies)=='function') && smiliebox_smilies && smiliebox_smilies.length) {
    SmilieList.reset(opener_top, opener_left);
    for (var i=0; i<smiliebox_smilies.length; i++) {
      if (typeof(smiliebox_smilies[i].id)=='string' && smiliebox_smilies[i].id.length>13 && 0==smiliebox_smilies[i].id.indexOf('smilie_image_')) {
        SmilieList.addSmilie(smiliebox_smilies[i].id.substring(13), smiliebox_smilies[i].alt, smiliebox_smilies[i].name);
        added_smilies.push(i);
      }
    }
    for (var i=0; i<added_smilies.length; i++) {
      if (smiliebox_smilies[added_smilies[i]].src.substring(smiliebox_smilies[added_smilies[i]].src.lastIndexOf(smiliebox_smilies[added_smilies[i]].name))==smiliebox_smilies[added_smilies[i]].name) {
        // Image already loaded
        SmilieList.SetPosition();
      } else {
        // Assign onload event
        eval('$(\''+smiliebox_smilies[added_smilies[i]].id+'\').onload=function() { SmilieList.SetPosition() };');
      }
      smiliebox_smilies[added_smilies[i]].onclick=function() {
        MouseMoveFunc=null;
        setSmilieCode(this.alt);
      }
      smiliebox_smilies[added_smilies[i]].onmousedown=function() {
        this.mouseX=mouseX;
        this.mouseY=mouseY;
        MouseMoveFuncObj=this;
        MouseMoveFunc=function() {
          if (Math.abs(MouseMoveFuncObj.mouseX-mouseX)>3 || Math.abs(MouseMoveFuncObj.mouseY-mouseY)>3) {
            MouseMoveFuncObj.dragStarted();
          }
        }
        return false;
      }

      // Drag-and-Drop implementation
      smiliebox_smilies[added_smilies[i]].dragStarted=function() {
        $('drag_smilie').src=this.src;
        MouseMoveFunc=function() {
                        $('drag_smilie').style.top=Math.round(mouseY-$('drag_smilie').scrollHeight/2)+'px';
                        $('drag_smilie').style.left=Math.round(mouseX-$('drag_smilie').scrollWidth/2)+'px';
                        $('drag_smilie').style.display='';
        }
        MouseMoveFuncObj=this;
        document.onmouseup_old_smilies=document.onmouseup;
        document.onmouseup=MouseMoveFuncObj.dragEnd;
      }
      smiliebox_smilies[added_smilies[i]].dragEnd=function() {
        document.onmouseup=document.onmouseup_old_smilies;
        var input_height=parseInt($(smiliebox_tgt_obj_id).style.height);
        if (!isDigitString(input_height) || input_height<$(smiliebox_tgt_obj_id).scrollHeight) input_height=$(smiliebox_tgt_obj_id).scrollHeight;
        var minX=getLeftPos($(smiliebox_tgt_obj_id));
        var maxX=minX+$(smiliebox_tgt_obj_id).scrollWidth;
        var minY=getTopPos($(smiliebox_tgt_obj_id));
        var maxY=minY+input_height;
        MouseMoveFunc=null;
        $('drag_smilie').style.display='none';
        if (mouseX>=minX && mouseX<=maxX && mouseY>=minY && mouseY<=maxY) {
          setSmilieCode(MouseMoveFuncObj.alt);
        }
        MouseMoveFuncObj=null;
      }

    }
  }
}


/**
 * Display smilie selection box and append the code of selected smilie to supplied object as the value
 * @param   string    tgt_obj_id  ID of an object to put the selected smilies into
 * @param   string    tgt_var     Name of the global variable to set selected smilie code to
 * @param   object    openerObj   Opener object
 * @param   boolean   no_resize   Optional. If TRUE, then smiliebox will not be resized and moved automatically. Default is FALSE.
 */
function openSmilieBox(tgt_obj_id, tgt_var, openerObj, no_resize) {
  var smilie_img=null;
  if (typeof(openerObj)!='undefined' && openerObj && openerObj.onmouseout) {
    openerObj.onmouseout();
  }
  if (typeof(no_resize)!='boolean') {
    no_resize=false;
  }
  SmilieBoxNoResizeMove=no_resize;
  SmilieList.NoResizeMove=SmilieBoxNoResizeMove;
  if (SmilieList.SmilieListLength>0) {
    toggleProgressBar(true);
    initSmilieList(getTopPos(openerObj), getLeftPos(openerObj));
    for (var i in SmilieList.SmilieList) {
      if (smilie_img=$('smilie_image_'+SmilieList.SmilieList[i].id)) {
        if (smilie_img.src!=smilie_img.name) {
          // Smilie image is not loaded yet
          smilie_img.src=smilie_img.name;
        }
      }
    }
    if (typeof(tgt_obj_id)!='string') {
      tgt_obj_id='';
    }
    if (typeof(css_attr)!='string') {
      css_attr='';
    }
    if (typeof(tgt_var)!='string') {
      tgt_var='';
    }
    smiliebox_tgt_obj_id=tgt_obj_id;
    smiliebox_tgt_tgt_var=tgt_var;
  }
}


/**
 * Set smilie box width and height (if displayed not in tool bar)
 */
function setSmilieBoxSizes() {
  if (!SmilieBoxNoResizeMove) {
    var newWidth=0;
    var newHeight=0;
    var container=$('smiliebox_container');
    var sb=$('smilie_selection_box');
    sb.style.display='';
    newWidth=sb.scrollWidth;
    newHeight=sb.scrollHeight-$('smiliebox_header').scrollHeight;
    sb.style.display='none';
    if (newWidth>winWidth-100) {
      newWidth=winWidth-100;
      newHeight+=30;
    }
    if (newHeight>winHeight-100) {
      newHeight=winHeight-100;
      newWidth+=30;
    }
    container.style.width=newWidth+'px';
    container.style.height=newHeight+'px';
    container.style.overflow='auto';
    $('smiliebox_header').style.width=newWidth+'px';
    sb.style.display='';
    moveToCenter(sb);
  }
}


/**
 * This function is called if smilie was clicked
 * @param   string    smilie_code     Code of clicked smilie
 */
function setSmilieCode(smilie_code) {
  var smiliebox_tgt_obj;
  if (typeof(smilie_code) === 'string' && smilie_code !== '' && smiliebox_tgt_obj_id !== '') {
    if (null !== (smiliebox_tgt_obj = $(smiliebox_tgt_obj_id))) {
      try {
        smiliebox_tgt_obj.focus();
      } catch (e) {}
      try {
        insertAtCaret(smiliebox_tgt_obj, ' ' + smilie_code + ' ');
      } catch (e) {
        try {
          smiliebox_tgt_obj.value += ' ' + smilie_code + ' ';
        } catch (e) {}
      }
    }
    if (smiliebox_tgt_tgt_var !== '') {
      smiliebox_tgt_tgt_var = smilie_code;
    }
  }
}

/**
 * Hide smilie box
 * @param   string    smilie_code     Code of clicked smilie
 */
function closeSmilieBox() {
  $('smilie_selection_box').style.display='none';
}
