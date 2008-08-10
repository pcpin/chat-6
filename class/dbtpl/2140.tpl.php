<?php
// Get userdata fields list
// Used in: PCPIN_Userdata_Field->getFields()

$query='SELECT `udf`.*,
               REPLACE( REPLACE( `udf`.`choices`, "\r", "\n" ), "\n\n", "\n" ) AS `choices`,
               IF( `udf`.`custom` = "y", `udf`.`name`, COALESCE( `le`.`value`, `udf`.`name` ) ) AS `name_translated`
          FROM `'.PCPIN_DB_PREFIX.'userdata_field` AS `udf`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'language_expression` AS `le` ON `le`.`language_id` = "\\_ARG1_\\" AND `le`.`code` = `udf`.`name`
      ORDER BY `udf`.`order` ASC';
?>