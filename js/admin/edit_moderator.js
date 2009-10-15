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
 * Nicknames returned by last search, keyed by user ID
 * @var object
 */
var Users=Array();

/**
 * Moderated categories (keyed by user ID)
 * @var object
 */
var ModeratedCategories=Array();

/**
 * Moderated rooms (keyed by user ID)
 * @var object
 */
var ModeratedRooms=Array();

/**
 * Categories as an Array with first element is tree structure
 * and other elements as references to categories in tree structure.
 * @var object
 */
var CategoryTree=Array();

/**
 * Categories indexed by category ID
 * @var object
 */
var CategoryTreeByID=Array();

/**
 * Flag: if TRUE, then "Edit moderator" form will be displayed
 * automatically. if only one user will be found
 * @var boolean
 */
var autoShowSingleUserForm=true;

/**
 * Flag: if TRUE, then window is a popup and on submit opener will be reloaded
 * @var boolean
 */
var isPopUp=false;


/**
 * Init window
 * @param   boolean   is_popup      Flag: if TRUE, then window is a popup and on submit opener will be reloaded
 */
function initEditModeratorWindow(is_popup) {
  isPopUp=typeof(is_popup)=='boolean' && is_popup;
  showUserSearchForm();
  if (isPopUp) {
    $('close_window_span').style.display='';
  }
}


/**
 * Get chat rooms list grouped in categories and sorted by name
 */
function getRoomStructure() {
  CategoryTree=Array();
  $('categories_and_rooms').innerHTML='&nbsp;';
  sendData('_CALLBACK_getRoomStructure()', formlink, 'POST', 'ajax=get_room_structure&s_id='+urlencode(s_id), true);
}
function _CALLBACK_getRoomStructure() {
//debug(actionHandler.getResponseString()); return false;
  switch (actionHandler.status) {
    case  -1:
      // Session is invalid
      document.location.href=formlink+'?session_timeout';
      return false;
    break;
    case 0:
      // Success
      // Make category tree
      makeCategoryTree(actionHandler.data['category']);
      // Display category tree
      displayCategories();
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
      // Add category to the global array
      CategoryTree.push(
                          {
                            id: cat_id,
                            parent_id: cat_parent_id,
                            child_ids: Array(),
                            name: cat['name'][0],
                            description: cat['description'][0],
                            children: Array(),
                            children_by_id: Array(),
                            rooms: Array(),
                            rooms_by_id: Array()
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
        curr_cat['rooms'].push(
                                {
                                  id: room_id,
                                  name: room['name'][0],
                                  description: room['description'][0]
                                }
                               );
        curr_cat['rooms_by_id'][room_id]=curr_cat['rooms'][curr_cat['rooms'].length-1];
      }
      // Save child categories' IDs and rooms/users counters
      for (var i=0; i<curr_cat['children'].length; i++) {
        if (curr_cat['parent_id']!=-1) {
          CategoryTreeByID[curr_cat['parent_id']]['child_ids'].push(curr_cat['children'][i]['id']);
        }
      }
    }
  }
}

/**
 * Create simplified category tree HTML
 * @param   array     cats          Array with category tree
 * @return  string
 */
