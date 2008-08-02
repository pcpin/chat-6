<div id="alertbox" style="display:none">
  <table border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td class="tbl_header_main" onmousedown="startDragNDrop('alertbox')" onmouseup="stopDragNDrop('alertbox')" style="cursor:move;padding:2px;text-align:right;">
        <button type="button" style="width:13px;height:13px;background-image:url(./pic/close_13x13.gif);padding:0px;margin:0px;" title="{LNG_CLOSE_WINDOW}" onclick="hideAlertBox()"></button>
      </td>
    </tr>
    <tr>
      <td class="text">
        <div id="alertbox_text"></div>
      </td>
    </tr>
    <tr>
      <td class="text" style="text-align:center">
        <button id="alertbox_btn" type="button" title="{LNG_OK}" onclick="hideAlertBox()">&nbsp;&nbsp;&nbsp;&nbsp;{LNG_OK}&nbsp;&nbsp;&nbsp;&nbsp;</button>
      </td>
  </table>
</div>