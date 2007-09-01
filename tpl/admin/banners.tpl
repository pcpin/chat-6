<PCPIN:TPL name="main">
  <table id="banners_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%">
    <tr>
      <td colspan="8" class="tbl_header_main">
        <b>{LNG_BANNERS}</b>
      </td>
    </tr>
    <tr id="banners_list_header" style="display:none">
      <td class="tbl_header_sub">
        <b>{LNG_BANNER_NAME}</b>
      </td>
      <td class="tbl_header_sub" style="text-align:center">
        <b>{LNG_ACTIVE}</b>
      </td>
      <td class="tbl_header_sub" style="text-align:center">
        <b>{LNG_BANNER_SOURCE}</b>
      </td>
      <td class="tbl_header_sub" style="text-align:center">
        <b>{LNG_DISPLAY_POSITION}</b>
      </td>
      <td class="tbl_header_sub" style="text-align:center">
        <b>{LNG_VIEWS} / {LNG_MAX_VIEWS}</b>
      </td>
      <td class="tbl_header_sub" style="text-align:center">
        <b>{LNG_START_DATE}</b>
      </td>
      <td class="tbl_header_sub" style="text-align:center">
        <b>{LNG_EXPIRATION_DATE}</b>
      </td>
    </tr>
    <tr id="new_banner_btn_row">
      <td colspan="8" class="tbl_row" style="text-align:center">
        <a href="{FORMLINK}?s_id={S_ID}&amp;ainc=settings&amp;group=banners" title="{LNG_GLOBAL_BANNER_SETTINGS}">{LNG_GLOBAL_BANNER_SETTINGS}</a>
        &nbsp;&nbsp;&nbsp;
        <a href=":" title="{LNG_ADD_NEW_BANNER}" onclick="showNewBannerForm(); return false;">
          <img src="./pic/plus_13x13.gif" name="img_hover" alt="{LNG_ADD_NEW_BANNER}" title="{LNG_ADD_NEW_BANNER}" />
          {LNG_ADD_NEW_BANNER}
        </a>
      </td>
    </tr>
  </table>

  <table id="new_banner_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
    <tr>
      <td colspan="6" class="tbl_header_main">
        <b>{LNG_BANNERS}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_header_sub" colspan="2">
        <b>{LNG_ADD_NEW_BANNER}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap">
        <b>{LNG_BANNER_NAME}:</b>
      </td>
      <td class="tbl_row">
        <input id="new_banner_name" type="text" title="{LNG_BANNER_NAME}" size="52" maxlength="255" autocomplete="off" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap">
        <b>{LNG_ACTIVE}:</b>
      </td>
      <td class="tbl_row">
        <label for="new_banner_active_y" title="{LNG_ACTIVE}: {LNG_YES}">
          <input type="radio" name="new_banner_active" id="new_banner_active_y" value="y"> {LNG_YES}
        </label>
        &nbsp;&nbsp;
        <label for="new_banner_active_n" title="{LNG_ACTIVE}: {LNG_NO}">
          <input type="radio" name="new_banner_active" id="new_banner_active_n" value="n"> {LNG_NO}
        </label>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap" style="vertical-align:top">
        <b>{LNG_BANNER_SOURCE}:</b>
      </td>
      <td class="tbl_row">
        <label for="new_banner_source_u" title="{LNG_BANNER_SOURCE}: {LNG_URL}">
          <input type="radio" name="new_banner_source" id="new_banner_source_u" value="u"> {LNG_URL}
        </label>
        &nbsp;&nbsp;
        <label for="new_banner_source_c" title="{LNG_BANNER_SOURCE}: {LNG_CUSTOM}">
          <input type="radio" name="new_banner_source" id="new_banner_source_c" value="c"> {LNG_CUSTOM}
        </label>
        <br />
        <span id="new_banner_source_url">
          <br />
          {LNG_URL}: <input type="text" id="new_banner_source_url_text" size="52" maxlength="255" title="{LNG_URL}" autocomplete="off" />
        </span>
        <span id="new_banner_source_custom">
          <br />
          {LNG_BANNER_SOURCE} (HTML):
          <br />
          <textarea id="new_banner_source_custom_text" rows="10" cols="52" title="{LNG_BANNER_SOURCE} (HTML)"></textarea>
        </span>
        <br />
        <button type="button" title="{LNG_PREVIEW}" onclick="showBannerPreview('new_banner')">{LNG_PREVIEW}</button>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap" style="vertical-align:top">
        <b>{LNG_DISPLAY_POSITION}:</b>
      </td>
      <td class="tbl_row">
        <label for="new_banner_display_position_t" title="{LNG_DISPLAY_POSITION}: {LNG_AT_WINDOW_TOP}">
          <input type="radio" name="new_banner_display_position" id="new_banner_display_position_t" value="t"> {LNG_AT_WINDOW_TOP}
        </label>
        <br />
        <label for="new_banner_display_position_b" title="{LNG_DISPLAY_POSITION}: {LNG_AT_WINDOW_BOTTOM}">
          <input type="radio" name="new_banner_display_position" id="new_banner_display_position_b" value="b"> {LNG_AT_WINDOW_BOTTOM}
        </label>
        <br />
        <label for="new_banner_display_position_p" title="{LNG_DISPLAY_POSITION}: {LNG_IN_POPUP_WINDOW}">
          <input type="radio" name="new_banner_display_position" id="new_banner_display_position_p" value="p"> {LNG_IN_POPUP_WINDOW}
        </label>
        <br />
        <label for="new_banner_display_position_m" title="{LNG_DISPLAY_POSITION}: {LNG_BETWEEN_MESSAGES}">
          <input type="radio" name="new_banner_display_position" id="new_banner_display_position_m" value="m"> {LNG_BETWEEN_MESSAGES}
        </label>
      </td>
    </tr>
    <tr id="new_banner_width_row">
      <td class="tbl_row" nowrap="nowrap">
        <b>{LNG_WIDTH}:</b>
      </td>
      <td class="tbl_row">
        <input id="new_banner_width" type="text" title="{LNG_WIDTH}" size="3" maxlength="5" autocomplete="off" />
        {LNG_PIXEL}
      </td>
    </tr>
    <tr id="new_banner_height_row">
      <td class="tbl_row" nowrap="nowrap">
        <b>{LNG_HEIGHT}:</b>
      </td>
      <td class="tbl_row">
        <input id="new_banner_height" type="text" title="{LNG_HEIGHT}" size="3" maxlength="5" autocomplete="off" />
        {LNG_PIXEL}
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap">
        <b>{LNG_MAX_VIEWS}:</b>
        <br />
        0: {LNG_UNLIMITED}
      </td>
      <td class="tbl_row">
        <input id="new_banner_max_views" type="text" title="{LNG_MAX_VIEWS}" size="6" maxlength="12" autocomplete="off" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap" style="vertical-align:top">
        <b>{LNG_START_DATE}:</b>
        <br />
        [{LNG_YEAR_SHORT}]-[{LNG_MONTH_SHORT}]-[{LNG_DAY_SHORT}] [{LNG_HOUR_SHORT}]:[{LNG_MINUTE_SHORT}]
      </td>
      <td class="tbl_row">
        <input id="new_banner_start_date_year" title="{LNG_START_DATE}: {LNG_YEAR}" size="3" maxlength="4" />-<input id="new_banner_start_date_month" title="{LNG_START_DATE}: {LNG_MONTH}" size="1" maxlength="2" />-<input id="new_banner_start_date_day" title="{LNG_START_DATE}: {LNG_DAY}" size="1" maxlength="2" />
        &nbsp;
        <input id="new_banner_start_date_hour" title="{LNG_START_DATE}: {LNG_HOUR}" size="1" maxlength="2" />:<input id="new_banner_start_date_minute" title="{LNG_START_DATE}: {LNG_MINUTE}" size="1" maxlength="2" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap" style="vertical-align:top">
        <b>{LNG_EXPIRATION_DATE}:</b>
        <br />
        [{LNG_YEAR_SHORT}]-[{LNG_MONTH_SHORT}]-[{LNG_DAY_SHORT}] [{LNG_HOUR_SHORT}]:[{LNG_MINUTE_SHORT}]
      </td>
      <td class="tbl_row">
        <label for="new_banner_expiration_date_never" title="{LNG_EXPIRATION_DATE}: {LNG_NEVER}">
          <input type="checkbox" id="new_banner_expiration_date_never" /> {LNG_NEVER}
        </label>
        <br />
        <input id="new_banner_expiration_date_year" title="{LNG_EXPIRATION_DATE}: {LNG_YEAR}" size="3" maxlength="4" />-<input id="new_banner_expiration_date_month" title="{LNG_EXPIRATION_DATE}: {LNG_MONTH}" size="1" maxlength="2" />-<input id="new_banner_expiration_date_day" title="{LNG_EXPIRATION_DATE}: {LNG_DAY}" size="1" maxlength="2" />
        &nbsp;
        <input id="new_banner_expiration_date_hour" title="{LNG_EXPIRATION_DATE}: {LNG_HOUR}" size="1" maxlength="2" />:<input id="new_banner_expiration_date_minute" title="{LNG_EXPIRATION_DATE}: {LNG_MINUTE}" size="1" maxlength="2" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" colspan="2" style="text-align:center">
        <button type="button" onclick="addNewBanner()" title="{LNG_ADD_NEW_BANNER}">{LNG_ADD_NEW_BANNER}</button>
        &nbsp;&nbsp;
        <button type="button" onclick="hideNewBannerForm()" title="{LNG_CANCEL}">{LNG_CANCEL}</button>
      </td>
    </tr>
  </table>

  <table id="edit_banner_tbl" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" style="display:none">
    <tr>
      <td colspan="6" class="tbl_header_main">
        <b>{LNG_BANNERS}</b>
        <input type="hidden" id="edit_banner_id" value="" />
      </td>
    </tr>
    <tr>
      <td class="tbl_header_sub" colspan="2">
        <b><span id="edit_banner_name_title"></span></b>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap">
        <b>{LNG_BANNER_NAME}:</b>
      </td>
      <td class="tbl_row">
        <input id="edit_banner_name" type="text" title="{LNG_BANNER_NAME}" size="52" maxlength="255" autocomplete="off" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap">
        <b>{LNG_ACTIVE}:</b>
      </td>
      <td class="tbl_row">
        <label for="edit_banner_active_y" title="{LNG_ACTIVE}: {LNG_YES}">
          <input type="radio" name="edit_banner_active" id="edit_banner_active_y" value="y"> {LNG_YES}
        </label>
        &nbsp;&nbsp;
        <label for="edit_banner_active_n" title="{LNG_ACTIVE}: {LNG_NO}">
          <input type="radio" name="edit_banner_active" id="edit_banner_active_n" value="n"> {LNG_NO}
        </label>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap" style="vertical-align:top">
        <b>{LNG_BANNER_SOURCE}:</b>
      </td>
      <td class="tbl_row">
        <label for="edit_banner_source_u" title="{LNG_BANNER_SOURCE}: {LNG_URL}">
          <input type="radio" name="edit_banner_source" id="edit_banner_source_u" value="u"> {LNG_URL}
        </label>
        &nbsp;&nbsp;
        <label for="edit_banner_source_c" title="{LNG_BANNER_SOURCE}: {LNG_CUSTOM}">
          <input type="radio" name="edit_banner_source" id="edit_banner_source_c" value="c"> {LNG_CUSTOM}
        </label>
        <br />
        <span id="edit_banner_source_url">
          <br />
          {LNG_URL}: <input type="text" id="edit_banner_source_url_text" size="52" maxlength="255" title="{LNG_URL}" autocomplete="off" />
        </span>
        <span id="edit_banner_source_custom">
          <br />
          {LNG_BANNER_SOURCE} (HTML):
          <br />
          <textarea id="edit_banner_source_custom_text" rows="10" cols="52" title="{LNG_BANNER_SOURCE} (HTML)"></textarea>
        </span>
        <br />
        <button type="button" title="{LNG_PREVIEW}" onclick="showBannerPreview('edit_banner')">{LNG_PREVIEW}</button>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap" style="vertical-align:top">
        <b>{LNG_DISPLAY_POSITION}:</b>
      </td>
      <td class="tbl_row">
        <label for="edit_banner_display_position_t" title="{LNG_DISPLAY_POSITION}: {LNG_AT_WINDOW_TOP}">
          <input type="radio" name="edit_banner_display_position" id="edit_banner_display_position_t" value="t"> {LNG_AT_WINDOW_TOP}
        </label>
        <br />
        <label for="edit_banner_display_position_b" title="{LNG_DISPLAY_POSITION}: {LNG_AT_WINDOW_BOTTOM}">
          <input type="radio" name="edit_banner_display_position" id="edit_banner_display_position_b" value="b"> {LNG_AT_WINDOW_BOTTOM}
        </label>
        <br />
        <label for="edit_banner_display_position_p" title="{LNG_DISPLAY_POSITION}: {LNG_IN_POPUP_WINDOW}">
          <input type="radio" name="edit_banner_display_position" id="edit_banner_display_position_p" value="p"> {LNG_IN_POPUP_WINDOW}
        </label>
        <br />
        <label for="edit_banner_display_position_m" title="{LNG_DISPLAY_POSITION}: {LNG_BETWEEN_MESSAGES}">
          <input type="radio" name="edit_banner_display_position" id="edit_banner_display_position_m" value="m"> {LNG_BETWEEN_MESSAGES}
        </label>
        <br />
      </td>
    </tr>
    <tr id="edit_banner_width_row">
      <td class="tbl_row" nowrap="nowrap">
        <b>{LNG_WIDTH}:</b>
      </td>
      <td class="tbl_row">
        <input id="edit_banner_width" type="text" title="{LNG_WIDTH}" size="3" maxlength="5" autocomplete="off" />
        {LNG_PIXEL}
      </td>
    </tr>
    <tr id="edit_banner_height_row">
      <td class="tbl_row" nowrap="nowrap">
        <b>{LNG_HEIGHT}:</b>
      </td>
      <td class="tbl_row">
        <input id="edit_banner_height" type="text" title="{LNG_HEIGHT}" size="3" maxlength="5" autocomplete="off" />
        {LNG_PIXEL}
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap">
        <b>{LNG_MAX_VIEWS}:</b>
        <br />
        0: {LNG_UNLIMITED}
      </td>
      <td class="tbl_row">
        <input id="edit_banner_max_views" type="text" title="{LNG_MAX_VIEWS}" size="6" maxlength="12" autocomplete="off" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap" style="vertical-align:top">
        <b>{LNG_START_DATE}:</b>
        <br />
        [{LNG_YEAR_SHORT}]-[{LNG_MONTH_SHORT}]-[{LNG_DAY_SHORT}] [{LNG_HOUR_SHORT}]:[{LNG_MINUTE_SHORT}]
      </td>
      <td class="tbl_row">
        <input id="edit_banner_start_date_year" title="{LNG_START_DATE}: {LNG_YEAR}" size="3" maxlength="4" />-<input id="edit_banner_start_date_month" title="{LNG_START_DATE}: {LNG_MONTH}" size="1" maxlength="2" />-<input id="edit_banner_start_date_day" title="{LNG_START_DATE}: {LNG_DAY}" size="1" maxlength="2" />
        &nbsp;
        <input id="edit_banner_start_date_hour" title="{LNG_START_DATE}: {LNG_HOUR}" size="1" maxlength="2" />:<input id="edit_banner_start_date_minute" title="{LNG_START_DATE}: {LNG_MINUTE}" size="1" maxlength="2" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" nowrap="nowrap" style="vertical-align:top">
        <b>{LNG_EXPIRATION_DATE}:</b>
        <br />
        [{LNG_YEAR_SHORT}]-[{LNG_MONTH_SHORT}]-[{LNG_DAY_SHORT}] [{LNG_HOUR_SHORT}]:[{LNG_MINUTE_SHORT}]
      </td>
      <td class="tbl_row">
        <label for="edit_banner_expiration_date_never" title="{LNG_EXPIRATION_DATE}: {LNG_NEVER}">
          <input type="checkbox" id="edit_banner_expiration_date_never" /> {LNG_NEVER}
        </label>
        <br />
        <input id="edit_banner_expiration_date_year" title="{LNG_EXPIRATION_DATE}: {LNG_YEAR}" size="3" maxlength="4" />-<input id="edit_banner_expiration_date_month" title="{LNG_EXPIRATION_DATE}: {LNG_MONTH}" size="1" maxlength="2" />-<input id="edit_banner_expiration_date_day" title="{LNG_EXPIRATION_DATE}: {LNG_DAY}" size="1" maxlength="2" />
        &nbsp;
        <input id="edit_banner_expiration_date_hour" title="{LNG_EXPIRATION_DATE}: {LNG_HOUR}" size="1" maxlength="2" />:<input id="edit_banner_expiration_date_minute" title="{LNG_EXPIRATION_DATE}: {LNG_MINUTE}" size="1" maxlength="2" />
      </td>
    </tr>
    <tr>
      <td class="tbl_row" colspan="2" style="text-align:center">
        <button type="button" onclick="updateBanner()" title="{LNG_SAVE_CHANGES}">{LNG_SAVE_CHANGES}</button>
        &nbsp;&nbsp;
        <button type="button" onclick="hideEditBannerForm()" title="{LNG_CANCEL}">{LNG_CANCEL}</button>
      </td>
    </tr>
  </table>

</PCPIN:TPL>