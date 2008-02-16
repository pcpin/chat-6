<?php
// Get chat admins
// Used in: PCPIN_User->getAdmins()
$query='SELECT `us`.`id`,
               IF( `us`.`hide_email` = "0", `us`.`email`, "" ) AS `email`,
               `us`.`moderated_categories`,
               `us`.`moderated_rooms`,
               IF ( `se`.`_s_id` IS NOT NULL, 1, 0 ) AS `is_online`
          FROM `'.PCPIN_DB_PREFIX.'user` `us`
               LEFT JOIN `'.PCPIN_DB_PREFIX.'session` `se` ON `se`.`_s_user_id` = `us`.`id`
         WHERE `us`.`is_admin` = "y"';
?>