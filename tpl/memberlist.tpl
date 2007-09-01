<PCPIN:TPL name="main">
<form action="." method="post" onsubmit="getMemberlist(null, null, null, $('nickname_search').value); return false;">
<table class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" id="members_tbl" style="display:none">
  <tr>
    <td colspan="4" class="tbl_header_main">
      {LNG_MEMBERLIST} &bull; <span id="title_postfix"></span>
    </td>
  </tr>
  <tr>
    <td class="tbl_header_sub">
      {LNG_USER}
      <img onclick="getMemberlist(1, 1, 0)" src="./pic/arrow_up_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_ASCENDING}" title="{LNG_SORT_ASCENDING}" />
      <img onclick="getMemberlist(1, 1, 1)" src="./pic/arrow_down_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_DESCENDING}" title="{LNG_SORT_DESCENDING}" />
    </td>
    <td class="tbl_header_sub" id="registration_date_col" style="text-align:center">
      {LNG_REGISTRATION_DATE}
      <img onclick="getMemberlist(1, 2, 0)" src="./pic/arrow_up_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_ASCENDING}" title="{LNG_SORT_ASCENDING}" />
      <img onclick="getMemberlist(1, 2, 1)" src="./pic/arrow_down_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_DESCENDING}" title="{LNG_SORT_DESCENDING}" />
    </td>
    <td class="tbl_header_sub" id="last_visit_col" style="text-align:center">
      {LNG_LAST_VISIT}
      <img onclick="getMemberlist(1, 3, 0)" src="./pic/arrow_up_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_ASCENDING}" title="{LNG_SORT_ASCENDING}" />
      <img onclick="getMemberlist(1, 3, 1)" src="./pic/arrow_down_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_DESCENDING}" title="{LNG_SORT_DESCENDING}" />
    </td>
    <td class="tbl_header_sub" id="time_online_col" style="text-align:center">
      {LNG_TIME_SPENT_ONLINE}
      <img onclick="getMemberlist(1, 5, 0)" src="./pic/arrow_up_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_ASCENDING}" title="{LNG_SORT_ASCENDING}" />
      <img onclick="getMemberlist(1, 5, 1)" src="./pic/arrow_down_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_DESCENDING}" title="{LNG_SORT_DESCENDING}" />
    </td>

    <td class="tbl_header_sub" id="banned_by_col" style="display:none;text-align:center;">
      {LNG_BANNED_BY}
    </td>
    <td class="tbl_header_sub" id="banned_until_col" style="display:none;text-align:center;">
      {LNG_BANNED_UNTIL_HEADER}
    </td>
    <td class="tbl_header_sub" id="banned_reason_col" style="display:none;text-align:center;">
      {LNG_REASON}
    </td>

    <td class="tbl_header_sub" id="muted_by_col" style="display:none;text-align:center;">
      {LNG_MUTED_BY}
    </td>
    <td class="tbl_header_sub" id="muted_until_col" style="display:none;text-align:center;">
      {LNG_MUTED_UNTIL}
    </td>
    <td class="tbl_header_sub" id="muted_reason_col" style="display:none">
      {LNG_REASON}
    </td>

    <td class="tbl_header_sub" id="moderated_categories_col" style="display:none;text-align:center;">
      {LNG_MODERATED_CATEGORIES}
    </td>
    <td class="tbl_header_sub" id="moderated_rooms_col" style="display:none;text-align:center;">
      {LNG_MODERATED_ROOMS}
    </td>
    <td class="tbl_header_sub" id="moderator_edit_col" style="display:none;text-align:center;">
      {LNG_EDIT_MODERATOR}
    </td>

    <td class="tbl_header_sub" id="not_activated_empty_col" colspan="2" style="display:none;text-align:center;">
      &nbsp;
    </td>

  </tr>
  <tr>
    <td colspan="4" class="tbl_row">
      <b>{LNG_SEARCH_FOR_USER}:</b>
      <input type="text" id="nickname_search" title="{LNG_NICKNAME}" size="32" maxlength="255" autocomplete="off" />
      &nbsp;
      <button type="submit" title="{LNG_SEARCH}" id="memberlist_search_button">{LNG_SEARCH}</button>
      <PCPIN:TPL name="admin_filter_options" type="simplecondition" requiredvars="DISPLAY">
        <br />
        <label for="all_members" title="{LNG_ALL_MEMBERS}">
          <input type="radio" name="filter_groups" {ALL_MEMBERS_CHECKED} id="all_members" title="{LNG_ALL_MEMBERS}" />
          {LNG_ALL_MEMBERS}
        </label>
        &nbsp;&nbsp;
        <label for="banned_only" title="{LNG_BANNED_ONLY}">
          <input type="radio" name="filter_groups" {BANNED_MEMBERS_CHECKED} id="banned_only" title="{LNG_BANNED_ONLY}" />
          {LNG_BANNED_ONLY}
        </label>
        &nbsp;&nbsp;
        <label for="muted_only" title="{LNG_MUTED_ONLY}">
          <input type="radio" name="filter_groups" {MUTED_MEMBERS_CHECKED} id="muted_only" title="{LNG_MUTED_ONLY}" />
          {LNG_MUTED_ONLY}
        </label>
        &nbsp;&nbsp;
        <label for="moderators_only" title="{LNG_MODERATORS_ONLY}">
          <input type="radio" name="filter_groups" {MODERATOR_MEMBERS_CHECKED} id="moderators_only" title="{LNG_MODERATORS_ONLY}" />
          {LNG_MODERATORS_ONLY}
        </label>
        &nbsp;&nbsp;
        <label for="admins_only" title="{LNG_ADMINS_ONLY}">
          <input type="radio" name="filter_groups" {ADMIN_MEMBERS_CHECKED} id="admins_only" title="{LNG_ADMINS_ONLY}" />
          {LNG_ADMINS_ONLY}
        </label>
        <PCPIN:TPL name="admin_filter_options_not_activated" type="simplecondition" requiredvars="DISPLAY">
          &nbsp;&nbsp;
          <label for="not_activated_only" title="{LNG_NOT_ACTIVATED_ACCOUNTS}">
            <input type="radio" name="filter_groups" id="not_activated_only" title="{LNG_NOT_ACTIVATED_ACCOUNTS}" />
            {LNG_NOT_ACTIVATED_ACCOUNTS}
          </label>
        </PCPIN:TPL>
      </PCPIN:TPL>
    </td>
  </tr>
</table>
<span id="page_numbers" style="display:none"></span>
</form>

</PCPIN:TPL>