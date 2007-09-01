<PCPIN:TPL name="main">
  <table border="0" cellspacing="1" cellpadding="0" width="100%">
    <tr valign="middle">
      <td align="left" width="1">
        <a href="{FORMLINK}?external_url=http%3A%2F%2Fwww.pcpin.com" title="Powered by PCPIN Chat" target="_blank" onfocus="blur()">
          <img src="./pic/pcpin_chat_logo_223_69.jpg" border="0" alt="Powered by PCPIN Chat" title="Powered by PCPIN Chat" />
        </a>
      </td>
      <td align="center">
        <b>{CHAT_NAME} &bull; {LNG_ADMINISTRATION_AREA}</b>
        <br />
        {WELCOME_MSG}
        <PCPIN:TPL name="exit" type="condition" conditionvar="LOGOUT">
          <PCPIN:SUB condition="default">
            <a href=":" title="{LNG_LOG_OUT}" onclick="logOut(); return false;">
              <b>[{LNG_LOG_OUT}]</b>
            </a>
          </PCPIN:SUB>
          <PCPIN:SUB condition="empty">
            <a href=":" title="{LNG_CLOSE_WINDOW}" onclick="parent.window.close()">
              <b>[{LNG_CLOSE_WINDOW}]</b>
            </a>
          </PCPIN:SUB>
        </PCPIN:TPL>
      </td>
    </tr>
  </table>
</PCPIN:TPL>