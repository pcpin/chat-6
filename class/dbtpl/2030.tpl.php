<?php
// Collect full message data for logging
// Used in: PCPIN_Message_Log->addLogRecord()
$query='SELECT `me`.*,
               COALESCE( `src_se`.`_s_ip`, "" ) AS `author_ip`,
               COALESCE( `src_cat`.`id`, 0 ) AS `category_id`,
               COALESCE( `src_cat`.`name`, "" ) AS `category_name`,
               COALESCE( `src_room`.`id`, 0 ) AS `room_id`,
               COALESCE( `src_room`.`name`, "" ) AS `room_name`,
               COALESCE( `tgt_cat`.`id`, 0 ) AS `target_category_id`,
               COALESCE( `tgt_cat`.`name`, "" ) AS `target_category_name`,
               COALESCE( `tgt_room`.`name`, "" ) AS `target_room_name`,
               COALESCE( `tgt_nn`.`nickname`, "" ) AS `target_user_nickname`
          FROM `'.PCPIN_DB_PREFIX.'message` `me`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `src_se` ON `src_se`.`_s_user_id` = `me`.`author_id`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'room` `src_room` ON `src_room`.`id` = `src_se`.`_s_room_id`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'category` `src_cat` ON `src_cat`.`id` = `src_room`.`category_id`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'room` `tgt_room` ON `tgt_room`.`id` = `me`.`target_room_id`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'category` `tgt_cat` ON `tgt_cat`.`id` = `tgt_room`.`category_id`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'nickname` `tgt_nn` ON ( `tgt_nn`.`user_id` = `me`.`target_user_id` AND `tgt_nn`.`default` = "y" )
               LEFT JOIN `'.PCPIN_DB_PREFIX.'user` `tgt_us` ON `tgt_us`.`id` = `me`.`target_user_id`
         WHERE `me`.`id` = "\\_ARG1_\\"
               LIMIT 1';
?>