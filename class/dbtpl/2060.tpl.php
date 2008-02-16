<?php
// List users for pruning: idle users and timed-out not activated users
// Used in: PCPIN_Session->_s_cleanUp()
$where_not_activated=' 0 ';
$where_idle=' 0 ';
if (!empty($argv[1])) {
  $where_not_activated='`activated` = "n" AND `joined` < FROM_UNIXTIME( "\\_ARG1_\\" )';
}
if (!empty($argv[1])) {
  $where_idle='`activated` = "y" AND `is_admin` = "n" AND `moderated_rooms` = "" AND `moderated_categories` = "" AND ( `last_login` != "0000-00-00 00:00:00" AND `last_login` < FROM_UNIXTIME( "\\_ARG2_\\" ) OR `last_login` = "0000-00-00 00:00:00" AND `joined` < FROM_UNIXTIME( "\\_ARG2_\\" ) )';
}
$query='SELECT `id` FROM `'.PCPIN_DB_PREFIX.'user` WHERE '.$where_not_activated.' OR '.$where_idle;
?>