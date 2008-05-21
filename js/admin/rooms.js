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
 * Categories as an Array
 * @var object
 */
var Categories=new Array();

/**
 * Rooms as an Array
 * @var object
 */
var Rooms=new Array();

/**
 * Initialize
 */
function initRoomsForm() {
  // Get category tree
  getCategoryTree();
}

/**
 * Get category tree
 */
function getCategoryTree() {
  sendData('_CALLBACK_getCategoryTree()', formlink, 'POST', 'ajax=get_room_structure&s_id='+urlencode(s_id));
}
function _CALLBACK_getCategoryTree() {
//debug(actionHandler.getResponseString()); return false;
  var categories_tbl=$('categories_tbl');

  var categories=null;
  var category=null;
  var category_nr=0;
  var category_id=0;
  var category_name='';
  var category_description='';
  var tr=null;
  var td=null;
  var room=null;
  var room_nr=0;
  var room_id=0;
  var room_name='';
  var room_description='';

  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    showCategories();
    // Clear table
    Categories=new Array();
    Rooms=new Array();
    while (categories_tbl.rows.length>2) {
      categories_tbl.deleteRow(-1);
    }
    // Get categories root
    categories=actionHandler.data['category'][0];
    for (category_nr=0; category_nr<categories['category'].length; category_nr++) {
      category=categories['category'][category_nr];
      category_id=stringToNumber(category['id'][0]);
      category_name=category['name'][0];
      category_description=category['description'][0];
      Categories[category_id]=new Array();
      Categories[category_id]['id']=category_id;
      Categories[category_id]['parent_id']=stringToNumber(category['parent_id'][0]);
      Categories[category_id]['name']=category_name;
      Categories[category_id]['description']=category_description;
      Categories[category_id]['creatable_rooms']=(1==category['creatable_rooms'][0]);
      Categories[category_id]['creatable_rooms_flag']=category['creatable_rooms_flag'][0];

      tr=categories_tbl.insertRow(-1);
      tr.title=htmlspecialchars(getLng('chat_category')+' '+category_name+' ('+(typeof(category['room'])!='undefined'? category['room'].length : 0)+' '+getLng('rooms')+')'+"\n"+category_description);

      td=tr.insertCell(-1);
      td.innerHTML=htmlspecialchars(category_name);
      td.colSpan=3;
      setCssClass(td, '.tbl_header_sub');

      td=tr.insertCell(-1);
      td.innerHTML='<a class="tbl_header_sub_link" href=":" title="'+htmlspecialchars(getLng('edit'))+'" onclick="showEditCategoryForm('+htmlspecialchars(category_id)+'); return false;">'+htmlspecialchars(getLng('edit'))+'</a>';
      setCssClass(td, '.tbl_header_sub');
      td.style.width='1%';
      td.style.textAlign='center';
      td.noWrap=true;

      td=tr.insertCell(-1);
      td.innerHTML='<a class="tbl_header_sub_link" href=":" title="'+htmlspecialchars(getLng('delete'))+'" onclick="deleteCategory('+htmlspecialchars(category_id)+'); return false;">'+htmlspecialchars(getLng('delete'))+'</a>';
      setCssClass(td, '.tbl_header_sub');
      td.style.width='1%';
      td.style.textAlign='center';
      td.noWrap=true;

      td=tr.insertCell(-1);
      td.innerHTML='<a class="tbl_header_sub_link" href=":" title="'+htmlspecialchars(getLng('move_up'))+'" onclick="moveCategory('+htmlspecialchars(category_id)+', 0); return false;">'+htmlspecialchars(getLng('move_up'))+'</a>'
                  +'&nbsp;&nbsp;&nbsp;&nbsp;'
                  +'<a class="tbl_header_sub_link" href=":" title="'+htmlspecialchars(getLng('move_down'))+'" onclick="moveCategory('+htmlspecialchars(category_id)+', 1); return false;">'+htmlspecialchars(getLng('move_down'))+'</a>';
      setCssClass(td, '.tbl_header_sub');
      td.style.width='1%';
      td.style.textAlign='center';
      td.noWrap=true;

      // Rooms
      if (typeof(category['room'])=='undefined' || category['room'].length==0) {
        // Category has no rooms
        tr=categories_tbl.insertRow(-1);
        td=tr.insertCell(-1);
        td.innerHTML=htmlspecialchars(getLng('category_has_no_rooms'));
        td.colSpan=6;
        setCssClass(td, '.tbl_row');
      } else {
        // Display rooms
        room_nr=0;
        for (room_nr=0; room_nr<category['room'].length; room_nr++) {
          room=category['room'][room_nr];
          room_id=room['id'][0];
          room_name=room['name'][0];
          room_description=room['description'][0];
          Rooms[room_id]=new Array();
          Rooms[room_id]['id']=room_id;
          Rooms[room_id]['category_id']=category_id;
          Rooms[room_id]['name']=room_name;
          Rooms[room_id]['description']=room_description;
          Rooms[room_id]['background_image']=stringToNumber(room['background_image'][0]);
          Rooms[room_id]['background_image_width']=stringToNumber(room['background_image_width'][0]);
          Rooms[room_id]['background_image_height']=stringToNumber(room['background_image_height'][0]);
          Rooms[room_id]['password_protected']='1'==room['password_protected'][0];
          Rooms[room_id]['default_message_color']=room['default_message_color'][0];

          tr=categories_tbl.insertRow(-1);

          td=tr.insertCell(-1);
          if (true==ImgResizeSupported && Rooms[room_id]['background_image']>0) {
            td.innerHTML='<img id="room_'+htmlspecialchars(room_id)+'_image" src="'+htmlspecialchars(formlink)+'?s_id='+htmlspecialchars(s_id)+'&amp;b_id='+htmlspecialchars(Rooms[room_id]['background_image'])+'&amp;b_x=30&amp;b_y=40" alt="'+htmlspecialchars(getLng('background_image'))+'" title="'+htmlspecialchars(getLng('background_image'))+'" border="0" />';
            $('room_'+htmlspecialchars(room_id)+'_image').binaryfile_id=Rooms[room_id]['background_image'];
            $('room_'+htmlspecialchars(room_id)+'_image').ow_width=Rooms[room_id]['background_image_width'];
            $('room_'+htmlspecialchars(room_id)+'_image').ow_height=Rooms[room_id]['background_image_height'];
            $('room_'+htmlspecialchars(room_id)+'_image').style.cursor='pointer';
            $('room_'+htmlspecialchars(room_id)+'_image').onclick=function() {
              openWindow(formlink+'?ainc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
              return false;
            }
          } else {
            td.innerHTML='';
          }
          setCssClass(td, '.tbl_row');
          td.style.width='1%';
          td.style.textAlign='center';

          td=tr.insertCell(-1);
          td.innerHTML='<b>'+htmlspecialchars(room_name)+'</b>'
                      +'<br />'
                      +nl2br(htmlspecialchars(room_description));
          setCssClass(td, '.tbl_row');

          td=tr.insertCell(-1);
          if ('1'==room['password_protected'][0]) {
            td.innerHTML='<img src="./pic/room_locked_15x12.gif" title="'+htmlspecialchars(getLng('room_is_password_protected'))+'" alt="" />';
          } else {
            td.innerHTML='<img src="./pic/clearpixel_1x1.gif" alt="" width="15" height="1" />';
          }
          setCssClass(td, '.tbl_row');
          td.style.width='1%';
          td.style.textAlign='center';

          td=tr.insertCell(-1);
          td.innerHTML='<a class="tbl_row_link" href=":" title="'+htmlspecialchars(getLng('edit'))+'" onclick="showEditRoomForm('+htmlspecialchars(room_id)+'); return false;">'+htmlspecialchars(getLng('edit'))+'</a>';
          setCssClass(td, '.tbl_row');
          td.style.width='1%';
          td.style.textAlign='center';
          td.noWrap=true;

          td=tr.insertCell(-1);
          td.innerHTML='<a class="tbl_row_link" href=":" title="'+htmlspecialchars(getLng('delete'))+'" onclick="deleteRoom('+htmlspecialchars(room_id)+'); return false;">'+htmlspecialchars(getLng('delete'))+'</a>';
          setCssClass(td, '.tbl_row');
          td.style.width='1%';
          td.style.textAlign='center';
          td.noWrap=true;

          td=tr.insertCell(-1);
          td.innerHTML='<a class="tbl_row_link" href=":" title="'+htmlspecialchars(getLng('move_up'))+'" onclick="moveRoom('+htmlspecialchars(room_id)+', 0); return false;">'+htmlspecialchars(getLng('move_up'))+'</a>'
                      +'<br />'
                      +'<a class="tbl_row_link" href=":" title="'+htmlspecialchars(getLng('move_down'))+'" onclick="moveRoom('+htmlspecialchars(room_id)+', 1); return false;">'+htmlspecialchars(getLng('move_down'))+'</a>';
          setCssClass(td, '.tbl_row');
          td.style.width='1%';
          td.style.textAlign='center';
          td.noWrap=true;
        }
      }
      // "Create new room" button
      tr=categories_tbl.insertRow(-1);
      td=tr.insertCell(-1);
      td.innerHTML='<button type="button" title="'+htmlspecialchars(getLng('create_new_room_in_category').split('[CATEGORY]').join(category_name))+'" onclick="showCreateRoomForm('+htmlspecialchars(category_id)+'); return false;">'
                  +htmlspecialchars(getLng('create_new_room_in_category').split('[CATEGORY]').join(category_name))
                  +'</a>';
      td.colSpan=6;
      setCssClass(td, '.tbl_row');
    }
  }
  toggleProgressBar(false);
}