function makeSimpleCategoryTreeHtml(cats) {
  var html='';
  var user_id=stringToNumber($('moderator_user_id').value);
  var cat_chkbox=false;
  var room_chkbox=false;
  var cat=null;
  var room=null;
  if (cats.length) {
    for (var i=0; i<cats.length; i++) {
      // Categories
      cat=cats[i];
      cat_chkbox=-1!=(','+ModeratedCategories[user_id]+',').indexOf(','+cat['id']+',');
      html+='<label for="category_selector_'+htmlspecialchars(cat['id'])+'">'
          + '<img src="./pic/clearpixel_1x1.gif" border="0" width="3" height="1" />'
          + '<input type="checkbox" '+(cat_chkbox? 'checked="checked"' : '')+' onclick="categorySelector('+htmlspecialchars(cat['id'])+', this.checked);" id="category_selector_'+htmlspecialchars(cat['id'])+'" />'
          + '<img src="./pic/clearpixel_1x1.gif" border="0" width="3" height="1" />'
          + '<span title="'+htmlspecialchars(getLng('chat_category')+': '+cat['name']+"\n"+cat['description'])+'" class="div_selection_scrollable_link">'
          + '<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="12" />'
          + '<b>'+htmlspecialchars(cat['name'])+'</b>'
          + '</span>'
          + '</label>'
          + '<br />';
      // Rooms
      if (cat['rooms'].length) {
        for (var ii=0; ii<cat['rooms'].length; ii++) {
          room=cat['rooms'][ii];
          room_chkbox=cat_chkbox || (-1!=(','+ModeratedRooms[user_id]+',').indexOf(','+room['id']+','));
          room_title=getLng('chat_room')+': '+room['name']+"\n"+room['description'];
          html+='<label for="room_selector_'+htmlspecialchars(room['id'])+'">'
              + '<span title="'+htmlspecialchars(room_title)+'">'
              + '<img src="./pic/clearpixel_1x1.gif" border="0" width="25" height="12" />'
              + '<input type="checkbox" '+(room_chkbox? 'checked="checked"' : '')+' onclick="roomSelector('+htmlspecialchars(cat['id'])+');" id="room_selector_'+htmlspecialchars(room['id'])+'" />'
              + '<img src="./pic/clearpixel_1x1.gif" border="0" width="5" height="12" />'
              + '<span class="div_selection_scrollable_inactive">'
              + htmlspecialchars(room['name'])
              + '</span>'
              + '</span>'
              + '</label>'
              + '<br />';
        }
      } else {
        html+='<img src="./pic/clearpixel_1x1.gif" border="0" width="30" height="12" />'
            + getLng('category_has_no_rooms')
            + '<br />';
      }
      if (cat['children'].length) {
        html+=makeSimpleCategoryTreeHtml(cat['children']);
      }
    }
  }
  return html;
}

/**
 * This function is called on each category checkbox click
 * @param     int       category_id       Category ID
 * @param     boolean   selector_active   TRUE, if checkbox is checked
 */
function categorySelector(category_id, selector_active) {
  if (selector_active) {
    for (var i in CategoryTreeByID[category_id]['rooms']) {
      $('room_selector_'+CategoryTreeByID[category_id]['rooms'][i]['id']).checked=true;
    }
  }
}


/**
 * This function is called on each room checkbox click
 * @param     int       category_id       Parent category ID
 */
function roomSelector(category_id) {
  $('category_selector_'+category_id).checked=false;
}


/**
 * Display "Search for user" form
 */
function showUserSearchForm() {
  $('search_user_row').style.display='';
  $('user_name_row').style.display='none';
  $('categories_rooms_row').style.display='none';
  $('nickname_search').focus();
  $('nickname_search').select();
}


/**
 * Hide "Search for user" form
 */
function hideUserSearchForm() {
  $('search_user_row').style.display='none';
  $('user_name_row').style.display='';
  $('categories_rooms_row').style.display='';
}


/**
 * Search for user
 * @param     boolean     resize    Optional. If TRUE, the window will be automatically resized after search complete. Default: FALSE.
 */
function moderatorSearchUser(resize) {
  autoShowSingleUserForm=true;
  Users=Array();
  $('nickname_search').value=trimString($('nickname_search').value);
  sendData('_CALLBACK_getMemberlist('+(typeof(resize)=='boolean' && resize ? 'true' : 'false')+')', formlink, 'POST', 'ajax=get_memberlist'
                                                                                                                     +'&s_id='+urlencode(s_id)
                                                                                                                     +'&sort_by=1'
                                                                                                                     +'&sort_dir=0'
                                                                                                                     +'&nickname='+urlencode($('nickname_search').value)
                                                                                                                     );
}
function _CALLBACK_getMemberlist(resize) {
//debug(actionHandler.getResponseString()); return false;
  var members=null;
  var member_nr=0;
  var user_id=0;
  var nickname='';
  var users_count=0;

  if (actionHandler.status==-1) {
    // Session is invalid
    window.close();
    opener.document.location.href=formlink+'?session_timeout&ts='+unixTimeStamp();
    return false;
  } else {
    if (actionHandler.status==0 && typeof(actionHandler.data['member'])!='undefined' && actionHandler.data['member'].length) {
      for (member_nr=0; member_nr<actionHandler.data['member'].length; member_nr++) {
        member=actionHandler.data['member'][member_nr];
        users_count++;
        user_id=stringToNumber(member['id'][0]);
        nickname=member['nickname'][0];
        Users[user_id]=nickname;
        ModeratedCategories[user_id]=member['moderated_categories'][0];
        ModeratedRooms[user_id]=member['moderated_rooms'][0];
      }
    }
  }
  // Display users
  if (users_count==0) {
    // No members found
    $('search_results').style.display='none';
    alert(getLng('no_members_found'));
  } else {
    showSearchResults();
  }
  toggleProgressBar(false);
  showUserSearchForm();
  if (users_count!=0 && resize) {
    setTimeout('resizeForDocumentHeight(10)', 1100);
  }
}


