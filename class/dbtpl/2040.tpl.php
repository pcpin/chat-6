<?php
// Delete old message logs
// Used in: PCPIN_Message_Log->cleanUp()
$query='DELETE `ml`, `la`
          FROM `'.PCPIN_DB_PREFIX.'message_log` `ml`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'message_log_attachment` `la` ON `la`.`message_id` = `ml`.`message_id`
         WHERE `ml`.`date` <= "\\_ARG1_\\"';
?>