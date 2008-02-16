<?php
// Check wether email address already in use or not
// Used in: PCPIN_User->checkEmailUnique()
$where='';
if (!empty($argv[1])) {
  $where.=' AND `us`.`id` != "\\_arg1_\\"';
}
$query='SELECT 1 FROM `'.PCPIN_DB_PREFIX.'user` `us`
                WHERE (`us`.`email` LIKE "\\_arg2_\\" OR `us`.`email_new` LIKE "\\_arg2_\\")
                      '.$where.'
                      LIMIT 1';
?>