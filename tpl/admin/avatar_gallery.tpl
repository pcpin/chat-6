<PCPIN:TPL name="main">
  <table id="avatars_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
    <tr>
      <td colspan="4" class="tbl_header_main">
        <b>{LNG_AVATAR_GALLERY}</b>
      </td>
    </tr>
  </table>
  <br />
  <table id="new_avatar_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
    <tr>
      <td class="tbl_header_main" colspan="2">
        <b>{LNG_ADD_NEW_AVATAR}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" width="50%" style="text-align:right">
        <b>{LNG_AVATAR_IMAGE}:</b>
      </td>
      <td class="tbl_row">
        <span id="avatar_image" title=""></span>
        <button type="button" id="upload_image_link" onclick="showAvatarImageUploadForm()" title="{LNG_UPLOAD_NEW_IMAGE}">{LNG_UPLOAD_NEW_IMAGE}</button>
        <br />
        <a href=":" id="delete_image_link" onclick="deleteAvatarImage(); return false;" title="{LNG_DELETE_IMAGE}" style="display:none">{LNG_DELETE_IMAGE}</a>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" colspan="2" style="text-align:center">
        <button type="button" onclick="addNewAvatar()" title="{LNG_ADD_NEW_AVATAR}">{LNG_ADD_NEW_AVATAR}</button>
      </td>
    </tr>
  </table>
</PCPIN:TPL>