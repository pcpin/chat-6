<div id="promptbox" style="display:none">
  <form method="post" action="#" onsubmit="hidePromptBox(true); return false;">
    <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td class="tbl_header_main" onmousedown="startDragNDrop('promptbox')" onmouseup="stopDragNDrop('promptbox')" style="cursor:move;padding:2px;text-align:right;">
          <button type="button" style="width:13px;height:13px;background-image:url(./pic/close_13x13.gif);padding:0px;margin:0px;" title="{LNG_CLOSE_WINDOW}" onclick="hidePromptBox()"></button>
        </td>
      </tr>
      <tr>
        <td class="text" id="promptbox_text"></td>
      </tr>
      <tr>
        <td class="text" style="text-align:center">
          <input type="text" id="promptbox_input" size="64" maxlength="255" autocomplete="off" />
          <input type="password" id="promptbox_input_password" size="64" maxlength="255" autocomplete="off" />
          <br /><br />
          <button type="submit" title="{LNG_OK}">&nbsp;&nbsp;&nbsp;&nbsp;{LNG_OK}&nbsp;&nbsp;&nbsp;&nbsp;</button>
          &nbsp;&nbsp;&nbsp;
          <button type="button" title="{LNG_CANCEL}" onclick="hidePromptBox(false)">&nbsp;{LNG_CANCEL}&nbsp;</button>
        </td>
    </table>
  </form>
</div>