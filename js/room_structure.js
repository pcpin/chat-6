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
 * Categories as an Array with first element is tree structure
 * and other elements as references to categories in tree structure.
 * @var object
 */
var CategoryTree=Array();

/**
 * Previous state of CategoryTree array
 * @var object
 */
var CategoryTree_previous=Array();

/**
 * Categories indexed by category ID
 * @var object
 */
var CategoryTreeByID=Array();

/**
 * Previous state of CategoryTreeByID array
 * @var object
 */
var CategoryTreeByID_previous=Array();

/**
 * Currently active category ID
 * @var int
 */
var ActiveCategoryId=0;

/**
 * Previously active ActiveCategoryId
 * @var int
 */
var ActiveCategoryId_previous=0;

/**
 * Currently active room ID
 * @var int
 */
var ActiveRoomId=0;

/**
 * Previously active room ID
 * @var int
 */
var ActiveRoomId_previous=0;

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
 * Empty room structure
 */
function resetRoomStructure() {
  CategoryTree_previous=CategoryTree;
  CategoryTreeByID_previous=CategoryTreeByID;
  CategoryTree=Array();
  CategoryTreeByID=Array();
  setActiveCategoryId(0);
  setActiveRoomId(0);
  setCssClass($('chat_rooms_list'), '.div_selection_scrollable');
  setCssClass($('chat_categories_list'), '.div_selection_scrollable');
  UserList.initialize();
}


/**
 * Get chat rooms list grouped in categories and sorted by name, including list of users in each room.
 * @param   string    callback2       Optional callback function to be executed after _CALLBACK_getRoomStructure()
 * @param   boolean   async           Optional. If FALSE (default): request will be executed in synchronous mode
 * @param   boolean   showProgressBar Optional. If FALSE (default): progress bar will be not displayed
 */
function getRoomStructure(callback2, async, showProgressBar) {
  resetRoomStructure();
  if (typeof(callback2)!=='string') {
    callback2='';
  }
  sendData('_CALLBACK_getRoomStructure(\''+callback2+'\')', formlink, 'POST', 'ajax=get_room_structure&s_id='+urlencode(s_id), typeof(showProgressBar)!='boolean' || showProgressBar, typeof(async)!='boolean' || !async);
}
function _CALLBACK_getRoomStructure(callback2) {
//debug(actionHandler.getResponseString()); return false;
  var cats=null;
  var additional_data=null;
  switch (actionHandler.status) {

    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;

    case 0:
      // Success
      // Get categories
      if (actionHandler.data['category'].length) {
        // Make category tree
        makeCategoryTree(actionHandler.data['category']);
        // Display category tree
        if (typeof(roomSelectionDisplayType)=='string' && roomSelectionDisplayType=='s') {
          displaySimpleCategoryTree();
        } else {
          displayCategoryTree();
        }
        if (ActiveCategoryId>0) {
          showCategoryRooms(ActiveCategoryId);
        }
      }
      // Get additional data
      // Are there new invitations?
      if ('1'==actionHandler.data['additional_data'][0]['new_invitations'][0]) {
        if (typeof(getNewInvitations)!='undefined') {
          getNewInvitations();
        }
      }
      // Are there new messages?
      if ('1'==actionHandler.data['additional_data'][0]['new_messages'][0]) {
        if (typeof(getNewMessages)!='undefined' && getNewMessages) {
          getNewMessages();
        }
      }

    break;

  }
  toggleProgressBar(false);
  if (callback2!='') {
    eval('try { '+callback2+' } catch (e) { }');
  }
}


/**
 * Create category tree from Categories array
 * @param   object    cats        Categories array as returned by AJAX interface
 * @return  array
 */
