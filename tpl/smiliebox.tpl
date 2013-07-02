<div id="smilie_selection_box" style="display:none">
  <table border="0" cellspacing="0" cellpadding="0" id="smiliebox_header">
    <PCPIN:TPL name="smiliebox_header_row" type="simplecondition" requiredvars="HEADER_ROW_COLSPAN">
      <tr>
        <td style="padding:0px;margin:0px" colspan="{HEADER_ROW_COLSPAN}">
          <table border="0" cellspacing="0" cellpadding="0" width="100%">
            <tr>
              <td class="tbl_header_main" onmousedown="startDragNDrop('smilie_selection_box')" onmouseup="stopDragNDrop('smilie_selection_box')" style="cursor:move">
                {LNG_SMILIES}
              </td>
              <td class="tbl_header_main" style="text-align:left;width:1%;padding-left:0px;">
                <button type="button" style="width:13px;height:13px;background-image:url(./pic/close_13x13.gif);padding:0px;margin:0px;" title="{LNG_CLOSE_WINDOW}" onclick="closeSmilieBox()"></button>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </PCPIN:TPL>
  </table>
  <div id="smiliebox_container" style="text-align:left">
    <table border="0" cellspacing="0" cellpadding="0">
      <PCPIN:TPL name="smiliebox_row">
        <tr>
          <PCPIN:TPL name="smiliebox_col">
            <td style="text-align:center;vertical-align:middle;padding-left:{PADDING_LEFT}px;padding-right:{PADDING_RIGHT}px;padding-top:{PADDING_TOP}px;padding-bottom:{PADDING_BOTTOM}px;"><img src="./pic/clearpixel_1x1.gif" alt="{CODE}" title="{DESCRIPTION}" name="{FORMLINK}?b_id={BINARYFILE_ID}" id="smilie_image_{ID}" style="cursor:pointer"></td>
          </PCPIN:TPL>
        </tr>
      </PCPIN:TPL>
    </table>
  </div>
</div>
<img id="drag_smilie" src="./pic/clearpixel_1x1.gif" alt="" title="" style="display:none;position:absolute;" />
