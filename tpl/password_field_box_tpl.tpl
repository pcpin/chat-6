<div id="password_field_box" style="display:none;padding:0px;">
  <table class="tbl" width="300px" style="border:0px" cellpadding="0" cellspacing="0">
    <tr>
      <td style="padding:0px;margin:0px">
        <table border="0" cellspacing="0" cellpadding="0" width="100%">
          <tr>
            <td class="tbl_header_main" onmousedown="startDragNDrop('password_field_box')" onmouseup="stopDragNDrop('password_field_box')">
              <span id="password_field_box_title"></span>
            </td>
            <td class="tbl_header_main" style="text-align:right;width:1%;padding-left:0px;">
              <button type="button" style="width:13px;height:13px;background-image:url(./pic/close_13x13.gif);padding:0px;margin:0px;" title="{LNG_CLOSE_WINDOW}" onclick="hidePasswordFieldBox()"></button>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" style="text-align:center">
        <b>{LNG_ENTER_PASSWORD}:</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" style="text-align:center">
        <input type="password" id="password_field_box_input" size="12" maxlength="255" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" style="text-align:center">
        <button type="button" onclick="hidePasswordFieldBox(true)" title="{LNG_OK}">{LNG_OK}</button>
        &nbsp;
        <button type="button" onclick="hidePasswordFieldBox(false)" title="{LNG_CANCEL}">{LNG_CANCEL}</button>
      </td>
    </tr>
  </table>
</div>