/**
 * Move category up or down
 * @param   int     id    Category ID
 * @param   int     dir   Direction (0: Up, 1: Down)
 */
function moveCategory(id, dir) {
  id=stringToNumber(id);
  if (id>0) {
    sendData('getCategoryTree()', formlink, 'POST', 'ajax=update_category&s_id='+urlencode(s_id)+'&category_id='+urlencode(id)+'&action=change_listpos&dir='+urlencode(dir));
  }
}


/**
 * Move room up or down (within a category)
 * @param   int     id    Room ID
 * @param   int     dir   Direction (0: Up, 1: Down)
 */
function moveRoom(id, dir) {
  id=stringToNumber(id);
  if (id>0) {
    sendData('getCategoryTree()', formlink, 'POST', 'ajax=update_room&s_id='+urlencode(s_id)+'&room_id='+urlencode(id)+'&action=change_listpos&dir='+urlencode(dir));
  }
}


/**
 * Display categories and rooms table
 */
function showCategories() {
  hideEditCategoryForm();
  hideEditRoomForm();
  hideCreateCategoryForm();
  hideCreateRoomForm();
  $('categories_tbl').style.display='';
}


/**
 * Hide categories and rooms table
 */
function hideCategories() {
  $('categories_tbl').style.display='none';
}


