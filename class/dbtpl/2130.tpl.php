<?php
// Get userdata fields for specified user
// Used in: PCPIN_userdata->getUserData()

$query='SELECT `udf`.*,
               REPLACE( REPLACE( `udf`.`choices`, "\r", "\n" ), "\n\n", "\n" ) AS `choices`,
               COALESCE( `ud`.`field_value`, `udf`.`default_value` ) AS `field_value`,
               IF( `udf`.`custom` = "y", `udf`.`name`, COALESCE( `le`.`value`, `udf`.`name` ) ) AS `name_translated`
          FROM `'.PCPIN_DB_PREFIX.'userdata_field` AS `udf`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'userdata` AS `ud` ON `ud`.`field_id` = `udf`.`id` AND `ud`.`user_id` = BINARY "\\_ARG1_\\"
               LEFT JOIN `'.PCPIN_DB_PREFIX.'language_expression` AS `le` ON `le`.`language_id` = "\\_ARG2_\\" AND `le`.`code` = `udf`.`name`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'user` `curr_us` ON `curr_us`.`id` = BINARY "\\_ARG3_\\"
         WHERE 
               `udf`.`disabled` = "n"
               AND (   `udf`.`visibility` IN( "public" )
                    OR `udf`.`visibility` IN( "registered" ) AND `curr_us`.`is_guest` = "n"
                    OR `udf`.`visibility` IN( "moderator" ) AND ( `curr_us`.`moderated_rooms` != "" OR `curr_us`.`moderated_categories` != "" )
                    OR `curr_us`.`is_admin` = "y"
                    )
      ORDER BY `udf`.`order` ASC';
?>