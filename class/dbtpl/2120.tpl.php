<?php
// Get a record from cache
// Used in: PCPIN_DB->_db_getCacheRecord()
$query='SELECT `contents` FROM `'.PCPIN_DB_PREFIX.'cache` WHERE `id` = BINARY "\\_ARG1_\\" LIMIT 1';
?>