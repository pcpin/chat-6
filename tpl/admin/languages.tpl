<PCPIN:TPL name="main">
<table id="languages_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
  <tr>
    <td colspan="4" class="tbl_header_main">
      <b>{LNG_LANGUAGES} &bull; {LNG_MANAGEMENT}</b>
    </td>
  </tr>
  <tr>
    <td class="tbl_header_sub" width="1%">
      &nbsp;
    </td>
    <td class="tbl_header_sub">
      <b>{LNG_LANGUAGE}</b>
    </td>
    <td class="tbl_header_sub" style="text-align:center">
      <b>{LNG_ACTIVE}</b>
    </td>
    <td class="tbl_header_sub" width="1%">
      <b>{LNG_DOWNLOAD}</b>
    </td>
  </tr>
  <tr>
    <td colspan="4" class="tbl_row" style="text-align:center">
      <button type="button" title="{LNG_ADD_NEW_LANGUAGE}" onclick="showNewLanguageForm()">{LNG_ADD_NEW_LANGUAGE}</button>
    </td>
  </tr>
</table>

<table id="new_language_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
  <tr>
    <td class="tbl_header_main">
      <b>{LNG_LANGUAGES} &bull; {LNG_ADD_NEW_LANGUAGE}</b>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="text-align:center">
      <br />
      <b>{LNG_PLEASE_SELECT}:</b>
      <br /><br />
      <button type="button" title="{LNG_UPLOAD_LANGUAGE_FILE}" onclick="showUploadWindow()">{LNG_UPLOAD_LANGUAGE_FILE}</button>
      &nbsp;&nbsp;&nbsp;
      <button type="button" title="{LNG_CREATE_NEW_TRANSLATION}" onclick="window.location.href=formlink+'?s_id='+urlencode(s_id)+'&ainc=translate&new_translation=1'">{LNG_CREATE_NEW_TRANSLATION}</button>
      <br /><br /><br />
      <button type="button" title="{LNG_CANCEL}" onclick="showLanguages()">{LNG_CANCEL}</button>
      <br /><br />
    </td>
  </tr>
</table>

<table id="edit_language_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
  <tr>
    <td class="tbl_header_main" colspan="2">
      <b>{LNG_LANGUAGES} &bull; {LNG_EDIT_LANGUAGE}</b>
      <input type="hidden" id="edit_language_id" value="" />
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_LANGUAGE_NAME}:</b>
    </td>
    <td class="tbl_row">
      <select id="edit_language_iso_name" title="{LNG_LANGUAGE_NAME}" onchange="$('edit_language_local_name').value=AvailableLanguageNames[this.value].Name"></select>
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_LOCAL_NAME}:</b>
    </td>
    <td class="tbl_row">
      <input type="text" id="edit_language_local_name" value="" title="{LNG_LOCAL_NAME}" size="30" maxlength="255" />
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_ACTIVE}:</b>
    </td>
    <td class="tbl_row">
      <label for="edit_language_active_y" title="{LNG_ACTIVE}: {LNG_YES}">
        <input type="radio" name="edit_language_active" id="edit_language_active_y" value="y" title="{LNG_ACTIVE}: {LNG_YES}" /> {LNG_YES}
      </label>
      &nbsp;&nbsp;&nbsp;
      <label for="edit_language_active_n" title="{LNG_ACTIVE}: {LNG_NO}">
        <input type="radio" name="edit_language_active" id="edit_language_active_n" value="n" title="{LNG_ACTIVE}: {LNG_NO}" /> {LNG_NO}
      </label>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" colspan="2" style="text-align:center">
      <button type="button" title="{LNG_SAVE_CHANGES}" onclick="saveLanguage()">{LNG_SAVE_CHANGES}</button>
      &nbsp;&nbsp;&nbsp;
      <button type="button" title="{LNG_CANCEL}" onclick="hideEditLanguageForm(); showLanguagesTable();">{LNG_CANCEL}</button>
    </td>
  </tr>
</table>

</PCPIN:TPL>