function makeCategoryTree(cats) {
  var cat=null;
  var cat_id=0;
  var cat_parent_id=0;
  var room=null;
  var room_nr=0;
  var room_id=0;
  var user_nr=0;
  var user=null;
  var user_id=0;
  var curr_cat=null;
  if (cats) {
    for (var cat_nr=0; cat_nr<cats.length; cat_nr++) {
      cat=cats[cat_nr];
      cat_id=stringToNumber(cat['id'][0]);
      cat_parent_id=stringToNumber(cat['parent_id'][0]);
      if (ActiveCategoryId_previous==cat_id) {
        setActiveCategoryId(cat_id);
      }
      // Add category to the global array
      CategoryTree.push(
                          {
                            id: cat_id,
                            parent_id: cat_parent_id,
                            child_ids: Array(),
                            name: cat['name'][0],
                            description: cat['description'][0],
                            creatable_rooms: cat['creatable_rooms'][0]=='1',
                            children: Array(),
                            children_by_id: Array(),
                            rooms: Array(),
                            rooms_by_id: Array(),
                            opened: typeof(CategoryTreeByID_previous[cat_id])!='undefined' && CategoryTreeByID_previous[cat_id]['opened'],
                            rooms_local: 0,
                            rooms_total: 0,
                            users_total: 0
                          }
                        );
      curr_cat=CategoryTree[CategoryTree.length-1];
      CategoryTreeByID[cat_id]=curr_cat;
      if (cat_parent_id!=-1) {
        // Make a reference
        CategoryTreeByID[cat_parent_id]['children'].push(curr_cat);
        CategoryTreeByID[cat_parent_id]['children_by_id'][cat_id]=CategoryTreeByID[cat_parent_id]['children'][CategoryTreeByID[cat_parent_id]['children'].length-1];
      }
      // Get child categories
      if (cat['category'].length) {
        makeCategoryTree(cat['category']);
      }
      // Get rooms
      for (room_nr=0; room_nr<cat['room'].length; room_nr++) {
        room=cat['room'][room_nr];
        room_id=stringToNumber(room['id'][0]);
        if (ActiveRoomId_previous==room_id) {
          setActiveRoomId(room_id);
        }
        curr_cat['rooms'].push(
                                {
                                  id: room_id,
                                  password_protected: '0'!=room['password_protected'][0],
                                  name: room['name'][0],
                                  description: room['description'][0],
                                  opened: typeof(CategoryTree_previous[cat_id])!='undefined' && typeof(CategoryTreeByID_previous[cat_id]['rooms_by_id'][room_id])!='undefined' && CategoryTreeByID_previous[cat_id]['rooms_by_id'][room_id]['opened'],
                                  moderated_by_me: '1'==room['moderated_by_me'][0],
                                  users: Array(),
                                  users_by_id: Array(),
                                  users_total: 0
                                }
                               );
        curr_cat['rooms_by_id'][room_id]=curr_cat['rooms'][curr_cat['rooms'].length-1];
        curr_cat['rooms_local']++;
        curr_cat['rooms_total']++;
        // Get users
        for (user_nr=0; user_nr<room['user'].length; user_nr++) {
          user=room['user'][user_nr];
          user_id=stringToNumber(user['id'][0]);
          curr_cat['rooms_by_id'][room_id]['users'].push(
                                                         {
                                                           user_id: user_id,
                                                           nickname: user['nickname'][0],
                                                           nickname_plain: user['nickname_plain'][0]
                                                         }
                                                        );
          curr_cat['rooms_by_id'][room_id]['users_by_id'][user_id]=curr_cat['rooms_by_id'][room_id]['users'][curr_cat['rooms_by_id'][room_id]['users'].length-1];
          curr_cat['rooms_by_id'][room_id]['users_total']++;
          curr_cat['users_total']++;
          UserList.addRecord(user_id,
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
                             '1'==user['is_moderator'][0]
                             );
        }
      }
      // Save child categories' IDs and rooms/users counters
      for (var i=0; i<curr_cat['children'].length; i++) {
        curr_cat['rooms_total']+=curr_cat['children'][i]['rooms_total'];
        curr_cat['users_total']+=curr_cat['children'][i]['users_total'];
        if (curr_cat['parent_id']!=-1) {
          CategoryTreeByID[curr_cat['parent_id']]['child_ids'].push(curr_cat['children'][i]['id']);
          curr_cat['parent_id']['rooms_total']+=curr_cat['rooms_total'];
          curr_cat['parent_id']['users_total']+=curr_cat['users_total'];
        }
      }
    }
  }
}


/**
 * Display category tree
 * @param   array     cats          Array with category tree
 * @param   int       depth         Current depth
 */
function displayCategoryTree(cats, depth) {
  try {
    var div_top=$('chat_categories_list').scrollTop;
    $('rooms_tree').style.display='';
    $('rooms_simplified').style.display='none';
    $('simplified_view_link').style.display='';
    $('advanced_view_link').style.display='none';
    $('join_room_tbl').style.width='100%';
    if (typeof(cats)!='object' || cats==null) {
      cats=CategoryTree[0]['children'];
    }
    if (typeof(depth)=='undefined') {
      depth=0;
    }
    $('chat_categories_list').innerHTML=makeCategoryTreeHtml(cats, depth);
    $('chat_categories_list').scrollTop=div_top;
    setTimeout("$('chat_categories_list').scrollTop="+div_top+";", 1);
  } catch (e) {}
}


