<div id="confirmbox" style="display:none">
  <table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="tbl_header_main" onmousedown="startDragNDrop('confirmbox')" onmouseup="stopDragNDrop('confirmbox')" style="cursor:move;padding:2px;text-align:right;">
        <button type="button" style="width:13px;height:13px;background-image:url(./pic/close_13x13.gif);padding:0px;margin:0px;" title="{LNG_CLOSE_WINDOW}" onclick="hideConfirmBox()"></button>
      </td>
    </tr>
    <tr>
      <td class="text">
        <div id="confirmbox_text"></div>
      </td>
    </tr>
    <tr>
      <td class="text" style="text-align:center">
        <button type="button" id="confirmbox_btn_ok" title="{LNG_OK}" onclick="hideConfirmBox(true)">&nbsp;&nbsp;&nbsp;&nbsp;{LNG_OK}&nbsp;&nbsp;&nbsp;&nbsp;</button>
        &nbsp;&nbsp;&nbsp;
        <button type="button" title="{LNG_CANCEL}" onclick="hideConfirmBox(false)">&nbsp;{LNG_CANCEL}&nbsp;</button>
      </td>
  </table>
</div>