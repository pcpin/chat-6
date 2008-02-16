<?php
// Add an invitation
                    // Used in: PCPIN_Invitation->addNewInvitation()
$query='INSERT INTO `'.PCPIN_DB_PREFIX.'invitation`
                    (`author_id`, `author_nickname`, `target_user_id`, `room_id`, `room_name`)
                    SELECT "\\_ARG1_\\" AS `author_id`,
                           `nn`.`nickname` AS `author_nickname`,
                           "\\_ARG2_\\" AS `target_user_id`,
                           "\\_ARG3_\\" AS `room_id`,
                           `ro`.`name` AS `room_name`
                      FROM `'.PCPIN_DB_PREFIX.'nickname` `nn`
                           LEFT JOIN `'.PCPIN_DB_PREFIX.'room` `ro` ON `ro`.`id` = "\\_ARG3_\\"
                     WHERE `nn`.`user_id` = "\\_ARG1_\\"
                           AND `nn`.`default` = "y"
                           AND `ro`.`id` IS NOT NULL
                           LIMIT 1';
?>