/**
 * Create HTML for category tree
 * @param   array     cats          Array with category tree
 * @param   int       depth         Current depth
 * @return  string
 */
function makeCategoryTreeHtml(cats, depth) {
  var html='';
  if (cats.length) {
    if (depth==0) {
      html='<b>'+htmlspecialchars(getLng('chat_categories'))+'</b><br />';
    }
    for (var i=0; i<cats.length; i++) {
      if (depth>0) {
        html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="'+(depth*15)+'" height="1" />';
      }
      if (cats[i]['children'].length) {
        if (cats[i]['opened']) {
          html+='<a href="#" title="'+htmlspecialchars(getLng('chat_category')+': '+cats[i]['name']+"\n "+cats[i]['description'])+'" onclick="setActiveRoomId(0); showStealthSwitch(false); closeCategoryFolder('+cats[i]['id']+'); showCategoryRooms(); return false;">'
              + '<img src="./pic/minus_box_15x12.gif" border="0" />'
              + '</a>';
        } else {
          html+='<a href="#" title="'+htmlspecialchars(getLng('chat_category')+': '+cats[i]['name']+"\n "+cats[i]['description'])+'" onclick="setActiveRoomId(0); showStealthSwitch(false); openCategoryFolder('+cats[i]['id']+'); return false;">'
              + '<img src="./pic/plus_box_15x12.gif" border="0" />'
              + '</a>';
        }
      } else {
        html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="15" height="1" />';
      }
      html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="3" height="1" />'
          + '<a title="'+htmlspecialchars(getLng('chat_category')+': '+cats[i]['name']+' ['+cats[i]['rooms_total']+' '+getLng('rooms')+' / '+cats[i]['users_total']+' '+getLng('users')+']'+"\n "+cats[i]['description'])+'" class="div_selection_scrollable_link" href="#" onclick="setActiveRoomId(0); showStealthSwitch(false); setActiveCategoryId('+cats[i]['id']+'); openCategoryFolder('+cats[i]['id']+'); showCategoryRooms('+cats[i]['id']+'); return false;">'
          + '<img src="./pic/'+(cats[i]['id']==ActiveCategoryId? 'folder_opened_15x12.gif' : 'folder_closed_15x12.gif')+'" border="0" />'
          + '<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="12" />'
          + '<span class="'+(cats[i]['id']==ActiveCategoryId? 'div_selection_scrollable_active' : 'div_selection_scrollable_inactive')+'">'
          + htmlspecialchars(cats[i]['name'])
          + ' ['+cats[i]['rooms_total']+'/'+cats[i]['users_total']+']'
          + '</span>'
          + '</a>'
          + '<br />';
      if (cats[i]['children'].length && cats[i]['opened']) {
        html+=makeCategoryTreeHtml(cats[i]['children'], ++depth);
        depth--;
      }
    }
  }
  return html;
}


/**
 * Open category folder
 * @param   int     cat_id    Category ID
 */
function openCategoryFolder(cat_id) {
  try {
    CategoryTree[cat_id]['opened']=true;
  } catch (e) {}
  displayCategoryTree();
}


/**
 * Close category folder
 * @param   int     cat_id    Category ID
 */
function closeCategoryFolder(cat_id) {
  try {
    CategoryTree[cat_id]['opened']=false;
    for (var i=0; i<CategoryTree[cat_id]['child_ids'].length; i++) {
      if (CategoryTree[cat_id]['child_ids'][i]==ActiveCategoryId) {
        setActiveCategoryId(cat_id);
        break;
      }
    }
  } catch (e) {}
  displayCategoryTree();
}


/**
 * make HTML for all rooms and subcategories from active category
 * @param   int       category_id     Category ID
 */
