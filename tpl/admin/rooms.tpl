<PCPIN:TPL name="main">
  <table id="categories_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
    <tr>
      <td colspan="6" class="tbl_header_main">
        <b>{LNG_CATEGORIES_AND_ROOMS}</b>
      </td>
    </tr>
    <tr>
      <td colspan="6" class="tbl_row">
        <button type="button" title="{LNG_CREATE_NEW_CATEGORY}" onclick="showCreateCategoryForm()">{LNG_CREATE_NEW_CATEGORY}</button>
      </td>
    </tr>
  </table>

  <form id="edit_category_form" action=":" method="post" onsubmit="updateCategory(); return false;">
    <input type="hidden" id="edit_category_id" value="" />
    <table id="edit_category_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
      <tr>
        <td colspan="2" class="tbl_header_main" id="edit_category_tbl_header"></td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_CATEGORY_NAME}:</b>
        </td>
        <td class="tbl_row">
          <input id="edit_category_name" type="text" title="{LNG_CATEGORY_NAME}" size="32" maxlength="255" autocomplete="off" />
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_CATEGORY_DESCRIPTION}:</b>
        </td>
        <td class="tbl_row">
          <textarea id="edit_category_description" title="{LNG_CATEGORY_DESCRIPTION}" rows="3" cols="32"></textarea>
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_WHO_CAN_CREATE_ROOMS_IN_CATEGORY}</b>
        </td>
        <td class="tbl_row">
          <label for="edit_category_creatable_rooms_n" title="{LNG_NOBODY}">
            <input type="radio" id="edit_category_creatable_rooms_n" name="creatable_rooms" value="n" /> {LNG_NOBODY}
          </label>
          <br />
          <label for="edit_category_creatable_rooms_r" title="{LNG_REGISTERED_USERS_ONLY}">
            <input type="radio" id="edit_category_creatable_rooms_r" name="creatable_rooms" value="r" /> {LNG_REGISTERED_USERS_ONLY}
          </label>
          <br />
          <label for="edit_category_creatable_rooms_g" title="{LNG_EVERYBODY}">
            <input type="radio" id="edit_category_creatable_rooms_g" name="creatable_rooms" value="g" /> {LNG_EVERYBODY}
          </label>
          <br />
        </td>
      </tr>
      <tr>
        <td colspan="2" class="tbl_row" style="text-align:center">
          <button type="submit" title="{LNG_SAVE_CHANGES}">{LNG_SAVE_CHANGES}</button>
          &nbsp;&nbsp;
          <button type="button" title="{LNG_CANCEL}" onclick="hideEditCategoryForm(); showCategories();">{LNG_CANCEL}</button>
        </td>
      </tr>
    </table>
  </form>

  <form id="create_category_form" action=":" method="post" onsubmit="createCategory(); return false;">
    <table id="create_category_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
      <tr>
        <td colspan="2" class="tbl_header_main">
        {LNG_CREATE_NEW_CATEGORY}
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_CATEGORY_NAME}:</b>
        </td>
        <td class="tbl_row">
          <input id="create_category_name" type="text" title="{LNG_CATEGORY_NAME}" size="32" maxlength="255" autocomplete="off" />
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_CATEGORY_DESCRIPTION}:</b>
        </td>
        <td class="tbl_row">
          <textarea id="create_category_description" title="{LNG_CATEGORY_DESCRIPTION}" rows="3" cols="32"></textarea>
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_WHO_CAN_CREATE_ROOMS_IN_CATEGORY}</b>
        </td>
        <td class="tbl_row">
          <label for="create_category_creatable_rooms_n" title="{LNG_NOBODY}">
            <input type="radio" id="create_category_creatable_rooms_n" name="creatable_rooms" value="n" /> {LNG_NOBODY}
          </label>
          <br />
          <label for="create_category_creatable_rooms_r" title="{LNG_REGISTERED_USERS_ONLY}">
            <input type="radio" id="create_category_creatable_rooms_r" name="creatable_rooms" value="r" /> {LNG_REGISTERED_USERS_ONLY}
          </label>
          <br />
          <label for="create_category_creatable_rooms_g" title="{LNG_EVERYBODY}">
            <input type="radio" id="create_category_creatable_rooms_g" name="creatable_rooms" value="g" /> {LNG_EVERYBODY}
          </label>
          <br />
        </td>
      </tr>
      <tr>
        <td colspan="2" class="tbl_row" style="text-align:center">
          <button type="submit" title="{LNG_CREATE_NEW_CATEGORY}">{LNG_CREATE_NEW_CATEGORY}</button>
          &nbsp;&nbsp;
          <button type="button" title="{LNG_CANCEL}" onclick="hideCreateCategoryForm(); showCategories();">{LNG_CANCEL}</button>
        </td>
      </tr>
    </table>
  </form>

  <form id="edit_room_form" action=":" method="post" onsubmit="updateRoom(); return false;">
    <input type="hidden" id="edit_room_id" value="" />
    <table id="edit_room_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
      <tr>
        <td colspan="2" class="tbl_header_main" id="edit_room_tbl_header"></td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_ROOM_NAME}:</b>
        </td>
        <td class="tbl_row">
          <input id="edit_room_name" type="text" title="{LNG_ROOM_NAME}" size="32" maxlength="255" autocomplete="off" />
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_ROOM_DESCRIPTION}:</b>
        </td>
        <td class="tbl_row">
          <textarea id="edit_room_description" title="{LNG_ROOM_DESCRIPTION}" rows="3" cols="32"></textarea>
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_CHAT_CATEGORY}:</b>
        </td>
        <td class="tbl_row">
          <select id="edit_room_category_id" title="{LNG_CHAT_CATEGORY}" />
        </td>
      </tr>
      <tr>
        <td class="tbl_row" style="vertical-align:top">
          <b>{LNG_BACKGROUND_IMAGE}:</b>
        </td>
        <td class="tbl_row">
          <a id="edit_room_background_image" href=":" title="{LNG_BACKGROUND_IMAGE}"></a>
          <a href=":" id="edit_room_upload_image_link" onclick="showRoomImageUploadForm(); return false;" title="{LNG_UPLOAD_NEW_IMAGE}">{LNG_UPLOAD_NEW_IMAGE}</a>
          &nbsp;
          <a href=":" id="edit_room_delete_image_link" onclick="deleteRoomImage(); return false;" title="{LNG_DELETE_IMAGE}" style="display:none">{LNG_DELETE_IMAGE}</a>
        </td>
      </tr>
      <tr>
        <td class="tbl_row" style="vertical-align:top">
          <b>{LNG_PROTECT_ROOM_WITH_PASSWORD}</b>
        </td>
        <td class="tbl_row">
          <table border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="tbl_row">
                <input type="hidden" id="edit_room_password_changed" value="0" />
                <select id="edit_room_password_protected" onchange="togglePasswordLink(this.value=='1'); togglePasswordFields(false);" title="{LNG_PROTECT_ROOM_WITH_PASSWORD}">
                  <option value="0" selected="selected">{LNG_NO}</option>
                  <option value="1">{LNG_YES}</option>
                </select>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </td>
              <td class="tbl_row" style="text-align:right">
                <span id="edit_room_password_link" style="display:none">
                  <a href=":" title="{LNG_CHANGE_PASSWORD}" onclick="togglePasswordLink(false); togglePasswordFields(true); return false;">{LNG_CHANGE_PASSWORD}</a>
                </span>
                &nbsp;
                <span id="edit_room_password_fields" style="display:none;text-align:right;">
                  <b>{LNG_ROOM_PASSWORD}:</b> <input type="password" id="edit_room_password_1" size="16" maxlength="255" title="{LNG_ROOM_PASSWORD}" />
                  <br />
                  <b>{LNG_CONFIRM_PASSWORD}:</b> <input type="password" id="edit_room_password_2" size="16" maxlength="255" title="{LNG_CONFIRM_PASSWORD}" />
                </span>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG__CONF_DEFAULT_MESSAGE_COLOR}</b>
        </td>
        <td class="tbl_row">
          <input type="hidden" id="edit_room_default_message_color" value="" />
          <div id="setting_color_edit_room_default_message_color" style="border: solid 1px #000000; cursor:pointer; width:60px; height: 20px;" title="{LNG__CONF_DEFAULT_MESSAGE_COLOR}" onclick="openColorBox('setting_color_edit_room_default_message_color', 'background-color', this, '$(\'edit_room_default_message_color\').value', true, null, true, this.style.backgroundColor); return false;">
            &nbsp;
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="tbl_row" style="text-align:center">
          <button type="submit" title="{LNG_SAVE_CHANGES}">{LNG_SAVE_CHANGES}</button>
          &nbsp;&nbsp;
          <button type="button" title="{LNG_CANCEL}" onclick="hideEditRoomForm(); showCategories();">{LNG_CANCEL}</button>
        </td>
      </tr>
    </table>
  </form>

  <form id="create_room_form" action=":" method="post" onsubmit="createRoom(); return false;">
    <input type="hidden" id="create_room_category_id" value="" />
    <table id="create_room_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
      <tr>
        <td colspan="2" class="tbl_header_main" id="create_room_tbl_header"></td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_ROOM_NAME}:</b>
        </td>
        <td class="tbl_row">
          <input id="create_room_name" type="text" title="{LNG_ROOM_NAME}" size="32" maxlength="255" autocomplete="off" />
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG_ROOM_DESCRIPTION}:</b>
        </td>
        <td class="tbl_row">
          <textarea id="create_room_description" title="{LNG_ROOM_DESCRIPTION}" rows="3" cols="32"></textarea>
        </td>
      </tr>
      <tr>
        <td class="tbl_row" style="vertical-align:top">
          <b>{LNG_BACKGROUND_IMAGE}:</b>
        </td>
        <td class="tbl_row">
          <a id="create_room_background_image" href=":" title="{LNG_BACKGROUND_IMAGE}"></a>
          <a href=":" id="create_room_upload_image_link" onclick="showRoomImageUploadForm(); return false;" title="{LNG_UPLOAD_NEW_IMAGE}">{LNG_UPLOAD_NEW_IMAGE}</a>
          &nbsp;
          <a href=":" id="create_room_delete_image_link" onclick="deleteRoomImage(); return false;" title="{LNG_DELETE_IMAGE}" style="display:none">{LNG_DELETE_IMAGE}</a>
        </td>
      </tr>
      <tr>
        <td class="tbl_row" style="vertical-align:top">
          <b>{LNG_PROTECT_ROOM_WITH_PASSWORD}</b>
        </td>
        <td class="tbl_row">
          <table border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td class="tbl_row">
                <select id="create_room_password_protected" onchange="toggleNewRoomPasswordFields(this.value=='1')" title="{LNG_PROTECT_ROOM_WITH_PASSWORD}">
                  <option value="0" selected="selected">{LNG_NO}</option>
                  <option value="1">{LNG_YES}</option>
                </select>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
              </td>
              <td class="tbl_row" style="text-align:right">
                <span id="create_room_password_link" style="display:none">
                  <a href=":" title="{LNG_CHANGE_PASSWORD}" onclick="togglePasswordLink(false); togglePasswordFields(true); return false;">{LNG_CHANGE_PASSWORD}</a>
                </span>
                &nbsp;
                <span id="create_room_password_fields" style="display:none;text-align:right;">
                  <b>{LNG_ROOM_PASSWORD}:</b> <input type="password" id="create_room_password_1" size="16" maxlength="255" title="{LNG_ROOM_PASSWORD}" />
                  <br />
                  <b>{LNG_CONFIRM_PASSWORD}:</b> <input type="password" id="create_room_password_2" size="16" maxlength="255" title="{LNG_CONFIRM_PASSWORD}" />
                </span>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="tbl_row">
          <b>{LNG__CONF_DEFAULT_MESSAGE_COLOR}</b>
        </td>
        <td class="tbl_row">
          <input type="hidden" id="create_room_default_message_color_global" value="{DEFAULT_MESSAGE_COLOR}" />
          <input type="hidden" id="create_room_default_message_color" value="" />
          <div id="setting_color_create_room_default_message_color" style="border: solid 1px #000000; cursor:pointer; width:60px; height: 20px;" title="{LNG__CONF_DEFAULT_MESSAGE_COLOR}" onclick="openColorBox('setting_color_create_room_default_message_color', 'background-color', this, '$(\'create_room_default_message_color\').value', true); return false;">
            &nbsp;
          </div>
        </td>
      </tr>
      <tr>
        <td colspan="2" class="tbl_row" style="text-align:center">
          <button type="submit" title="{LNG_SAVE_CHANGES}">{LNG_SAVE_CHANGES}</button>
          &nbsp;&nbsp;
          <button type="button" title="{LNG_CANCEL}" onclick="hideEditRoomForm(); showCategories();">{LNG_CANCEL}</button>
        </td>
      </tr>
    </table>
  </form>

</PCPIN:TPL>