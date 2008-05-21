<PCPIN:TPL name="main">
<table class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%">
  <tr>
    <td class="tbl_header_main" colspan="3">
      <b>{LNG_EDIT_MODERATOR}</b>
    </td>
  </tr>
  <tr id="search_user_row" style="display:none">
    <td class="tbl_row" style="text-align:center" colspan="3">
      <form action=":" id="moderator_search_form" onsubmit="moderatorSearchUser(); return false;">
        <br /><br />
        <b>{LNG_SEARCH_FOR_USER}:</b>
        <input type="text" id="nickname_search" title="{LNG_NICKNAME}" size="32" maxlength="255" autocomplete="off" />
        &nbsp;
        <button type="submit" title="{LNG_SEARCH}">{LNG_SEARCH}</button>
        <br /><br /><br />
      </form>
    </td>
  </tr>
  <tr id="search_results" style="display:none">
    <td class="tbl_row" id="search_results_col_0" style="vertical-align:top" width="33%"></td>
    <td class="tbl_row" id="search_results_col_1" style="vertical-align:top" width="33%"></td>
    <td class="tbl_row" id="search_results_col_2" style="vertical-align:top" width="33%"></td>
  </tr>
  <tr id="user_name_row" style="display:none">
    <td class="tbl_row" colspan="3" style="text-align:center">
      <input type="hidden" name="moderator_user_id" id="moderator_user_id" value="" />
      <b>
        {LNG_USER}:
        <span id="moderator_user_nick"></span>
      </b>
    </td>
  </tr>
  <tr id="categories_rooms_row" style="display:none">
    <td class="tbl_row" style="text-align:center" colspan="3">
      <b>{LNG_MODERATED_CATEGORIES} / {LNG_MODERATED_ROOMS}:</b>
      <table border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td class="tbl_row">
            <div id="categories_and_rooms" class="div_selection_scrollable" style="text-align:left;width:500px;" title="{LNG_CHAT_CATEGORIES} / {LNG_CHAT_ROOMS}"><br /></div>
          </td>
        </tr>
      </table>
      <br />
      <button type="button" title="{LNG_SAVE_CHANGES}" onclick="saveModerator()">{LNG_SAVE_CHANGES}</button>
      &nbsp;
      <button type="button" title="{LNG_CANCEL}" onclick="hideModeratorForm()">{LNG_CANCEL}</button>
      <span id="close_window_span" style="display:none">
        <br /><br />
        <button type="button" title="{LNG_CLOSE_WINDOW}" onclick="window.close()">{LNG_CLOSE_WINDOW}</button>
      </span>
    </td>
  </tr>
</table>
</PCPIN:TPL>