function showCategoryRooms(category_id) {
  try {
    var chat_rooms_list=$('chat_rooms_list');
    var div_top=chat_rooms_list.scrollTop;
    if (typeof(category_id)!='number') {
      category_id=ActiveCategoryId;
    }
    if (category_id>0) {
      var rooms_div_height=stringToNumber(chat_rooms_list.style.height.substring(0, chat_rooms_list.style.height.length-2));
      chat_rooms_list.innerHTML=makeCategoryRoomsHTML(category_id);
      chat_rooms_list.scrollTop=div_top;
      setTimeout("$('chat_rooms_list').scrollTop="+div_top+";", 1);
      chat_rooms_list.style.height=(rooms_div_height-1)+'px';
      setTimeout('$(\'chat_rooms_list\').style.height=\''+rooms_div_height+'px\';', 20);
    }
  } catch (e) {}
}

/**
 * Display all rooms and subcategories from active category
 * @param   int       category_id     Category ID
 * @return  string
 */
function makeCategoryRoomsHTML(category_id) {
  var rooms_div=$('chat_rooms_list');
  var html='';
  var room_pic='';
  var room_title='';
  var urec_tpl=$('userlist_record_tpl').innerHTML;
  var urec='';
  var usr=null;
  var ignored_img_suffix='';
  var status_img='';
  var status_title='';
  var cat=null;
  var child_cat=null;
  var room=null;

  if (category_id>0) {
    cat=CategoryTreeByID[category_id];
    html='<b>'+getLng('chat_category')+' &quot;'+htmlspecialchars(cat['name'])+'&quot;</b><br />';
    if (cat['description']!='') {
      html+=nl2br(htmlspecialchars(cat['description']))+'<br />';
    }
    if (cat['children'].length) {
      // Display subcategories
      html+='<br /><u>'+getLng('subcategories')+':</u><br />';
      for (var ii=0; ii<cat['children'].length; ii++) {
        child_cat=cat['children'][ii];
        html+='<a onclick="setActiveCategoryId('+child_cat['id']+'); openCategoryFolder('+child_cat['id']+'); showCategoryRooms(); return false;" title="'+htmlspecialchars(getLng('chat_category')+': '+child_cat['name']+' ['+cat['rooms_total']+' '+getLng('rooms')+' / '+cat['users_total']+' '+getLng('users')+']'+"\n "+child_cat['description'])+'" href="#" class="div_selection_scrollable_link">'
            + '<img src="./pic/folder_closed_15x12.gif" border="0" />'
            + '<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="1" />'
            + '<span class="div_selection_scrollable_inactive">'
            + htmlspecialchars(child_cat['name'])
            + htmlspecialchars(' ['+child_cat['rooms_total']+'/'+child_cat['users_total']+']')
            + '</span>'
            + '</a>'
            + '<br />';
      }
    }
    html+='<hr width="100%" />';
    // Display rooms
    if (cat['rooms'].length) {
      html+='<u>'+getLng('chat_rooms')+':</u><br />';
      for (var ii=0; ii<cat['rooms'].length; ii++) {
        room=cat['rooms'][ii];
        if (room['users'].length) {
          // There are users in room
          if (room['opened']) {
            html+='<a href="#" title="'+htmlspecialchars(getLng('hide_online_users'))+'" onclick="openCloseRoom('+room['id']+', '+category_id+', false); showCategoryRooms('+category_id+'); return false;">'
                + '<img src="./pic/minus_box_15x12.gif" border="0" />'
                + '</a>'
                ;
          } else {
            html+='<a href="#" title="'+htmlspecialchars(getLng('show_online_users'))+'" onclick="openCloseRoom('+room['id']+', '+category_id+', true); showCategoryRooms('+category_id+'); return false;">'
                + '<img src="./pic/plus_box_15x12.gif" border="0" />'
                + '</a>';
          }
        } else {
          // There are no users in room
          html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="15" height="1" />';
        }
        room_title=getLng('chat_room')+': '+room['name']+' ['+room['users_total']+' '+getLng('users')+']'+"\n "+room['description'];
        if (room['password_protected']) {
          room_pic='room_locked_15x12.gif';
          room_title+="\n *"+getLng('room_is_password_protected');
        } else {
          room_pic='members_15x15.gif';
        }
        html+='<a onclick="setActiveRoomId('+room['id']+'); openCloseRoom('+room['id']+', '+category_id+', true); showCategoryRooms('+category_id+'); showStealthSwitch('+(room['moderated_by_me']? 'true' : 'false')+'); $(\'enterChatRoom_btn\').focus(); return false;" href="#" title="'+htmlspecialchars(room_title)+'" class="div_selection_scrollable_link">'
            + '<img src="./pic/'+room_pic+'" border="0" alt="" />'
            + '</a>'
            + '<img src="./pic/clearpixel_1x1.gif" border="0" width="8" height="12" />'
            + '<a onclick="setActiveRoomId('+room['id']+'); enterChatRoom(); return false;" style="cursor:pointer" title="'+htmlspecialchars(room_title)+'">'
            + '<span class="'+(room['id']==ActiveRoomId? 'div_selection_scrollable_active' : 'div_selection_scrollable_inactive')+'">'
            + htmlspecialchars(room['name'])
            + htmlspecialchars(' ['+room['users_total']+']')
            + '</span>'
            + '</a>'
            + '<br />';
        if (room['opened']) {
          // Show users
          if (room['users_total']>0) {
            for (var iii=0; iii<room['users'].length; iii++) {
              usr=UserList.getRecord(room['users'][iii]['user_id']);
              urec=urec_tpl;
              urec=urec.split('[ID]').join(usr.ID);
              // Online status
              if (true==usr.MutedLocally) {
                ignored_img_suffix='ignored_';
              } else {
                ignored_img_suffix='';
              }
              if (true==usr.GlobalMuted) {
                status_img='./pic/online_status_muted_'+ignored_img_suffix+'10x10.gif';
                if (usr.GlobalMutedUntil==0) {
                  status_title=getLng('permanently_globalmuted')+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
                } else {
                  status_title=getLng('globalmuted_until').split('[EXPIRATION_DATE]').join(date(dateFormat, usr.GlobalMutedUntil))+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
                }
              } else {
                status_img='./pic/online_status_'+usr.OnlineStatus+'_'+ignored_img_suffix+'10x10.gif';
                status_title=usr.OnlineStatusMessage+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
              }
              status_title=htmlspecialchars(status_title);
              urec=urec.split('[ONLINE_STATUS_ICON]').join('<img id="user_status_image_'+usr.ID+'" src="'+status_img+'" alt="'+status_title+'" title="'+status_title+'" />');
              // Gender
              if (userlistGender) {
                urec=urec.split('[GENDER_ICON]').join('<img src="./pic/gender_'+usr.Gender+'_10x10.gif" alt="'+htmlspecialchars(getLng('gender')+': '+getLng('gender_'+usr.Gender))+'" title="'+htmlspecialchars(getLng('gender')+': '+getLng('gender_'+usr.Gender))+'" border="0" />');
              } else {
                urec=urec.split('[GENDER_ICON]').join('');
              }
              // Avatar
              if (userlistAvatar) {
                if (usr.AvatarBID>0) {
                  urec=urec.split('[AVATAR_THUMB]').join('<img style="cursor:pointer" onclick="showUserProfile('+usr.ID+')" src="'+htmlspecialchars(formlink)+'?b_x='+htmlspecialchars(userlistAvatarHeight)+'&amp;b_y='+htmlspecialchars(userlistAvatarWidth)+'&amp;b_id='+htmlspecialchars(usr.AvatarBID)+'&amp;s_id='+htmlspecialchars(s_id)+'" onmouseover="showUserlistAvatarThumb(this, '+htmlspecialchars(usr.AvatarBID)+')" onmouseout="hideUserlistAvatarThumb()" onclick="hideUserlistAvatarThumb()" alt="'+htmlspecialchars(getLng('avatar'))+'" title="'+htmlspecialchars(getLng('avatar'))+'" border="0" />');
                } else {
                  urec=urec.split('[AVATAR_THUMB]').join('<img src="./pic/clearpixel_1x1.gif" width="'+htmlspecialchars(userlistAvatarWidth)+'" height="'+htmlspecialchars(userlistAvatarHeight)+'" alt="" title="" border="0" />');
                }
              } else {
                urec=urec.split('[AVATAR_THUMB]').join('');
              }
              // Nickname
              urec=urec.split('[NICKNAME_PLAIN]').join('"'+coloredToPlain(usr.Nickname, true)+'"');
              urec=urec.split('[NICKNAME_COLORED]').join(coloredToHTML(usr.Nickname));
              // Admin
              if (userlistPrivileged && usr.IsAdmin) {
                urec=urec.split('_admin_section').join(' onclick="alert(getLng(\'user_is_admin\').split(\'[USER]\').join(\''+htmlspecialchars(coloredToPlain(usr.Nickname, false))+'\')); return false;" ');
              } else {
                urec=urec.split('_admin_section').join(' style="display:none" ');
              }
              // Moderator
              if (userlistPrivileged && usr.IsModerator) {
                urec=urec.split('_moderator_section').join(' onclick="alert(getLng(\'user_is_moderator\').split(\'[USER]\').join(\''+htmlspecialchars(coloredToPlain(usr.Nickname, false))+'\')); return false;" ');
              } else {
                urec=urec.split('_moderator_section').join(' style="display:none" ');
              }
              html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="25" height="1" />'
                   +urec
                   +'<br />';
            }
          } else {
            room['opened']=false;
          }
        }
      }
    } else {
      html+=getLng('category_has_no_rooms');
    }
    if (cat['creatable_rooms']) {
      html+='<br />'
           +'<a href="#" onclick="showNewRoomBox('+category_id+')" title="'+htmlspecialchars(getLng('create_new_room'))+'">'
           +'<img src="./pic/plus_13x13.gif" name="img_hover" alt="'+htmlspecialchars(getLng('create_new_room'))+'" title="'+htmlspecialchars(getLng('create_new_room'))+'" />'
           +'<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="1" />'
           +htmlspecialchars(getLng('create_new_room'))
           +'</a>';
    }
  }
  return html;
}

