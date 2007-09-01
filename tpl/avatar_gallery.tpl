<PCPIN:TPL name="main">
<table id="avatar_gallery_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%">
  <tr>
    <td class="tbl_header_main" colspan="{HEADER_COLSPAN}">
      {LNG_AVATAR_GALLERY}
    </td>
  </tr>
  <PCPIN:TPL name="avatar_gallery_row">
    <tr>
      <PCPIN:TPL name="avatar_gallery_col" type="condition" conditionvar="BINARYFILE_ID">
        <PCPIN:SUB condition="empty">
          <td class="tbl_row" width="{WIDTH}">
            <img src="./pic/clearpixel.gif" alt="" width="1px" height="1px" />
          </td>
        </PCPIN:SUB>
        <PCPIN:SUB condition="default">
          <td class="tbl_row" width="{WIDTH}" style="text-align:center;vertical-align:middle;">
            <img id="gallery_avatar_{ID}" src="{FORMLINK}?b_id={BINARYFILE_ID}&amp;b_x=100&amp;b_y=85" alt="{LNG_PICK_THIS_AVATAR}" title="{LNG_PICK_THIS_AVATAR}" onclick="pickAvatar(this)" style="cursor:pointer" />
          </td>
        </PCPIN:SUB>
      </PCPIN:TPL>
    </tr>
  </PCPIN:TPL>
</table>

<br />
<table border="0" cellspacing="0" cellpadding="0" width="100%">
  <tr>
    <td style="text-align:center">
      <button type="button" title="{LNG_CLOSE_WINDOW}" onclick="window.close()">{LNG_CLOSE_WINDOW}</button>
    </td>
  </tr>
</table>
</PCPIN:TPL>