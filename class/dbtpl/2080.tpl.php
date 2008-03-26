<?php
// Clean unbanned users
// Used in: PCPIN_Session->_s_CleanUp()
$query='UPDATE `'.PCPIN_DB_PREFIX.'user`
           SET `banned_by` = 0,
               `banned_by_username` = "",
               `banned_until` = "0000-00-00 00:00:00",
               `ban_reason` = ""
         WHERE `banned_until` > "0000-00-00 00:00:00"
               AND `banned_until` < "\\_ARG1_\\"
               AND `banned_permanently` != "y"';
?>