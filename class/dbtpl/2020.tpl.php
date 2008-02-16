<?php
// Calculates total online time (in seconds), including current session
// Used in: PCPIN_User->calculateOnlineTime()
$query='SELECT `us`.`time_online` + COALESCE( UNIX_TIMESTAMP() - UNIX_TIMESTAMP(`se`.`_s_created`), 0 ) AS `time_online_total`
          FROM `'.PCPIN_DB_PREFIX.'user` `us`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `se` ON `se`.`_s_user_id` = `us`.`id`
         WHERE `us`.`id` = "\\_ARG1_\\"
               LIMIT 1';
?>