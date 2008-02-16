<?php
// Update chat setting
// Used in: PCPIN_Config->_conf_updateSettings()
$query='UPDATE `'.PCPIN_DB_PREFIX.'config` SET `_conf_value` = "\\_ARG2_\\" WHERE `_conf_name` = BINARY "\\_ARG1_\\" LIMIT 1';
?>