/**
 * Display "Edit category" form
 * @param   int   id    Category ID
 */
function showEditCategoryForm(id) {
  id=stringToNumber(id);
  if (id>0) {
    hideCategories();
    $('edit_category_tbl').style.display='';
    $('edit_category_tbl_header').innerHTML=htmlspecialchars(getLng('edit_category').split('[NAME]').join(Categories[id]['name']));

    $('edit_category_id').value=id;
    $('edit_category_name').value=Categories[id]['name'];
    $('edit_category_description').value=Categories[id]['description'];
    $('edit_category_creatable_rooms_'+Categories[id]['creatable_rooms_flag']).checked=true;
  }
}


/**
 * Hide "Edit category" form
 */
function hideEditCategoryForm() {
  $('edit_category_tbl').style.display='none';
}


/**
 * Display "Edit room" form
 * @param   int   id    Room ID
 */
function showEditRoomForm(id) {
  id=stringToNumber(id);
  if (id>0) {
    hideCategories();
    $('edit_room_tbl').style.display='';
    $('edit_room_tbl_header').innerHTML=htmlspecialchars(getLng('edit_room').split('[ROOM]').join(Rooms[id]['name']));

    $('edit_room_id').value=id;
    $('edit_room_name').value=Rooms[id]['name'];
    $('edit_room_description').value=Rooms[id]['description'];
    $('edit_room_category_id').options.length=0;
    for (var i in Categories) {
      $('edit_room_category_id').options[$('edit_room_category_id').options.length]=new Option(Categories[i]['name'], Categories[i]['id']);
      if (i==Rooms[id]['category_id']) {
        $('edit_room_category_id').selectedIndex=$('edit_room_category_id').options.length-1;
      }
    }
    if (Rooms[id]['background_image']>0) {
      if (true==ImgResizeSupported) {
        $('edit_room_background_image').innerHTML='<img src="'+htmlspecialchars(formlink)+'?s_id='+htmlspecialchars(s_id)+'&amp;b_id='+htmlspecialchars(Rooms[id]['background_image'])+'&amp;b_x=30&amp;b_y=40" alt="'+htmlspecialchars(getLng('background_image'))+'" title="'+htmlspecialchars(getLng('background_image'))+'" border="0" />';
        $('edit_room_background_image').title=getLng('background_image');
        $('edit_room_background_image').binaryfile_id=Rooms[id]['background_image'];
        $('edit_room_background_image').ow_width=Rooms[id]['background_image_width'];
        $('edit_room_background_image').ow_height=Rooms[id]['background_image_height'];
        $('edit_room_background_image').onclick=function() {
          openWindow(formlink+'?ainc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
          return false;
        }
      } else {
        $('edit_room_background_image').innerHTML=htmlspecialchars(htmlspecialchars(getLng('background_image')))+'<br />';
        $('edit_room_background_image').title=getLng('background_image');
        $('edit_room_background_image').binaryfile_id=Rooms[id]['background_image'];
        $('edit_room_background_image').ow_width=Rooms[id]['background_image_width'];
        $('edit_room_background_image').ow_height=Rooms[id]['background_image_height'];
        $('edit_room_background_image').onclick=function() {
          openWindow(formlink+'?ainc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
          return false;
        }
      }
      $('edit_room_delete_image_link').style.display='';
      $('edit_room_upload_image_link').style.display='none';
    } else {
      $('edit_room_background_image').innerHTML='';
      $('edit_room_delete_image_link').style.display='none';
      $('edit_room_upload_image_link').style.display='';
      $('edit_room_background_image').binaryfile_id='0';
    }
    $('edit_room_password_changed').value='0';
    $('edit_room_password_protected').value=Rooms[id]['password_protected']? '1' : '0';
    togglePasswordLink(Rooms[id]['password_protected']);
    togglePasswordFields(false);
    $('edit_room_default_message_color').value=Rooms[id]['default_message_color'];
    $('setting_color_edit_room_default_message_color').style.backgroundColor='#'+Rooms[id]['default_message_color'];
  }
}


/**
 * Hide "Edit room" form
 */
function hideEditRoomForm() {
  $('edit_room_tbl').style.display='none';
}


/**
 * Hide "Edit category" form
 */
function hideEditCategoryForm() {
  $('edit_category_tbl').style.display='none';
}


/**
 * Save category changes
 */
function updateCategory() {
  var category_id=$('edit_category_id').value;
  var category_name=$('edit_category_name').value=trimString($('edit_category_name').value);
  var category_description=$('edit_category_description').value=trimString($('edit_category_description').value);
  var creatable_rooms='n';
  var inputs=$$('INPUT', $('edit_category_form'));
  for (var i=0; i<inputs.length; i++) {
    if (inputs[i].name=='creatable_rooms' && inputs[i].checked) {
      creatable_rooms=inputs[i].value;
      break;
    }
  }
  // Validate form
  var errors=new Array();
  if (category_name=='') {
    errors.push(getLng('category_name_empty'));
  }

  if (errors.length) {
    alert(errors.join("\n"));
  } else {
    sendData('_CALLBACK_updateCategory()', formlink, 'POST', 'ajax=update_category'
                                                              +'&s_id='+urlencode(s_id)
                                                              +'&category_id='+urlencode(category_id)
                                                              +'&action=change_data'
                                                              +'&name='+urlencode(category_name)
                                                              +'&description='+urlencode(category_description)
                                                              +'&creatable_rooms='+urlencode(creatable_rooms)
                                                              );
  }
}
function _CALLBACK_updateCategory() {
//debug(actionHandler.getResponseString()); return false;
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    alert(actionHandler.message);
    if (actionHandler.status==0) {
      getCategoryTree();
    } else {
      // An error
      toggleProgressBar(false);
    }
  }
}


/**
 * Display "Upload room image" window
 */
function showRoomImageUploadForm() {
  setTimeout("openWindow(formlink+'?s_id='+s_id+'&inc=upload&f_target=room_image', 'file_upload', 400, 80, false, false, false, false, true);", 150);
}


/**
 * Parse response from "Upload room image" window
 * @param   int       code            Response code
 * @param   string    message         Response message
 * @param   int       binaryfile_id   Binaryfile ID
 * @param   int       width           If file was an image: width
 * @param   int       height          If file was an image: height
 * @param   string    filename        Filename
 */
function parseUploadResponse(code, message, binaryfile_id, width, height, filename) {
  if (typeof(code)!='undefined' && typeof(message)!='undefined') {
    switch (code) {

      case 0:
        // Success
        if (true==ImgResizeSupported) {
          $('edit_room_background_image').innerHTML='<img src="'+htmlspecialchars(formlink)+'?s_id='+htmlspecialchars(s_id)+'&amp;b_id='+htmlspecialchars(binaryfile_id)+'&amp;b_x=30&amp;b_y=40" alt="'+htmlspecialchars(filename)+'" title="'+htmlspecialchars(filename)+'" border="0" />';
          $('edit_room_background_image').title=filename;
          $('edit_room_background_image').binaryfile_id=binaryfile_id;
          $('edit_room_background_image').ow_width=width;
          $('edit_room_background_image').ow_height=height;
          $('edit_room_background_image').onclick=function() {
            openWindow(formlink+'?ainc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
            return false;
          }
          $('create_room_background_image').innerHTML='<img src="'+htmlspecialchars(formlink)+'?s_id='+htmlspecialchars(s_id)+'&amp;b_id='+htmlspecialchars(binaryfile_id)+'&amp;b_x=30&amp;b_y=40" alt="'+htmlspecialchars(filename)+'" title="'+htmlspecialchars(filename)+'" border="0" />';
          $('create_room_background_image').title=filename;
          $('create_room_background_image').binaryfile_id=binaryfile_id;
          $('create_room_background_image').ow_width=width;
          $('create_room_background_image').ow_height=height;
          $('create_room_background_image').onclick=function() {
            openWindow(formlink+'?ainc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
            return false;
          }
        } else {
          $('edit_room_background_image').innerHTML=htmlspecialchars(htmlspecialchars(filename))+'<br />';
          $('edit_room_background_image').title=filename;
          $('edit_room_background_image').binaryfile_id=binaryfile_id;
          $('edit_room_background_image').ow_width=width;
          $('edit_room_background_image').ow_height=height;
          $('edit_room_background_image').onclick=function() {
            openWindow(formlink+'?ainc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
            return false;
          }
          $('create_room_background_image').innerHTML=htmlspecialchars(htmlspecialchars(filename))+'<br />';
          $('create_room_background_image').title=filename;
          $('create_room_background_image').binaryfile_id=binaryfile_id;
          $('create_room_background_image').ow_width=width;
          $('create_room_background_image').ow_height=height;
          $('create_room_background_image').onclick=function() {
            openWindow(formlink+'?ainc=show_image&img_b_id='+this.binaryfile_id+'&s_id='+s_id, '', this.ow_width, this.ow_height, false, false, false, false, true);
            return false;
          }
        }
        $('edit_room_delete_image_link').style.display='';
        $('edit_room_upload_image_link').style.display='none';
        $('create_room_delete_image_link').style.display='';
        $('create_room_upload_image_link').style.display='none';
      break;

      case -1:
        // No file uploaded
        // do nothing ;)
      break;

      default:
        alert(actionHandler.message);
      break;

    }
  }
  // Reset window status resolution
}


/**
 * "Delete" uploaded image
 */
function deleteRoomImage() {
  $('edit_room_background_image').innerHTML='';
  $('edit_room_delete_image_link').style.display='none';
  $('edit_room_upload_image_link').style.display='';
  $('edit_room_background_image').binaryfile_id='';
  $('create_room_background_image').innerHTML='';
  $('create_room_delete_image_link').style.display='none';
  $('create_room_upload_image_link').style.display='';
  $('create_room_background_image').binaryfile_id='';
}


/**
 * Show/hide "Change room password" link
 * @param   boolean   state   If TRUE: display, if FALSE: hide
 */
function togglePasswordLink(state) {
  if (state==false) {
    $('edit_room_password_link').style.display='none';
  } else {
    $('edit_room_password_link').style.display='';
  }
}


/**
 * Show/hide room password fields (Edit room form)
 * @param   boolean   state   If TRUE: display, if FALSE: hide
 */
function togglePasswordFields(state) {
  if (state==false) {
    $('edit_room_password_fields').style.display='none';
  } else {
    $('edit_room_password_changed').value='1';
    $('edit_room_password_fields').style.display='';
    $('edit_room_password_1').value='';
    $('edit_room_password_2').value='';
  }
}


/**
 * Show/hide room password fields (Create new room form)
 * @param   boolean   state   If TRUE: display, if FALSE: hide
 */
function toggleNewRoomPasswordFields(state) {
  if (state==false) {
    $('create_room_password_fields').style.display='none';
  } else {
    $('create_room_password_fields').style.display='';
    $('create_room_password_1').value='';
    $('create_room_password_2').value='';
  }
}


/**
 * Save room changes
 */
function updateRoom() {
  var room_id=$('edit_room_id').value;
  var errors=new Array();
  $('edit_room_name').value=trimString($('edit_room_name').value);
  $('edit_room_description').value=trimString($('edit_room_description').value);
  if ($('edit_room_name').value=='') {
    // Room name empty
    errors.push(getLng('room_name_empty'));
  }
  if ($('edit_room_password_protected').value=='1' && $('edit_room_password_changed').value=='1') {
    if ($('edit_room_password_1').value!=$('edit_room_password_2').value) {
      // Passwords are not ident
      errors.push(getLng('passwords_not_ident'));
    } else if ($('edit_room_password_1').value.length<3) {
      // Password too short
      errors.push(getLng('password_too_short'));
    }
  } else {
    $('edit_room_password_1').value='';
    $('edit_room_password_2').value='';
  }
  if (errors.length>0) {
    alert(errors.join("\n"));
  } else {
    sendData('_CALLBACK_updateRoom()', formlink, 'POST', 'ajax=update_room'
                                                        +'&s_id='+urlencode(s_id)
                                                        +'&room_id='+urlencode(room_id)
                                                        +'&action=change_data'
                                                        +'&name='+urlencode($('edit_room_name').value)
                                                        +'&category_id='+urlencode($('edit_room_category_id').value)
                                                        +'&description='+urlencode($('edit_room_description').value)
                                                        +'&password_protect='+urlencode($('edit_room_password_protected').value)
                                                        +'&change_password='+urlencode($('edit_room_password_changed').value)
                                                        +'&password='+urlencode(base64encode($('edit_room_password_1').value))
                                                        +'&image='+urlencode($('edit_room_background_image').binaryfile_id)
                                                        +'&default_message_color='+urlencode($('edit_room_default_message_color').value)
                                                        );
  }
}
function _CALLBACK_updateRoom() {
//debug(actionHandler.getResponseString()); return false;
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    alert(actionHandler.message);
    if (actionHandler.status==0) {
      getCategoryTree();
    } else {
      // An error
      toggleProgressBar(false);
    }
  }
}


/**
 * Delete category
 * @param   int       id          Category ID
 * @param   boolean   confirmed   Optional. If TRUE: no confirmation will be displayed. Default: FALSE.
 */
function deleteCategory(id, confirmed) {
  id=stringToNumber(id);
  if (id>0) {
    if (typeof(confirmed)!='boolean' || !confirmed) {
      confirm(getLng('confirm_delete_category').split('[NAME]').join(Categories[id]['name']), 0, 0, 'deleteCategory('+id+', true)');
    } else {
      sendData('_CALLBACK_deleteCategory()', formlink, 'POST', 'ajax=delete_category&s_id='+urlencode(s_id)+'&category_id='+urlencode(id));
    }
  }
}
function _CALLBACK_deleteCategory() {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    if (actionHandler.status==0) {
      alert(actionHandler.message, 0, 0, 'getCategoryTree()');
    } else {
      // An error
      alert(actionHandler.message);
    }
  }
}


/**
 * Delete room
 * @param   int       id          Room ID
 * @param   boolean   confirmed   Optional. If TRUE: no confirmation will be displayed. Default: FALSE.
 */
function deleteRoom(id, confirmed) {
  id=stringToNumber(id);
  if (id>0) {
    if (typeof(confirmed)!='boolean' || !confirmed) {
      confirm(getLng('confirm_delete_room').split('[NAME]').join(Rooms[id]['name']), 0, 0, 'deleteRoom('+id+', true)');
    } else {
      sendData('_CALLBACK_deleteRoom()', formlink, 'POST', 'ajax=delete_room&s_id='+urlencode(s_id)+'&room_id='+urlencode(id));
    }
  }
}
function _CALLBACK_deleteRoom() {
//debug(actionHandler.getResponseString()); return false;
  toggleProgressBar(false);
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    if (actionHandler.status==0) {
      alert(actionHandler.message, 0, 0, 'getCategoryTree()');
    } else {
      // An error
      alert(actionHandler.message);
    }
  }
}


/**
 * Display "Create new category" form
 */
function showCreateCategoryForm() {
  hideCategories();
  $('create_category_tbl').style.display='';
  $('create_category_name').value='';
  $('create_category_description').value='';
  $('create_category_creatable_rooms_n').click();
  $('create_category_name').focus();
}


/**
 * Hide "Create new category" form
 */
function hideCreateCategoryForm() {
  $('create_category_tbl').style.display='none';
}


/**
 * Create new category
 */
function createCategory() {
  $('create_category_name').value=trimString($('create_category_name').value);
  $('create_category_description').value=trimString($('create_category_description').value);
  var creatable_rooms='n';
  var inputs=$$('INPUT', $('create_category_form'));
  for (var i=0; i<inputs.length; i++) {
    if (inputs[i].name=='creatable_rooms' && inputs[i].checked) {
      creatable_rooms=inputs[i].value;
      break;
    }
  }
  // Validate form
  var errors=new Array();
  if ($('create_category_name').value=='') {
    errors.push(getLng('category_name_empty'));
  }

  if (errors.length) {
    alert(errors.join("\n"));
  } else {
    sendData('_CALLBACK_createCategory()', formlink, 'POST', 'ajax=create_category'
                                                              +'&s_id='+urlencode(s_id)
                                                              +'&name='+urlencode($('create_category_name').value)
                                                              +'&description='+urlencode($('create_category_description').value)
                                                              +'&creatable_rooms='+urlencode(creatable_rooms)
                                                              );
  }
}
function _CALLBACK_createCategory() {
//debug(actionHandler.getResponseString()); return false;
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    alert(actionHandler.message);
    if (actionHandler.status==0) {
      getCategoryTree();
    } else {
      // An error
      toggleProgressBar(false);
    }
  }
}


