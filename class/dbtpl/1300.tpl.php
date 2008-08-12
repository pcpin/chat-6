<?php
// Get new online messages for user
// Used in: PCPIN_Message->getNewMessages()
// Used in: PCPIN_Message->getLastMessages()
$where=' 1 ';
$orderby='`me`.`id` ASC';
$limit='';
if (!empty($argv[1])) {
  $argv[1]*=1;
}
if (empty($argv[2])) {
  $where.=' AND ( `me`.`id` > `se`.`_s_last_message_id` AND `me`.`date` > `se`.`_s_room_date` )';
} else {
  $argv[2]*=1;
  $orderby='`me`.`id` DESC';
  $limit='LIMIT \\_ARG2_\\';
}
if (!empty($argv[3])) {
  // Return messages of specified type
  $where.=' AND `me`.`type` = "\\_ARG3_\\" ';
}
$query='SELECT `me`.`id`,
               `me`.`type`,
               `me`.`offline`,
               UNIX_TIMESTAMP( `me`.`date` ) + `curr_us`.`time_zone_offset` - '.date('Z').' AS `date`,
               `me`.`author_id`,
               `me`.`author_nickname`,
               `me`.`target_room_id`,
               `me`.`target_user_id`,
               `me`.`privacy`,
               `me`.`body`,
               `me`.`css_properties`,
               IF( `at`.`id` IS NOT NULL, 1, 0 ) AS `has_attachments`
          FROM `'.PCPIN_DB_PREFIX.'message` `me`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'attachment` `at` ON `at`.`message_id` = `me`.`id`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `se` ON `se`.`_s_user_id` = "\\_ARG1_\\"
               LEFT JOIN `'.PCPIN_DB_PREFIX.'user` `curr_us` ON `curr_us`.`id` = BINARY "\\_ARG1_\\"
         WHERE '.$where.'
               AND `me`.`offline` = "n"
               AND ( `me`.`target_room_id` = `se`.`_s_room_id` OR `me`.`target_room_id` = 0 )
               AND ( `me`.`target_user_id` = `se`.`_s_user_id` OR `me`.`author_id` = `se`.`_s_user_id` OR `me`.`privacy` = 0 )
               AND ( `me`.`privacy` != 2 || `me`.`id` > `se`.`_s_last_message_id` ) /* do not show own PMs */
      GROUP BY `me`.`id`
      ORDER BY '.$orderby.'
     '.$limit;
?>