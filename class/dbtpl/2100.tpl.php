<?php
// Get sessions which must be deleted
// Used in: PCPIN_Session->_s_cleanUp()
$query='SELECT DISTINCT * FROM `'.PCPIN_DB_PREFIX.'session` WHERE

                          /* Old backend sessions (older than 30 minutes) */
                              `_s_backend` = "y"
                          AND `_s_last_ping` < "\\_ARG1_\\"

                     OR

                          /* Old sessions */
                              `_s_backend` = "n"
                          AND `_s_last_ping` < "\\_ARG2_\\"

                     OR

                          /* Sessions of users who have closed the browser without logging out */
                              `_s_page_unloaded` = "y"
                          AND `_s_last_ping` < "\\_ARG3_\\"

                     OR

                          /* "kicked out" sessions */
                              `_s_kicked` = "y"
';
?>