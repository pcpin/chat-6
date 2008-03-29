<?php
// Insert new cache record or update existing one
// Used in: PCPIN_DB->_db_addCacheRecord()
$query='INSERT INTO `'.PCPIN_DB_PREFIX.'cache` ( `id`, `contents` ) VALUES ( "\\_ARG1_\\", "\\_ARG2_\\" ) ON DUPLICATE KEY UPDATE `contents` = "\\_ARG2_\\"';
?>