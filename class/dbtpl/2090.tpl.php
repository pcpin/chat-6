<?php
// Increase failed logins counter
// Used in: PCPIN_Failed_Login->increaseCounter()
$query='INSERT INTO `'.PCPIN_DB_PREFIX.'failed_login` ( `ip`, `count` ) VALUES ( "\\_arg1_\\", 1 ) ON DUPLICATE KEY UPDATE `count` = `count` + 1';
?>