/**
 * Open/close a room
 * @param   int       room_id       Room ID
 * @param   int       category_id   ID of the category where the room located in
 * @param   boolean   open          If TRUE, then room will be opened, if FALSE - closed
 */
function openCloseRoom(room_id, category_id, open) {
  try {
    CategoryTreeByID[category_id]['rooms_by_id'][room_id]['opened']=open;
  } catch (e) {}
}


/**
 * Display simplified category tree HTML
 * @param   array     cats          Array with category tree
 */
function displaySimpleCategoryTree(cats) {
  if (typeof(CategoryTree[0])=='undefined' || typeof(CategoryTree[0]['children'])=='undefined') {
    return false;
  }
  var div_top=$('chat_rooms_list_simplified').scrollTop;
  $('rooms_tree').style.display='none';
  $('rooms_simplified').style.display='';
  $('simplified_view_link').style.display='none';
  $('advanced_view_link').style.display='';
  $('join_room_tbl').style.width='50%';
  if (typeof(cats)!='object' || cats==null) {
    cats=CategoryTree[0]['children'];
  }
  $('chat_rooms_list_simplified').innerHTML=makeSimpleCategoryTreeHtml(cats);
  $('chat_rooms_list_simplified').scrollTop=div_top;
  setTimeout("$('chat_rooms_list_simplified').scrollTop="+div_top+";", 1);
}

