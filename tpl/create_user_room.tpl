<PCPIN:TPL name="main">
<form action="#" onsubmit="createRoom(); return false;">
  <table class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%">
    <tr>
      <td colspan="2" class="tbl_header_main">
        {TITLE}
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_ROOM_NAME}:</b>
      </td>
      <td class="tbl_row">
        <input type="text" id="room_name" title="{LNG_ROOM_NAME}" size="32" maxlength="{ROOM_NAME_LENGTH_MAX}" autocomplete="off" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" style="vertical-align:top">
        <b>{LNG_ROOM_DESCRIPTION}:</b>
      </td>
      <td class="tbl_row">
        <textarea id="room_description" cols="32" rows="8" title="{LNG_ROOM_DESCRIPTION}"></textarea>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" style="vertical-align:top">
        <b>{LNG_BACKGROUND_IMAGE}:</b>
      </td>
      <td class="tbl_row">
        <a id="background_image" href=":" title=""></a>
        <a href=":" id="upload_image_link" onclick="showRoomImageUploadForm(); return false;" title="{LNG_UPLOAD_NEW_IMAGE}">{LNG_UPLOAD_NEW_IMAGE}</a>
        &nbsp;
        <a href=":" id="delete_image_link" onclick="deleteRoomImage(); return false;" title="{LNG_DELETE_IMAGE}" style="display:none">{LNG_DELETE_IMAGE}</a>
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
              <select id="room_password_protected" onchange="togglePasswordFields()">
                <option value="0" selected="selected">{LNG_NO}</option>
                <option value="1">{LNG_YES}</option>
              </select>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            </td>
            <td class="tbl_row" style="text-align:right">
              &nbsp;
              <span id="room_password_fields" style="display:none;text-align:right;">
                <b>{LNG_ROOM_PASSWORD}:</b> <input type="password" id="room_password_1" size="16" maxlength="255" title="{LNG_ROOM_PASSWORD}" />
                <br />
                <b>{LNG_CONFIRM_PASSWORD}:</b> <input type="password" id="room_password_2" size="16" maxlength="255" title="{LNG_CONFIRM_PASSWORD}" />
              </span>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" colspan="2" style="text-align:center">
        <button type="submit" title="{LNG_CREATE_NEW_ROOM}">{LNG_CREATE_NEW_ROOM}</button>
        &nbsp;
        <button type="submit" onclick="window.close(); return false;" title="{LNG_CANCEL}">{LNG_CANCEL}</button>
      </td>
    </tr>
  </table>
</form>
</PCPIN:TPL>