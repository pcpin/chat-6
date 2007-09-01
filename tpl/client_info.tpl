<PCPIN:TPL name="main">
<table class="tbl" border="0" cellspacing="1" cellpadding="0" width="100%" id="client_table" style="display:none">
  <tr>
    <td colspan="3" class="tbl_header_main">
      {LNG_CLIENT_INFO}
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_IP_ADDRESS}:</b>
    </td>
    <td class="tbl_row">
      <span id="client_ip"></span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_PING}:</b>
    </td>
    <td class="tbl_row">
      <span id="client_ping"></span>
      <button type="button" onclick="getPing(htmlspecialchars_decode($('client_ip').innerHTML))" title="{LNG_PING}">{LNG_PING}</button>
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_HOST_NAME}:</b>
    </td>
    <td class="tbl_row">
      <span id="client_host"></span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_BROWSER}:</b>
    </td>
    <td class="tbl_row">
      <span id="client_agent"></span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_OPERATING_SYSTEM}:</b>
    </td>
    <td class="tbl_row">
      <span id="client_os"></span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_USED_LANGUAGE}:</b>
    </td>
    <td class="tbl_row">
      <span id="client_language"></span>
    </td>
  </tr>
  <tr>
    <td class="tbl_row">
      <b>{LNG_SESSION_START_TIME}:</b>
    </td>
    <td class="tbl_row">
      <span id="client_session_start"></span>
    </td>
  </tr>
</table>
</PCPIN:TPL>