/**
 * Create simplified category tree HTML
 * @param   array     cats          Array with category tree
 * @return  string
 */
function makeSimpleCategoryTreeHtml(cats) {
  var urec_tpl=$('userlist_record_tpl').innerHTML;
  var urec='';
  var usr=null;
  var ignored_img_suffix='';
  var status_img='';
  var status_title='';
  var html='';
  if (cats.length) {
    for (var i=0; i<cats.length; i++) {
      if (cats[i]['rooms_local']>0 || cats[i]['creatable_rooms']) {
        html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="3" height="1" />'
            + '<span title="'+htmlspecialchars(getLng('chat_category')+': '+cats[i]['name']+' ['+cats[i]['rooms_local']+' '+getLng('rooms')+' / '+cats[i]['users_total']+' '+getLng('users')+']'+"\n "+cats[i]['description'])+'" class="div_selection_scrollable_link" style="cursor:default">'
            + '<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="12" />'
            + '<b>'+htmlspecialchars(cats[i]['name'])+'</b>'
            + ' ['+cats[i]['rooms_total']+'/'+cats[i]['users_total']+']'
            + '</span>';
        if (cats[i]['creatable_rooms']) {
          html+='<br />'
               +'<img src="./pic/clearpixel_1x1.gif" border="0" width="10" height="1" />'
               +'<a href="#" onclick="showNewRoomBox('+cats[i]['id']+')" title="'+htmlspecialchars(getLng('create_new_room'))+'">'
               +'<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="1" />'
               +htmlspecialchars(getLng('create_new_room'))
               +'</a>';
        }
        html+='<br />';
        // Rooms
        for (var ii=0; ii<cats[i]['rooms'].length; ii++) {
          if (cats[i]['rooms'][ii]['users'].length) {
            // There are users in room
            if (cats[i]['rooms'][ii]['opened']) {
              html+='<a href="#" title="'+htmlspecialchars(getLng('hide_online_users'))+'" onclick="openCloseRoom('+cats[i]['rooms'][ii]['id']+', '+cats[i]['id']+', false); displaySimpleCategoryTree(); return false;">'
                  + '<img src="./pic/minus_box_15x12.gif" border="0" />'
                  + '</a>';
            } else {
              html+='<a href="#" title="'+htmlspecialchars(getLng('show_online_users'))+'" onclick="openCloseRoom('+cats[i]['rooms'][ii]['id']+', '+cats[i]['id']+', true); displaySimpleCategoryTree('+cats[i]['id']+'); return false;">'
                  + '<img src="./pic/plus_box_15x12.gif" border="0" />'
                  + '</a>';
            }
          } else {
            // There are no users in room
            html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="15" height="1" />';
          }
          room_title=getLng('chat_room')+': '+cats[i]['rooms'][ii]['name']+' ['+cats[i]['rooms'][ii]['users_total']+' '+getLng('users')+']'+"\n "+cats[i]['rooms'][ii]['description'];
          if (cats[i]['rooms'][ii]['password_protected']) {
            room_pic='room_locked_15x12.gif';
            room_title+="\n *"+getLng('room_is_password_protected');
          } else {
            room_pic='members_15x15.gif';
          }
          html+='<span onclick="setActiveRoomId('+cats[i]['rooms'][ii]['id']+'); openCloseRoom('+cats[i]['rooms'][ii]['id']+', '+cats[i]['id']+', true); displaySimpleCategoryTree('+cats[i]['id']+'); showStealthSwitch('+(cats[i]['rooms'][ii]['moderated_by_me']? 'true' : 'false')+'); $(\'enterChatRoom_btn\').focus(); return false;" style="cursor:pointer" title="'+htmlspecialchars(room_title)+'" class="div_selection_scrollable_link">'
              + '<img src="./pic/'+room_pic+'" border="0" alt="" />'
              + '<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="12" />'
              + '<span class="'+(cats[i]['rooms'][ii]['id']==ActiveRoomId? 'div_selection_scrollable_active' : 'div_selection_scrollable_inactive')+'"  onclick="setActiveRoomId('+cats[i]['rooms'][ii]['id']+'); setActiveCategoryId('+cats[i]['id']+'); enterChatRoom(); return false;" style="cursor:pointer">'
              + htmlspecialchars(cats[i]['rooms'][ii]['name'])
              + htmlspecialchars(' ['+cats[i]['rooms'][ii]['users_total']+']')
              + '</span>'
              + '</span>'
              + '<br />';
          if (cats[i]['rooms'][ii]['opened']) {
            // Show users
            if (cats[i]['rooms'][ii]['users_total']>0) {
              for (var iii=0; iii<cats[i]['rooms'][ii]['users'].length; iii++) {
                usr=UserList.getRecord(cats[i]['rooms'][ii]['users'][iii]['user_id']);
                urec=urec_tpl;
                urec=urec.split('[ID]').join(usr.ID);
                // Online status
                if (true==usr.MutedLocally) {
                  ignored_img_suffix='ignored_';
                } else {
                  ignored_img_suffix='';
                }
                if (true==usr.GlobalMuted) {
                  status_img='./pic/online_status_muted_'+ignored_img_suffix+'10x10.gif';
                  if (usr.GlobalMutedUntil==0) {
                    status_title=getLng('permanently_globalmuted')+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
                  } else {
                    status_title=getLng('globalmuted_until').split('[EXPIRATION_DATE]').join(date(dateFormat, usr.GlobalMutedUntil))+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
                  }
                } else {
                  status_img='./pic/online_status_'+usr.OnlineStatus+'_'+ignored_img_suffix+'10x10.gif';
                  status_title=usr.OnlineStatusMessage+(ignored_img_suffix!=''? (' + '+getLng('ignored')) : '');
                }
                status_title=htmlspecialchars(status_title);
                urec=urec.split('[ONLINE_STATUS_ICON]').join('<img id="user_status_image_'+usr.ID+'" src="'+status_img+'" alt="'+status_title+'" title="'+status_title+'" />');
                // Gender
                if (userlistGender) {
                  urec=urec.split('[GENDER_ICON]').join('<img src="./pic/gender_'+usr.Gender+'_10x10.gif" alt="'+htmlspecialchars(getLng('gender')+': '+getLng('gender_'+usr.Gender))+'" title="'+htmlspecialchars(getLng('gender')+': '+getLng('gender_'+usr.Gender))+'" border="0" />');
                } else {
                  urec=urec.split('[GENDER_ICON]').join('');
                }
                // Avatar
                if (userlistAvatar) {
                  if (usr.AvatarBID>0) {
                    urec=urec.split('[AVATAR_THUMB]').join('<img style="cursor:pointer" onclick="showUserProfile('+usr.ID+')" src="'+htmlspecialchars(formlink)+'?b_x='+htmlspecialchars(userlistAvatarHeight)+'&amp;b_y='+htmlspecialchars(userlistAvatarWidth)+'&amp;b_id='+htmlspecialchars(usr.AvatarBID)+'&amp;s_id='+htmlspecialchars(s_id)+'" onmouseover="showUserlistAvatarThumb(this, '+htmlspecialchars(usr.AvatarBID)+')" onmouseout="hideUserlistAvatarThumb()" onclick="hideUserlistAvatarThumb()" alt="'+htmlspecialchars(getLng('avatar'))+'" title="'+htmlspecialchars(getLng('avatar'))+'" border="0" />');
                  } else {
                    urec=urec.split('[AVATAR_THUMB]').join('<img src="./pic/clearpixel_1x1.gif" width="'+htmlspecialchars(userlistAvatarWidth)+'" height="'+htmlspecialchars(userlistAvatarHeight)+'" alt="" title="" border="0" />');
                  }
                } else {
                  urec=urec.split('[AVATAR_THUMB]').join('');
                }
                // Nickname
                urec=urec.split('[NICKNAME_PLAIN]').join('"'+coloredToPlain(usr.Nickname, true)+'"');
                urec=urec.split('[NICKNAME_COLORED]').join(coloredToHTML(usr.Nickname));
                // Admin
                if (userlistPrivileged && usr.IsAdmin) {
                  urec=urec.split('_admin_section').join(' onclick="alert(getLng(\'user_is_admin\').split(\'[USER]\').join(\''+htmlspecialchars(coloredToPlain(usr.Nickname, false))+'\')); return false;" ');
                } else {
                  urec=urec.split('_admin_section').join(' style="display:none" ');
                }
                // Moderator
                if (userlistPrivileged && usr.IsModerator) {
                  urec=urec.split('_moderator_section').join(' onclick="alert(getLng(\'user_is_moderator\').split(\'[USER]\').join(\''+htmlspecialchars(coloredToPlain(usr.Nickname, false))+'\')); return false;" ');
                } else {
                  urec=urec.split('_moderator_section').join(' style="display:none" ');
                }
                html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="25" height="1" />'
                     +urec
                     +'<br />';
              }
            } else {
              cats[i]['rooms'][ii]['opened']=false;
            }
          }
        }
        html+='<br />';
      }
      if (cats[i]['children'].length) {
        html+=makeSimpleCategoryTreeHtml(cats[i]['children']);
      }
    }
  }
  return html;
}



