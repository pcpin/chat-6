<?php
// Get memberlist
// Used in: PCPIN_User->getMemberlist()
$select='';
$where='';
$groupby='';
$orderby='';
$orderdir='';
$limit='';
if (!empty($argv[1])) {
  // Count only
  $select=' COUNT( DISTINCT `us`.`id` ) AS `members` ';
  $argv[3]=0; // No limit needed
  $argv[4]=-1; // No sort needed
  $groupby='';
} else {
  // Full data
  $select='`us`.`id` AS `id`,
            UNIX_TIMESTAMP( `us`.`joined`) AS `joined`,
            IF( `us`.`activated` = "y", 1, 0 ) AS `activated`,
            UNIX_TIMESTAMP( `us`.`last_login`) AS `last_login`,
            `us`.`time_online` + IF( `se`.`_s_id` IS NOT NULL, UNIX_TIMESTAMP() - UNIX_TIMESTAMP( `se`.`_s_created` ), 0 ) AS `time_online`,
            IF( `curr_us`.`is_admin` = "y" OR `curr_us`.`id` = `us`.`id`, `se`.`_s_ip`, "" ) AS `ip_address`,
            IF( `curr_us`.`id` = `us`.`id` OR `curr_us`.`is_admin` = "y", `us`.`login`, "" ) AS `login`,
            IF( `curr_us`.`id` = `us`.`id` OR `curr_us`.`is_admin` = "y", `us`.`muted_users`, "" ) AS `muted_users`,
            COALESCE( `se`.`_s_online_status`, 0 ) AS `online_status`,
            COALESCE( `se`.`_s_online_status_message`, "" ) AS `online_status_message`,
            COALESCE( `nn`.`nickname`, `us`.`login` ) AS `nickname`,
            COALESCE( `nn`.`nickname_plain`, `us`.`login` ) AS `nickname_plain`,
            `us`.`hide_email` AS `hide_email`,
            IF( `us`.`hide_email` = "0" OR `curr_us`.`is_admin` = "y" OR `curr_us`.`id` = `us`.`id`, `us`.`email`, "" ) AS `email`,
            IF( `us`.`is_admin` = "y", 1, 0 ) AS `is_admin`,
            IF( `us`.`moderated_rooms` != "" OR `us`.`moderated_categories` != "", 1, 0 ) AS `is_moderator`,
            IF( `curr_us`.`is_admin` = "y" OR `curr_us`.`id` = `us`.`id`, `us`.`moderated_rooms`, "" ) AS `moderated_rooms`,
            IF( `curr_us`.`is_admin` = "y" OR `curr_us`.`id` = `us`.`id`, `us`.`moderated_categories`, "" ) AS `moderated_categories`,
            COALESCE( `av`.`binaryfile_id`, `av_def`.`binaryfile_id` ) AS `avatar_bid`,
            IF( FIND_IN_SET( `us`.`id`, `curr_us`.`muted_users` )>0, 1, 0 ) AS `muted_locally`,
            IF( `us`.`global_muted_until` > "'.date('Y-m-d').'" OR `us`.`global_muted_permanently` = "y", 1, 0 ) AS `global_muted`,
            IF( `us`.`global_muted_until` > "'.date('Y-m-d').'", UNIX_TIMESTAMP( `us`.`global_muted_until` ), 0 ) AS `global_muted_until`,
            `us`.`global_muted_reason` AS `global_muted_reason`,
            IF( `curr_us`.`is_admin` = "y" OR `curr_us`.`id` = `us`.`id`, `us`.`global_muted_by`, "" ) AS `global_muted_by`,
            IF( `curr_us`.`is_admin` = "y" OR `curr_us`.`id` = `us`.`id`, `us`.`global_muted_by_username`, "" ) AS `global_muted_by_username`,
            IF( `us`.`banned_until` > "'.date('Y-m-d').'" OR `us`.`banned_permanently` = "y", 1, 0 ) AS `banned`,
            IF( `us`.`banned_until` > "'.date('Y-m-d').'", UNIX_TIMESTAMP( `us`.`banned_until` ), 0 ) AS `banned_until`,
            `us`.`ban_reason` AS `ban_reason`,
            IF( `curr_us`.`is_admin` = "y" OR `curr_us`.`id` = `us`.`id`, `us`.`banned_by`, "" ) AS `banned_by`,
            IF( `curr_us`.`is_admin` = "y" OR `curr_us`.`id` = `us`.`id`, `us`.`banned_by_username`, "" ) AS `banned_by_username`,
            IF( `us`.`is_guest` = "y", 1, 0 ) AS `is_guest`,
            IF( `se`.`_s_backend` = "n" AND `curr_se`.`_s_room_id` != "0" AND `se`.`_s_room_id` != `curr_se`.`_s_room_id`, "1", "0" ) AS `invitable`
            ';
  $groupby=' GROUP BY `us`.`id` ';
}
if (!empty($argv[3])) {
  // LIMIT
  if (!empty($argv[2])) {
    $limit=' LIMIT \\_ARG2_\\, \\_ARG3_\\';
  } else {
    $limit=' LIMIT \\_ARG3_\\';
  }
}
if (!empty($argv[4])) {
  $orderdir=!empty($argv[5])? ' DESC ' : ' ASC ';
  // Sort by
  switch ($argv[4]) {

    case 1 :
      // Nickname
      $orderby=' ORDER BY `nickname_plain` '.$orderdir;
    break;

    case 2 :
      // Join date
      $orderby=' ORDER BY `joined` '.$orderdir.', `nickname_plain` ASC';
    break;

    case 3 :
      // Last login date
      $orderby=' ORDER BY `last_login` '.$orderdir.', `nickname_plain` ASC';
    break;

    case 4 :
      // Online stats
      $orderby=' ORDER BY `online_status` '.$orderdir.', `nickname_plain` ASC';
    break;

    case 5 :
      // Time spent online
      $orderby=' ORDER BY `time_online` '.$orderdir.', `nickname_plain` ASC';
    break;

  }
}
if (!empty($argv[6])) {
  // Nickname
  $where.=' AND ( `nn`.`nickname_plain` LIKE "%\\_arg6_\\%" OR `nn`.`nickname_plain` IS NULL AND `us`.`login` LIKE "%\\_arg6_\\%" )';
}
if (isset($argv[7])) {
  // ID of current user
  $argv[7]*=1;
} else {
  $argv[7]=0;
}
if (isset($argv[8]) && true===$argv[8]) {
  // Banned users only
  $where.=' AND (`us`.`banned_until` > "'.date('Y-m-d').'" OR `us`.`banned_permanently` = "y")';
}
if (isset($argv[9]) && true===$argv[9]) {
  // Muted users only
  $where.=' AND (`us`.`global_muted_until` > "'.date('Y-m-d').'" OR `us`.`global_muted_permanently` = "y")';
}
if (isset($argv[10]) && true===$argv[10]) {
  // Moderators only
  $where.=' AND ( `us`.`moderated_rooms` != "" OR `us`.`moderated_categories` != "" )';
}
if (isset($argv[11]) && true===$argv[11]) {
  // Moderators only
  $where.=' AND `us`.`is_admin` = "y"';
}
if (isset($argv[12])) {
  if (true===$argv[12]) {
    // Not activated only
    $where.=' AND `us`.`activated` = "n"';
  } else {
    // Activated only
    $where.=' AND `us`.`activated` = "y"';
  }
}
if (isset($argv[13]) && $argv[13]!='') {
  $this->_db_prepareList($argv[13]);
  $where.=' AND `us`.`id` IN( \\_ARG13_\\ )';
}
$query='SELECT '.$select.'
          FROM `'.PCPIN_DB_PREFIX.'user` `us`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'nickname` `nn` ON (`nn`.`user_id` = `us`.`id` AND `nn`.`default` = "y")
               LEFT JOIN `'.PCPIN_DB_PREFIX.'user` `curr_us` ON `curr_us`.`id` = BINARY "\\_ARG7_\\"
               LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `curr_se` ON `curr_se`.`_s_user_id` = `curr_us`.`id`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `se` ON `se`.`_s_user_id` = `us`.`id`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'avatar` `av` ON `av`.`user_id` = `us`.`id` AND `av`.`primary` = "y"
               LEFT JOIN `'.PCPIN_DB_PREFIX.'avatar` `av_def` ON `av_def`.`user_id` = 0 AND `av_def`.`primary` = "y"
         WHERE 1
               '.$where.'
               '.$groupby.'
               '.$orderby.'
               '.$limit;
?>