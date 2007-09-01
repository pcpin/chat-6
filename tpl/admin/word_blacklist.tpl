<PCPIN:TPL name="main">
  <table id="words_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%">
    <tr>
      <td colspan="3" class="tbl_header_main">
        <b>{LNG_WORD_BLACKLIST}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_header_sub">
        {LNG_WORD}
      </td>
      <td class="tbl_header_sub">
        {LNG_REPLACEMENT}
      </td>
      <td class="tbl_header_sub">
        &nbsp;
      </td>
    </tr>
  </table>
  <br />
  <table class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%">
    <tr>
      <td class="tbl_header_sub" colspan="2">
        <b>{LNG_FILTER_ADD_NEW_WORD}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_WORD}:</b>
      </td>
      <td class="tbl_row">
        <input id="new_word_word" title="{LNG_WORD}" size="32" maxlength="255" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_REPLACEMENT}:</b>
      </td>
      <td class="tbl_row">
        <input id="new_word_replacement" title="{LNG_REPLACEMENT}" size="32" maxlength="255" value="***" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" colspan="2" style="text-align:center">
        <button type="button" onclick="addBadWord()" title="{LNG_FILTER_ADD_NEW_WORD}">{LNG_FILTER_ADD_NEW_WORD}</button>
      </td>
    </tr>
  </table>
</PCPIN:TPL>