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
var CategoryTree=new Array();

/**
 * Previous state of CategoryTree array
 * @var object
 */
var CategoryTree_previous=new Array();

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
  CategoryTree=new Array();
  setActiveCategoryId(0);
  setActiveRoomId(0);
  setCssClass($('chat_rooms_list'), '.div_selection_scrollable');
  setCssClass($('chat_categories_list'), '.div_selection_scrollable');
  UserList.initialize();
}


/**
 * Get chat rooms list grouped in categories and sorted by name, including list of users in each room.
 */
function getRoomStructure() {
  resetRoomStructure();
  sendData('_CALLBACK_getRoomStructure()', formlink, 'POST', 'ajax='+urlencode('get_room_structure')+'&s_id='+urlencode(s_id), true);
}
function _CALLBACK_getRoomStructure() {
//debug(actionHandler.getResponseString());
  var message=actionHandler.getCdata('message');
  var status=actionHandler.getCdata('status');
  var cats=null;
  var additional_data=null;
  switch (status) {

    case  '-1':
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;

    case '0':
      // Success
      // Get categories
      if (null!=(cats=actionHandler.getElement('categories'))) {
        // Make category tree
        makeCategoryTree(cats);
        // Display category tree
        if (typeof(roomSelectionDisplayType)=='number' && roomSelectionDisplayType==1) {
          displaySimpleCategoryTree();
        } else {
          displayCategoryTree();
        }
        if (ActiveCategoryId>0) {
          showCategoryRooms(ActiveCategoryId);
        }
      }
      // Get additional data
      if (null!=(additional_data=actionHandler.getElement('additional_data'))) {
        // Are there new invitations?
        if ('1'==actionHandler.getCdata('new_invitations', 0, additional_data)) {
          if (typeof(getNewInvitations)!='undefined') {
            getNewInvitations();
          }
        }
        // Are there new messages?
        if ('1'==actionHandler.getCdata('new_messages', 0, additional_data)) {
          if (typeof(getNewMessages)!='undefined' && getNewMessages) {
            getNewMessages();
          }
        }
      }
    break;

  }
  toggleProgressBar(false);
}


/**
 * Create category tree from Categories XML object
 * @param   object    cats        Categories as XML object
 * @return  array
 */
