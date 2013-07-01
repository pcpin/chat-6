<PCPIN:TPL name="main">
  <table id="ip_table" class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%">
    <tr>
      <td colspan="7" class="tbl_header_main">
        <b>{LNG_IP_FILTER}</b>
      </td>
    </tr>
    <tr id="ip_list_header">
      <td class="tbl_header_sub" width="1%">&nbsp;</td>
      <td class="tbl_header_sub">
        {LNG_TYPE}
        <img onclick="sort_by=5; sort_dir=0; getFilteredIPAddresses();" src="./pic/arrow_up_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_ASCENDING}" title="{LNG_SORT_ASCENDING}" border="0" />
        <img onclick="sort_by=5; sort_dir=1; getFilteredIPAddresses();" src="./pic/arrow_down_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_DESCENDING}" title="{LNG_SORT_DESCENDING}" border="0" />
      </td>
      <td class="tbl_header_sub">
        {LNG_ADDRESS_MASK}
        <img onclick="sort_by=0; sort_dir=0; getFilteredIPAddresses();" src="./pic/arrow_up_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_ASCENDING}" title="{LNG_SORT_ASCENDING}" border="0" />
        <img onclick="sort_by=0; sort_dir=1; getFilteredIPAddresses();" src="./pic/arrow_down_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_DESCENDING}" title="{LNG_SORT_DESCENDING}" border="0" />
      </td>
      <td class="tbl_header_sub">
        {LNG_ACTION}
        <img onclick="sort_by=1; sort_dir=0; getFilteredIPAddresses();" src="./pic/arrow_up_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_ASCENDING}" title="{LNG_SORT_ASCENDING}" border="0" />
        <img onclick="sort_by=1; sort_dir=1; getFilteredIPAddresses();" src="./pic/arrow_down_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_DESCENDING}" title="{LNG_SORT_DESCENDING}" border="0" />
      </td>
      <td class="tbl_header_sub">
        {LNG_ADDED_ON}
        <img onclick="sort_by=3; sort_dir=0; getFilteredIPAddresses();" src="./pic/arrow_up_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_ASCENDING}" title="{LNG_SORT_ASCENDING}" border="0" />
        <img onclick="sort_by=3; sort_dir=1; getFilteredIPAddresses();" src="./pic/arrow_down_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_DESCENDING}" title="{LNG_SORT_DESCENDING}" border="0" />
      </td>
      <td class="tbl_header_sub">
        {LNG_EXPIRES}
        <img onclick="sort_by=2; sort_dir=0; getFilteredIPAddresses();" src="./pic/arrow_up_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_ASCENDING}" title="{LNG_SORT_ASCENDING}" border="0" />
        <img onclick="sort_by=2; sort_dir=1; getFilteredIPAddresses();" src="./pic/arrow_down_13x9.gif" name="img_hover" style="cursor:pointer" alt="{LNG_SORT_DESCENDING}" title="{LNG_SORT_DESCENDING}" border="0" />
      </td>
      <td class="tbl_header_sub">
        {LNG_COMMENTS}
      </td>
    </tr>
    <tr id="ip_list_empty">
      <td class="tbl_row" colspan="6">
        <button type="button" onclick="deleteSelectedAddresses()" title="{LNG_DELETE_SELECTED}">{LNG_DELETE_SELECTED}</button>
      </td>
    </tr>
  </table>
  <br />
  <table class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%">
    <tr>
      <td class="tbl_header_sub" colspan="2">
        <b>{LNG_FILTER_ADD_NEW_IP_ADDRESS}</b>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_TYPE}:</b>
      </td>
      <td class="tbl_row">
        <select id="new_ip_type" title="{LNG_TYPE}" onchange="setIpMaskVisiblility(this.value)">
          <option value="IPv4">IPv4</option>
          <option value="IPv6">IPv6</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_ACTION}:</b>
      </td>
      <td class="tbl_row">
        <select id="new_ip_action" title="{LNG_ACTION}">
          <option value="a">{LNG_ALLOW}</option>
          <option value="d">{LNG_DENY}</option>
        </select>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_ADDRESS_MASK}:</b>
        <div id="ipv4_mask_rules">
          {LNG_IP_MASK_RULES}
        </div>
      </td>
      <td class="tbl_row">
        <span id="new_ip_mask_ipv4">
          <input id="new_ip_mask_0" title="{LNG_ADDRESS_MASK}" size="2" maxlength="3" />
          .
          <input id="new_ip_mask_1" title="{LNG_ADDRESS_MASK}" size="2" maxlength="3" />
          .
          <input id="new_ip_mask_2" title="{LNG_ADDRESS_MASK}" size="2" maxlength="3" />
          .
          <input id="new_ip_mask_3" title="{LNG_ADDRESS_MASK}" size="2" maxlength="3" />
        </span>
        <span id="new_ip_mask_ipv6" style="display:none">
          <input id="new_ip_mask_ipv6_0" title="{LNG_ADDRESS_MASK}" size="30" maxlength="45" />
        </span>
      </td>
    </tr>
    <tr>
      <td class="tbl_row">
        <b>{LNG_EXPIRATION_DATE}:</b>
        <br />
        [{LNG_YEAR_SHORT}]-[{LNG_MONTH_SHORT}]-[{LNG_DAY_SHORT}] [{LNG_HOUR_SHORT}]:[{LNG_MINUTE_SHORT}]
      </td>
      <td class="tbl_row">
        <input id="new_ip_expires_year" title="{LNG_EXPIRATION_DATE}: {LNG_YEAR}" size="3" maxlength="4" />-<input id="new_ip_expires_month" title="{LNG_EXPIRATION_DATE}: {LNG_MONTH}" size="1" maxlength="2" />-<input id="new_ip_expires_day" title="{LNG_EXPIRATION_DATE}: {LNG_DAY}" size="1" maxlength="2" />
        &nbsp;
        <input id="new_ip_expires_hour" title="{LNG_EXPIRATION_DATE}: {LNG_HOUR}" size="1" maxlength="2" />:<input id="new_ip_expires_minute" title="{LNG_EXPIRATION_DATE}: {LNG_MINUTE}" size="1" maxlength="2" />
        <br />
        <label for="new_ip_expires_never" title="{LNG_EXPIRATION_DATE}: {LNG_NEVER}">
          <input type="checkbox" id="new_ip_expires_never" /> {LNG_NEVER}
        </label>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" style="vertical-align:top">
        <b>{LNG_COMMENTS}:</b>
      </td>
      <td class="tbl_row">
        <textarea rows="8" cols="60" title="{LNG_COMMENTS}" id="new_ip_description"></textarea>
      </td>
    </tr>
    <tr>
      <td class="tbl_row" colspan="2" style="text-align:center">
        <button type="button" onclick="addIPAddress()" title="{LNG_ADD_THIS_IP_TO_FILTER}">{LNG_ADD_THIS_IP_TO_FILTER}</button>
      </td>
    </tr>
  </table>
</PCPIN:TPL>