/**
 * Show/hide "Stealth mode" checkbox
 * @param   boolean   status    If TRUE: checkbox will be shown, if FALSE - hidden
 */
function showStealthSwitch(status) {
  var stealthrow=$('stealth_mode_chkbox_row');
  var stealthbox=$('stealth_mode_chkbox');
  if (stealthrow && stealthbox) {
    if (ActiveRoomId>0 && typeof(status)=='boolean' && true==status) {
      // Show checkbox
      stealthrow.style.display='';
    } else {
      // Hide checkbox
      stealthrow.style.display='none';
      stealthbox.checked=false;
    }
  }
}

/**
 * Display "New room" dialogue
 * @param   int   category_id     Parent category
 */
function showNewRoomBox(category_id) {
  if (typeof(category_id)=='number' && category_id>0) {
    openWindow(formlink+'?s_id='+s_id+'&inc=create_user_room&category_id='+urlencode(category_id), 'create_user_room', 600, 400, false, false, false, false, true);
  }
}

/**
 * Set active room ID
 */
function setActiveRoomId(id) {
  try {
    ActiveRoomId_previous=ActiveRoomId;
    ActiveRoomId=stringToNumber(id);
  } catch (e) {}
}

/**
 * Set active category ID
 */
function setActiveCategoryId(id) {
  try {
    ActiveCategoryId_previous=ActiveCategoryId;
    ActiveCategoryId=stringToNumber(id);
  } catch (e) {}
}