function makeCategoryTree(cats) {
  var cat=null;
  var cat_nr=0;
  var cat_id=0;
  var cat_parent_id=0;
  var room=null;
  var room_nr=0;
  var room_id=0;
  var user_nr=0;
  var user=null;
  var user_id=0;
  if (cats) {
    while (cat=actionHandler.getElement('category', cat_nr++, cats)) {
      // Add category to the global array
      cat_id=stringToNumber(actionHandler.getCdata('id', 0, cat));
      if (ActiveCategoryId_previous==cat_id) {
        setActiveCategoryId(cat_id);
      }
      cat_parent_id=actionHandler.getCdata('parent_id', 0, cat);
      if (cat_parent_id!=null) {
        cat_parent_id=stringToNumber(cat_parent_id);
      }
      CategoryTree[cat_id]=new Array();
      CategoryTree[cat_id]['parent_id']=cat_parent_id;
      CategoryTree[cat_id]['name']=actionHandler.getCdata('name', 0, cat);
      CategoryTree[cat_id]['description']=actionHandler.getCdata('description', 0, cat, '');
      CategoryTree[cat_id]['creatable_rooms']=actionHandler.getCdata('creatable_rooms', 0, cat)=='1';
      CategoryTree[cat_id]['children']=new Array();
      CategoryTree[cat_id]['child_ids']=new Array();
      CategoryTree[cat_id]['rooms']=new Array();
      CategoryTree[cat_id]['opened']=typeof(CategoryTree_previous[cat_id])!='undefined' && CategoryTree_previous[cat_id]['opened'];
      CategoryTree[cat_id]['rooms_local']=0;
      CategoryTree[cat_id]['rooms_total']=0;
      CategoryTree[cat_id]['users_total']=0;
      if (CategoryTree[cat_id]['parent_id']!=null) {
        // Make a reference
        CategoryTree[CategoryTree[cat_id]['parent_id']]['children'][cat_id]=CategoryTree[cat_id];
      }
      // Get child categories
      makeCategoryTree(actionHandler.getElement('categories', 0, cat));
      // Get rooms
      room_nr=0;
      while (room=actionHandler.getElement('room', room_nr++, cat)) {
        room_id=actionHandler.getCdata('id', 0, room);
        if (ActiveRoomId_previous==room_id) {
          setActiveRoomId(room_id);
        }
        CategoryTree[cat_id]['rooms'][room_id]=new Array();
        CategoryTree[cat_id]['rooms'][room_id]['password_protected']='0'!=actionHandler.getCdata('password_protected', 0, room);
        CategoryTree[cat_id]['rooms'][room_id]['name']=actionHandler.getCdata('name', 0, room);
        CategoryTree[cat_id]['rooms'][room_id]['description']=actionHandler.getCdata('description', 0, room, '');
        CategoryTree[cat_id]['rooms'][room_id]['opened']=typeof(CategoryTree_previous[cat_id])!='undefined' && typeof(CategoryTree_previous[cat_id]['rooms'][room_id])!='undefined' && CategoryTree_previous[cat_id]['rooms'][room_id]['opened'];
        CategoryTree[cat_id]['rooms'][room_id]['moderated_by_me']='1'==actionHandler.getCdata('moderated_by_me', 0, room);
        CategoryTree[cat_id]['rooms'][room_id]['users']=new Array();
        CategoryTree[cat_id]['rooms'][room_id]['users_total']=0;
        CategoryTree[cat_id]['rooms_local']++;
        CategoryTree[cat_id]['rooms_total']++;
        // Get users
        user_nr=0;
        while (user=actionHandler.getElement('user', user_nr++, room)) {
          user_id=actionHandler.getCdata('id', 0, user);
          CategoryTree[cat_id]['rooms'][room_id]['users'][user_id]=new Array();
          CategoryTree[cat_id]['rooms'][room_id]['users'][user_id]['nickname']=actionHandler.getCdata('nickname', 0, user);
          CategoryTree[cat_id]['rooms'][room_id]['users'][user_id]['nickname_plain']=actionHandler.getCdata('nickname_plain', 0, user);
          CategoryTree[cat_id]['rooms'][room_id]['users_total']++;
          CategoryTree[cat_id]['users_total']++;
          UserList.addRecord(user_id,
                             actionHandler.getCdata('nickname', 0, user),
                             actionHandler.getCdata('online_status', 0, user),
                             actionHandler.getCdata('online_status_message', 0, user),
                             '1'==actionHandler.getCdata('muted_locally', 0, user),
                             '1'==actionHandler.getCdata('global_muted', 0, user),
                             actionHandler.getCdata('global_muted_until', 0, user),
                             actionHandler.getCdata('ip_address', 0, user),
                             actionHandler.getCdata('gender', 0, user),
                             actionHandler.getCdata('avatar_bid', 0, user),
                             '1'==actionHandler.getCdata('is_admin', 0, user),
                             '1'==actionHandler.getCdata('is_moderator', 0, user)
                             );

        }
      }
      // Save child categories' IDs and rooms/users counters
      for (var i in CategoryTree[cat_id]['children']) {
        CategoryTree[cat_id]['child_ids'][CategoryTree[cat_id]['child_ids'].length]=i;
        CategoryTree[cat_id]['rooms_total']+=CategoryTree[cat_id]['children'][i]['rooms_total'];
        CategoryTree[cat_id]['users_total']+=CategoryTree[cat_id]['children'][i]['users_total'];
        if (CategoryTree[cat_id]['parent_id']!=null) {
          CategoryTree[CategoryTree[cat_id]['parent_id']]['child_ids'][CategoryTree[CategoryTree[cat_id]['parent_id']]['child_ids'].length]=i;
          CategoryTree[cat_id]['parent_id']['rooms_total']+=CategoryTree[cat_id]['rooms_total'];
          CategoryTree[cat_id]['parent_id']['users_total']+=CategoryTree[cat_id]['users_total'];
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
    for (var i in cats) {
      if (depth>0) {
        html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="'+(depth*15)+'" height="1" />';
      }
      if (cats[i]['children'].length) {
        if (cats[i]['opened']) {
          html+='<a href="#" title="'+htmlspecialchars(getLng('chat_category')+': '+cats[i]['name']+"\n"+cats[i]['description'])+'" onclick="setActiveRoomId(0); showStealthSwitch(false); closeCategoryFolder('+i+'); showCategoryRooms(); return false;">'
              + '<img src="./pic/minus_box_15x12.gif" border="0" />'
              + '</a>';
        } else {
          html+='<a href="#" title="'+htmlspecialchars(getLng('chat_category')+': '+cats[i]['name']+"\n"+cats[i]['description'])+'" onclick="setActiveRoomId(0); showStealthSwitch(false); openCategoryFolder('+i+'); return false;">'
              + '<img src="./pic/plus_box_15x12.gif" border="0" />'
              + '</a>';
        }
      } else {
        html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="15" height="1" />';
      }
      html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="3" height="1" />'
          + '<a title="'+htmlspecialchars(getLng('chat_category')+': '+cats[i]['name']+' ['+cats[i]['rooms_total']+' '+getLng('rooms')+' / '+cats[i]['users_total']+' '+getLng('users')+']'+"\n"+cats[i]['description'])+'" class="div_selection_scrollable_link" href="#" onclick="setActiveRoomId(0); showStealthSwitch(false); setActiveCategoryId('+i+'); openCategoryFolder('+i+'); showCategoryRooms('+i+'); return false;">'
          + '<img src="./pic/'+(i==ActiveCategoryId? 'folder_opened_15x12.gif' : 'folder_closed_15x12.gif')+'" border="0" />'
          + '<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="12" />'
          + '<span class="'+(i==ActiveCategoryId? 'div_selection_scrollable_active' : 'div_selection_scrollable_inactive')+'">'
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

  if (category_id>0) {
    html='<b>'+getLng('chat_category')+' &quot;'+htmlspecialchars(CategoryTree[category_id]['name'])+'&quot;</b><br />';
    if (CategoryTree[category_id]['description']!='') {
      html+=nl2br(htmlspecialchars(CategoryTree[category_id]['description']))+'<br />';
    }
    if (CategoryTree[category_id]['children'].length) {
      // Display subcategories
      html+='<br /><u>'+getLng('subcategories')+':</u><br />';
      for (var ii in CategoryTree[category_id]['children']) {
        html+='<a onclick="setActiveCategoryId('+ii+'); openCategoryFolder('+ii+'); showCategoryRooms(); return false;" title="'+htmlspecialchars(getLng('chat_category')+': '+CategoryTree[category_id]['children'][ii]['name']+' ['+CategoryTree[category_id]['rooms_total']+' '+getLng('rooms')+' / '+CategoryTree[category_id]['users_total']+' '+getLng('users')+']'+"\n"+CategoryTree[category_id]['children'][ii]['description'])+'" href="#" class="div_selection_scrollable_link">'
            + '<img src="./pic/folder_closed_15x12.gif" border="0" />'
            + '<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="1" />'
            + '<span class="div_selection_scrollable_inactive">'
            + htmlspecialchars(CategoryTree[category_id]['children'][ii]['name'])
            + htmlspecialchars(' ['+CategoryTree[category_id]['children'][ii]['rooms_total']+'/'+CategoryTree[category_id]['children'][ii]['users_total']+']')
            + '</span>'
            + '</a>'
            + '<br />';
      }
    }
    html+='<hr width="100%" />';
    // Display rooms
    if (CategoryTree[category_id]['rooms'].length) {
      html+='<u>'+getLng('chat_rooms')+':</u><br />';
      for (var ii in CategoryTree[category_id]['rooms']) {
        if (CategoryTree[category_id]['rooms'][ii]['users'].length) {
          // There are users in room
          if (CategoryTree[category_id]['rooms'][ii]['opened']) {
            html+='<a href="#" title="'+htmlspecialchars(getLng('hide_online_users'))+'" onclick="openCloseRoom('+ii+', '+category_id+', false); showCategoryRooms('+category_id+'); return false;">'
                + '<img src="./pic/minus_box_15x12.gif" border="0" />'
                + '</a>'
                ;
          } else {
            html+='<a href="#" title="'+htmlspecialchars(getLng('show_online_users'))+'" onclick="openCloseRoom('+ii+', '+category_id+', true); showCategoryRooms('+category_id+'); return false;">'
                + '<img src="./pic/plus_box_15x12.gif" border="0" />'
                + '</a>';
          }
        } else {
          // There are no users in room
          html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="15" height="1" />';
        }
        room_title=getLng('chat_room')+': '+CategoryTree[category_id]['rooms'][ii]['name']+' ['+CategoryTree[category_id]['rooms'][ii]['users_total']+' '+getLng('users')+']'+"\n"+CategoryTree[category_id]['rooms'][ii]['description'];
        if (CategoryTree[category_id]['rooms'][ii]['password_protected']) {
          room_pic='room_locked_15x12.gif';
          room_title+="\n*"+getLng('room_is_password_protected');
        } else {
          room_pic='members_15x15.gif';
        }
        html+='<a onclick="setActiveRoomId('+ii+'); openCloseRoom('+ii+', '+category_id+', true); showCategoryRooms('+category_id+'); showStealthSwitch('+(CategoryTree[category_id]['rooms'][ii]['moderated_by_me']? 'true' : 'false')+'); $(\'enterChatRoom_btn\').focus(); return false;" href="#" title="'+htmlspecialchars(room_title)+'" class="div_selection_scrollable_link">'
            + '<img src="./pic/'+room_pic+'" border="0" alt="" />'
            + '</a>'
            + '<img src="./pic/clearpixel_1x1.gif" border="0" width="8" height="12" />'
            + '<a onclick="setActiveRoomId('+ii+'); enterChatRoom(CurrentNicknameID); return false;" style="cursor:pointer" title="'+htmlspecialchars(room_title)+'">'
            + '<span class="'+(ii==ActiveRoomId? 'div_selection_scrollable_active' : 'div_selection_scrollable_inactive')+'">'
            + htmlspecialchars(CategoryTree[category_id]['rooms'][ii]['name'])
            + htmlspecialchars(' ['+CategoryTree[category_id]['rooms'][ii]['users_total']+']')
            + '</span>'
            + '</a>'
            + '<br />';
        if (CategoryTree[category_id]['rooms'][ii]['opened']) {
          // Show users
          if (CategoryTree[category_id]['rooms'][ii]['users_total']>0) {
            for (var iii in CategoryTree[category_id]['rooms'][ii]['users']) {
              usr=UserList.getRecord(iii);
              urec=urec_tpl;
              urec=urec.split('[ID]').join(iii);
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
              urec=urec.split('[ONLINE_STATUS_ICON]').join('<img id="user_status_image_'+iii+'" src="'+status_img+'" alt="'+status_title+'" title="'+status_title+'" />');
              // Gender
              if (userlistGender) {
                urec=urec.split('[GENDER_ICON]').join('<img src="./pic/gender_'+usr.Gender+'_10x10.gif" alt="'+htmlspecialchars(getLng('gender')+': '+getLng('gender_'+usr.Gender))+'" title="'+htmlspecialchars(getLng('gender')+': '+getLng('gender_'+usr.Gender))+'" border="0" />');
              } else {
                urec=urec.split('[GENDER_ICON]').join('');
              }
              // Avatar
              if (userlistAvatar) {
                if (usr.AvatarBID>0) {
                  urec=urec.split('[AVATAR_THUMB]').join('<img style="cursor:pointer" onclick="showUserProfile('+iii+')" src="'+htmlspecialchars(formlink)+'?b_x='+htmlspecialchars(userlistAvatarHeight)+'&amp;b_y='+htmlspecialchars(userlistAvatarWidth)+'&amp;b_id='+htmlspecialchars(usr.AvatarBID)+'&amp;s_id='+htmlspecialchars(s_id)+'" alt="'+htmlspecialchars(getLng('avatar'))+'" title="'+htmlspecialchars(getLng('avatar'))+'" border="0" />');
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
            CategoryTree[category_id]['rooms'][ii]['opened']=false;
          }
        }
      }
    } else {
      html+=getLng('category_has_no_rooms');
    }
    if (CategoryTree[category_id]['creatable_rooms']) {
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
    CategoryTree[category_id]['rooms'][room_id]['opened']=open;
  } catch (e) {}
}


/**
 * Display simplified category tree HTML
 * @param   array     cats          Array with category tree
 */
function displaySimpleCategoryTree(cats) {
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
    for (var i in cats) {
      if (cats[i]['rooms_local']>0 || cats[i]['creatable_rooms']) {
        html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="3" height="1" />'
            + '<span title="'+htmlspecialchars(getLng('chat_category')+': '+cats[i]['name']+' ['+cats[i]['rooms_local']+' '+getLng('rooms')+' / '+cats[i]['users_total']+' '+getLng('users')+']'+"\n"+cats[i]['description'])+'" class="div_selection_scrollable_link">'
            + '<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="12" />'
            + '<b>'+htmlspecialchars(cats[i]['name'])+'</b>'
            + ' ['+cats[i]['rooms_total']+'/'+cats[i]['users_total']+']'
            + '</span>';
        if (cats[i]['creatable_rooms']) {
          html+='<br />'
               +'<img src="./pic/clearpixel_1x1.gif" border="0" width="10" height="1" />'
               +'<a href="#" onclick="showNewRoomBox('+i+')" title="'+htmlspecialchars(getLng('create_new_room'))+'">'
               +'<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="1" />'
               +htmlspecialchars(getLng('create_new_room'))
               +'</a>';
        }
        html+='<br />';
        // Rooms
        for (var ii in cats[i]['rooms']) {
          if (cats[i]['rooms'][ii]['users'].length) {
            // There are users in room
            if (cats[i]['rooms'][ii]['opened']) {
              html+='<a href="#" title="'+htmlspecialchars(getLng('hide_online_users'))+'" onclick="openCloseRoom('+ii+', '+i+', false); displaySimpleCategoryTree(); return false;">'
                  + '<img src="./pic/minus_box_15x12.gif" border="0" />'
                  + '</a>';
            } else {
              html+='<a href="#" title="'+htmlspecialchars(getLng('show_online_users'))+'" onclick="openCloseRoom('+ii+', '+i+', true); displaySimpleCategoryTree('+i+'); return false;">'
                  + '<img src="./pic/plus_box_15x12.gif" border="0" />'
                  + '</a>';
            }
          } else {
            // There are no users in room
            html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="15" height="1" />';
          }
          room_title=getLng('chat_room')+': '+cats[i]['rooms'][ii]['name']+' ['+cats[i]['rooms'][ii]['users_total']+' '+getLng('users')+']'+"\n"+cats[i]['rooms'][ii]['description'];
          if (cats[i]['rooms'][ii]['password_protected']) {
            room_pic='room_locked_15x12.gif';
            room_title+="\n*"+getLng('room_is_password_protected');
          } else {
            room_pic='members_15x15.gif';
          }
          html+='<span onclick="setActiveRoomId('+ii+'); openCloseRoom('+ii+', '+i+', true); displaySimpleCategoryTree('+i+'); showStealthSwitch('+(cats[i]['rooms'][ii]['moderated_by_me']? 'true' : 'false')+'); $(\'enterChatRoom_btn\').focus(); return false;" style="cursor:pointer" title="'+htmlspecialchars(room_title)+'" class="div_selection_scrollable_link">'
              + '<img src="./pic/'+room_pic+'" border="0" alt="" />'
              + '<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="12" />'
              + '<span class="'+(ii==ActiveRoomId? 'div_selection_scrollable_active' : 'div_selection_scrollable_inactive')+'"  onclick="setActiveRoomId('+ii+'); setActiveCategoryId('+i+'); enterChatRoom(CurrentNicknameID); return false;" style="cursor:pointer">'
              + htmlspecialchars(cats[i]['rooms'][ii]['name'])
              + htmlspecialchars(' ['+cats[i]['rooms'][ii]['users_total']+']')
              + '</span>'
              + '</span>'
              + '<br />';
          if (cats[i]['rooms'][ii]['opened']) {
            // Show users
            if (cats[i]['rooms'][ii]['users_total']>0) {
              for (var iii in cats[i]['rooms'][ii]['users']) {
                usr=UserList.getRecord(iii);
                urec=urec_tpl;
                urec=urec.split('[ID]').join(iii);
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
                urec=urec.split('[ONLINE_STATUS_ICON]').join('<img id="user_status_image_'+iii+'" src="'+status_img+'" alt="'+status_title+'" title="'+status_title+'" />');
                // Gender
                if (userlistGender) {
                  urec=urec.split('[GENDER_ICON]').join('<img src="./pic/gender_'+usr.Gender+'_10x10.gif" alt="'+htmlspecialchars(getLng('gender')+': '+getLng('gender_'+usr.Gender))+'" title="'+htmlspecialchars(getLng('gender')+': '+getLng('gender_'+usr.Gender))+'" border="0" />');
                } else {
                  urec=urec.split('[GENDER_ICON]').join('');
                }
                // Avatar
                if (userlistAvatar) {
                  if (usr.AvatarBID>0) {
                    urec=urec.split('[AVATAR_THUMB]').join('<img style="cursor:pointer" onclick="showUserProfile('+iii+')" src="'+htmlspecialchars(formlink)+'?b_x='+htmlspecialchars(userlistAvatarHeight)+'&amp;b_y='+htmlspecialchars(userlistAvatarWidth)+'&amp;b_id='+htmlspecialchars(usr.AvatarBID)+'&amp;s_id='+htmlspecialchars(s_id)+'" alt="'+htmlspecialchars(getLng('avatar'))+'" title="'+htmlspecialchars(getLng('avatar'))+'" border="0" />');
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