/**
 * Display search results
 */
function showSearchResults() {
  var users_count=0;
  var col_length=0;
  var col_length_max=0;
  var col_nr=0;
  var col_html='';
  var cols_html=Array();
  var last_user_id=0;
  for (var i in Users) {
    users_count++;
  }

  if (Users.length>0) {
    $('search_results').style.display='';
    col_length_max=Math.ceil(users_count/3);
    col_html='';
    col_length=0;
    for (var i in Users) {
      col_length++;
      col_html+='<a href=":" onclick="showModeratorForm('+htmlspecialchars(i)+'); return false;" title="'+htmlspecialchars(getLng('edit_moderator')+': '+coloredToPlain(Users[i], false))+'">'
              + coloredToHTML(Users[i])
              + '</a>'
              + '<br />';
      if (col_length==col_length_max) {
        cols_html[col_nr]=col_html;
        col_html='';
        col_nr++;
        col_length=0;
      }
      last_user_id=i;
    }
    if (col_html!='') {
      cols_html[col_nr]=col_html;
    }
    for (var i=0; i<3; i++) {
      if (typeof(cols_html[i])=='string') {
        $('search_results_col_'+i).innerHTML=cols_html[i];
      } else {
        $('search_results_col_'+i).innerHTML='&nbsp;';
      }
    }
  }
  if (users_count==1 && autoShowSingleUserForm) {
    setTimeout("showModeratorForm("+last_user_id+");", 300);
    autoShowSingleUserForm=false;
  }
}


/**
 * Hide search results
 */
function hideSearchResults() {
  $('search_results').style.display='none';
}


/**
 * Display categories and rooms
 */
function displayCategories() {
  $('categories_rooms_row').style.display='';
  $('categories_and_rooms').innerHTML=makeSimpleCategoryTreeHtml(CategoryTree[0]['children']);
}


/**
 * Hide categories and rooms
 */
function hideCategories() {
  $('categories_rooms_row').style.display='none';
}


/**
 * Display moderator form
 * @param     int     user_id     User ID
 */
function showModeratorForm(user_id) {
  hideUserSearchForm();
  hideSearchResults();
  $('user_name_row').style.display='';
  $('moderator_user_id').value=user_id;
  $('moderator_user_nick').innerHTML=coloredToHTML(Users[user_id]);
  // Get room structure
  getRoomStructure();
}


/**
 * Hide moderator form
 * @param     int     user_id     User ID
 */
function hideModeratorForm() {
  $('user_name_row').style.display='none';
  hideCategories();
  showUserSearchForm();
  showSearchResults();
}


/**
 * Save new moderator data
 */
function saveModerator(cats, cat_ids, room_ids, level) {
  if (typeof(cats)=='undefined') {
    cats=CategoryTree[0]['children'];
  }
  if (typeof(cat_ids)=='undefined') {
    cat_ids=Array();
  }
  if (typeof(room_ids)=='undefined') {
    room_ids=Array();
  }
  if (typeof(level)=='undefined') {
    level=0;
  }
  for (var i in cats) {
    if ($('category_selector_'+cats[i]['id']).checked) {
      // Category is selected
      cat_ids.push(cats[i]['id']);
    } else {
      // Check rooms
      for (var ii in cats[i]['rooms_by_id']) {
        if ($('room_selector_'+ii).checked) {
          // Room is selected
          room_ids.push(ii);
        }
      }
    }
  }
  sendData('_CALLBACK_saveModerator()', formlink, 'POST', 'ajax=update_moderator'
                                                         +'&s_id='+urlencode(s_id)
                                                         +'&moderator_user_id='+urlencode($('moderator_user_id').value)
                                                         +'&categories='+urlencode(cat_ids.join(','))
                                                         +'&rooms='+urlencode(room_ids.join(',')),
                                                         true);
}
function _CALLBACK_saveModerator() {
//debug(actionHandler.getResponseString()); return false;
  if (actionHandler.status==-1) {
    // Session is invalid
    document.location.href=formlink+'?session_timeout';
    return false;
  }
  if (actionHandler.status==0 && isPopUp) {
    alert(actionHandler.message, 0, 0, 'try { window.opener.document.location.reload() } catch (e) {} window.close()');
  } else {
    alert(actionHandler.message, 0, 0, 'moderatorSearchUser()');
  }
}