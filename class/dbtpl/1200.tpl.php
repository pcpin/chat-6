<?php
// Get chat rooms list grouped in categories
// Used in: PCPIN_Category->getTree()
$where='';
$query='SELECT `ca`.`id` AS `category_id`,
               `ca`.`parent_id` AS `category_parent_id`,
               `ca`.`name` AS `category_name`,
               `ca`.`description` AS `category_description`,
               IF( `ca`.`creatable_rooms` = "g" OR `ca`.`creatable_rooms` = "r" AND `curr_us`.`is_guest` = "n", 1, 0 ) AS `creatable_rooms`,
               `ca`.`creatable_rooms` AS `creatable_rooms_flag`,
               `ro`.`id` AS `room_id`,
               `ro`.`name` AS `room_name`,
               `ro`.`description` AS `room_description`,
               `ro`.`default_message_color`,
               COALESCE( `ud`.`field_value`, "-" ) AS `gender`,
               IF( `ro`.`password` != "", 1, 0) AS `password_protected`,
               IF( `ro`.`password` = "" OR `curr_us`.`is_admin` = "y" OR `curr_se`.`_s_room_id` = `ro`.`id`, `ro`.`background_image`, 0) AS `background_image`,
               IF( `ro`.`password` = "" OR `curr_us`.`is_admin` = "y" OR `curr_se`.`_s_room_id` = `ro`.`id`, `bf`.`width`, 0) AS `background_image_width`,
               IF( `ro`.`password` = "" OR `curr_us`.`is_admin` = "y" OR `curr_se`.`_s_room_id` = `ro`.`id`, `bf`.`height`, 0) AS `background_image_height`,
               IF( `curr_us`.`is_admin` = "y" OR FIND_IN_SET( `ro`.`id`, `curr_us`.`moderated_rooms` ), 1, 0) AS `moderated_by_me`,
               `se`.`_s_user_id` AS `user_id`,
               IF( `curr_us`.`is_admin` = "y", `se`.`_s_ip`, "" ) AS `ip_address`,
               COALESCE( `nn`.`nickname`, `us`.`login` ) AS `nickname`,
               COALESCE( `nn`.`nickname_plain`, `us`.`login` ) AS `nickname_plain`,
               COALESCE( `av`.`binaryfile_id`, `av_def`.`binaryfile_id` ) AS `avatar_bid`,
               `se`.`_s_online_status` AS `online_status`,
               `se`.`_s_online_status_message` AS `online_status_message`,
               `us`.`global_muted_permanently`,
               IF( `us`.`is_admin` = "y", 1, 0) AS `is_admin`,
               IF( `us`.`is_guest` = "y", 1, 0) AS `is_guest`,
               IF( `curr_se`.`_s_room_id` > 0 AND `curr_se`.`_s_room_id` = `se`.`_s_room_id` AND FIND_IN_SET( `se`.`_s_room_id`, `us`.`moderated_rooms` ) > 0, 1, 0) AS `is_moderator`,
               IF( `us`.`global_muted_until` > "'.date('Y-m-d').'" OR `us`.`global_muted_permanently` = "y", 1, 0 ) AS  `global_muted`,
               IF( `us`.`global_muted_until` > "'.date('Y-m-d').'" OR `us`.`global_muted_permanently` = "y", `us`.`global_muted_by`, 0) AS `global_muted_by`,
               IF( `us`.`global_muted_until` > "'.date('Y-m-d').'" OR `us`.`global_muted_permanently` = "y", `us`.`global_muted_by_username`, "") AS `global_muted_by_username`,
               IF( `us`.`global_muted_until` > "'.date('Y-m-d').'", UNIX_TIMESTAMP( `us`.`global_muted_until` ) + `curr_us`.`time_zone_offset` - '.date('Z').', 0) AS `global_muted_until`,
               IF( `us`.`global_muted_until` > "'.date('Y-m-d').'" OR `us`.`global_muted_permanently` = "y", `us`.`global_muted_reason`, "") AS `global_muted_reason`,
               IF( FIND_IN_SET( `us`.`id`, `curr_us`.`muted_users` ), 1, 0) AS `muted_locally`
          FROM `'.PCPIN_DB_PREFIX.'category` `ca`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'user` `curr_us` ON `curr_us`.`id` = BINARY "\\_ARG1_\\"
               LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `curr_se` ON `curr_se`.`_s_user_id` = `curr_us`.`id`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'room` `ro` ON `ro`.`category_id` = `ca`.`id`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `se` ON (`se`.`_s_room_id` = `ro`.`id` AND (`se`.`_s_stealth_mode` = "n" OR `curr_us`.`is_admin` = "y" OR `curr_se`.`_s_room_id` != 0 AND FIND_IN_SET( `se`.`_s_room_id`, `curr_us`.`moderated_rooms` )))
               LEFT JOIN `'.PCPIN_DB_PREFIX.'nickname` `nn` ON (`nn`.`user_id` = `se`.`_s_user_id` AND `nn`.`default` = "y")
               LEFT JOIN `'.PCPIN_DB_PREFIX.'user` `us` ON `us`.`id` = `se`.`_s_user_id`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'userdata_field` `udf` ON `udf`.`name` = "gender" AND `udf`.`custom` = "n"
               LEFT JOIN `'.PCPIN_DB_PREFIX.'userdata` `ud` ON `ud`.`user_id` = `us`.`id` AND `ud`.`field_id` = `udf`.`id`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'avatar` `av` ON `av`.`user_id` = `us`.`id` AND `av`.`primary` = "y"
               LEFT JOIN `'.PCPIN_DB_PREFIX.'avatar` `av_def` ON `av_def`.`user_id` = 0 AND `av_def`.`primary` = "y"
               LEFT JOIN `'.PCPIN_DB_PREFIX.'binaryfile` `bf` ON `bf`.`id` = `ro`.`background_image`
         WHERE 1
               '.$where.'
      ORDER BY `ca`.`listpos` ASC,
               `ro`.`listpos` ASC,
               `nn`.`nickname_plain` ASC';
?>