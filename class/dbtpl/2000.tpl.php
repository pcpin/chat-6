<?php
// Get display types of displayable banners
// Used in: PCPIN_Banner->checktRoomBanners()
$query='SELECT DISTINCT `display_position` AS `pos`
                   FROM `'.PCPIN_DB_PREFIX.'banner`
                  WHERE `active` = "y"
                        AND `start_date` <= NOW()
                        AND ( `expiration_date` >= NOW() OR `expiration_date` = "0000-00-00 00:00:00" )
                        AND ( `max_views` = 0 OR `max_views` < `views` )
                        ';
?>