/**
 * Display "Create new room" form
 * @param   int   category_id     Parent category ID
 */
function showCreateRoomForm(category_id) {
  category_id=stringToNumber(category_id);
  if (category_id>0) {
    hideCategories();
    $('create_room_tbl').style.display='';
    $('create_room_tbl_header').innerHTML=htmlspecialchars(getLng('create_new_room_in_category').split('[CATEGORY]').join(Categories[category_id]['name']));

    $('create_room_category_id').value=category_id;
    $('create_room_name').value='';
    $('create_room_name').focus();
    $('create_room_description').value='';

    $('create_room_background_image').innerHTML='';
    $('create_room_delete_image_link').style.display='none';
    $('create_room_upload_image_link').style.display='';
    $('create_room_background_image').binaryfile_id='0';

    $('create_room_password_protected').value='0';

    $('create_room_default_message_color').value=$('create_room_default_message_color_global').value;
    $('setting_color_create_room_default_message_color').style.backgroundColor='#'+$('create_room_default_message_color_global').value;

    toggleNewRoomPasswordFields(false);
  }
}


/**
 * Hide "Create new room" form
 */
function hideCreateRoomForm() {
  $('create_room_tbl').style.display='none';
}


/**
 * Create new room
 */
function createRoom() {
  var errors=new Array();
  $('create_room_name').value=trimString($('create_room_name').value);
  $('create_room_description').value=trimString($('create_room_description').value);
  if ($('create_room_name').value=='') {
    // Room name empty
    errors.push(getLng('room_name_empty'));
  }
  if ($('create_room_password_protected').value=='1') {
    if ($('create_room_password_1').value!=$('create_room_password_2').value) {
      // Passwords are not ident
      errors.push(getLng('passwords_not_ident'));
    } else if ($('create_room_password_1').value.length<3) {
      // Password too short
      errors.push(getLng('password_too_short'));
    }
  } else {
    $('create_room_password_1').value='';
    $('create_room_password_2').value='';
  }
  if (errors.length>0) {
    alert(errors.join("\n"));
  } else {
    sendData('_CALLBACK_createRoom()', formlink, 'POST', 'ajax=create_room'
                                                        +'&s_id='+urlencode(s_id)
                                                        +'&name='+urlencode($('create_room_name').value)
                                                        +'&category_id='+urlencode($('create_room_category_id').value)
                                                        +'&description='+urlencode($('create_room_description').value)
                                                        +'&password_protect='+urlencode($('create_room_password_protected').value)
                                                        +'&password='+urlencode(base64encode($('create_room_password_1').value))
                                                        +'&image='+($('create_room_background_image').binaryfile_id!=''? '1' : '0')
                                                        +'&default_message_color='+urlencode($('create_room_default_message_color').value)
                                                        );
  }
}
function _CALLBACK_createRoom() {
//debug(actionHandler.getResponseString()); return false;
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  } else {
    alert(actionHandler.message);
    if (actionHandler.status==0) {
      getCategoryTree();
    } else {
      // An error
      toggleProgressBar(false);
    }
  }
}
