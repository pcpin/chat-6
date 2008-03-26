<?php
// Get display types of displayable banners
// Used in: PCPIN_Banner->checktRoomBanners()
$query='SELECT DISTINCT `display_position` AS `pos`
                   FROM `'.PCPIN_DB_PREFIX.'banner`
                  WHERE `active` = "y"
                        AND `start_date` <= "\\_ARG1_\\"
                        AND ( `expiration_date` >= "\\_ARG1_\\" OR `expiration_date` = "0000-00-00 00:00:00" )
                        AND ( `max_views` = 0 OR `max_views` < `views` )
                        ';
?>