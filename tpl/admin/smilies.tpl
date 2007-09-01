<PCPIN:TPL name="main">
  <table id="smilies_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
    <tr>
      <td colspan="4" class="tbl_header_main">
        <b>{LNG_SMILIES}</b>
      </td>
    </tr>
  </table>
  <br />
  <table id="new_smilie_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
    <tr>
      <td class="tbl_header_main" colspan="2">
        <b>{LNG_ADD_NEW_SMILIE}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" width="50%" style="text-align:right">
        <b>{LNG_SMILIE_IMAGE}:</b>
      </td>
      <td class="tbl_row">
        <span id="smilie_image" title=""></span>
        <button type="button" id="upload_image_link" onclick="showSmilieImageUploadForm()" title="{LNG_UPLOAD_NEW_IMAGE}">{LNG_UPLOAD_NEW_IMAGE}</button>
        <br />
        <a href=":" id="delete_image_link" onclick="deleteSmilieImage(); return false;" title="{LNG_DELETE_IMAGE}" style="display:none">{LNG_DELETE_IMAGE}</a>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" width="50%" style="text-align:right">
        <b>{LNG_CODE}:</b>
      </td>
      <td class="tbl_row">
        <input id="new_smilie_code" title="{LNG_CODE}" size="6" maxlength="32" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" style="text-align:right">
        <b>{LNG_DESCRIPTION}:</b>
      </td>
      <td class="tbl_row">
        <input id="new_smilie_description" title="{LNG_DESCRIPTION}" size="16" maxlength="255" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" colspan="2" style="text-align:center">
        <button type="button" onclick="addNewSmilie()" title="{LNG_ADD_NEW_SMILIE}">{LNG_ADD_NEW_SMILIE}</button>
      </td>
    </tr>
  </table>
</PCPIN:TPL>