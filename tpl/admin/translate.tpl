<PCPIN:TPL name="main">
<table id="start_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
  <tr>
    <td colspan="4" class="tbl_header_main">
      <b>{LNG_LANGUAGES} &bull; {LNG_TRANSLATE}</b>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="text-align:center">
      <br />
      <b>{LNG_PLEASE_SELECT}:</b>
      <br /><br />
      <button type="button" title="{LNG_EDIT_TRANSLATION}" onclick="showLanguageSelectionPage()">{LNG_EDIT_TRANSLATION}</button>
      &nbsp;&nbsp;&nbsp;
      <button type="button" title="{LNG_CREATE_NEW_TRANSLATION}" onclick="showNewTranslationPage()">{LNG_CREATE_NEW_TRANSLATION}</button>
      <br /><br /><br />
    </td>
  </tr>
</table>

<table id="select_language_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
  <tr>
    <td class="tbl_header_main">
      <b>{LNG_LANGUAGES} &bull; {LNG_TRANSLATE}</b>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="text-align:center">
      <b>{LNG_SELECT_LANGUAGE_TO_EDIT}:</b>
      &nbsp;&nbsp;
      <select id="translation_select_language" title="{LNG_SELECT_LANGUAGE_TO_EDIT}"></select>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="text-align:center">
      <button type="button" title="{LNG_TRANSLATE}" onclick="loadLngExpressions($('translation_select_language').value)">{LNG_OK}</button>
      &nbsp;&nbsp;
      <button type="button" title="{LNG_CANCEL}" onclick="showStartPage()">{LNG_CANCEL}</button>
    </td>
  </tr>
</table>

<form action=":" onsubmit="saveEditTranslationPage(); return false;" method="post">
  <table id="edit_expressions_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
    <tr>
      <td class="tbl_header_main" colspan="2">
        <b>{LNG_LANGUAGES} &bull; {LNG_TRANSLATE}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_header_sub" colspan="2">
        <b>{LNG_LANGUAGE}:</b>
        <span id="edit_expressions_lng_name"></span>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_CODE}</b>
      </td>
      <td class="tbl_row">
        <b>{LNG_VALUE}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" style="text-align:center;" colspan="2">
        <button type="submit" title="{LNG_SAVE}">{LNG_SAVE}</button>
        &nbsp;&nbsp;
        <button type="submit" id="save_and_next_page_btn" title="{LNG_SAVE_AND_NEXT_PAGE}" onclick="currentStartFrom+=maxExpressions">{LNG_SAVE_AND_NEXT_PAGE}</button>
        <br /><br />
        <button type="reset" title="{LNG_RESET_FORM}">{LNG_RESET_FORM}</button>
      </td>
    </tr>
    <tr>
      <td id="page_numbers" class="tbl_row" style="text-align:right" colspan="2">>
      </td>
    </tr>
  </table>
</form>

<table id="start_translation_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
  <tr>
    <td class="tbl_header_main" colspan="2">
      <b>{LNG_LANGUAGES} &bull; {LNG_CREATE_NEW_TRANSLATION}</b>
      <input type="hidden" id="edit_language_id" value="" />
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_TRANSLATE_FROM}:</b>
    </td>
    <td class="tbl_row">
      <select id="start_translation_translate_from" title="{LNG_TRANSLATE_FROM}: {LNG_LANGUAGE_NAME}"></select>
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_TRANSLATE_TO}:</b>
    </td>
    <td class="tbl_row">
      <select id="start_translation_translate_to" title="{LNG_TRANSLATE_TO}: {LNG_LANGUAGE_NAME}"></select>
    </td>
  </tr>
  <tr>
    <td class="tbl_row" style="text-align:center" colspan="2">
      <button type="button" title="{LNG_CREATE_NEW_TRANSLATION}" onclick="copyLanguage($('start_translation_translate_from').value, $('start_translation_translate_to').value)">{LNG_CREATE_NEW_TRANSLATION}</button>
      &nbsp;&nbsp;&nbsp;
      <button type="button" title="{LNG_CANCEL}" onclick="showStartPage()">{LNG_CANCEL}</button>
    </td>
  </tr>
</table>

</PCPIN:TPL>