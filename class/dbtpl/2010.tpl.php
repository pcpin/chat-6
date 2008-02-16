<?php
// Get random displayable banner of specified display position
// Used in: PCPIN_Banner->getRandomBanner()
$query='SELECT * FROM `'.PCPIN_DB_PREFIX.'banner`
                WHERE `active` = "y"
                      AND `start_date` <= NOW()
                      AND ( `expiration_date` >= NOW() OR `expiration_date` = "0000-00-00 00:00:00" )
                      AND ( `max_views` = 0 OR `max_views` < `views` )
                      AND `display_position` = "\\_ARG1_\\"
                 ORDER BY RAND()
                    LIMIT 1';
?>