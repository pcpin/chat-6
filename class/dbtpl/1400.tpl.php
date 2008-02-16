<?php
// Check wether IP address allowed/denied via IP filter or not
// Used in: PCPIN_IPFilter->isBlocked()
$query='SELECT `id`, `action`, `description`, `expires`
          FROM `'.PCPIN_DB_PREFIX.'ipfilter`
         WHERE `id` != "\\_ARG2_\\"
               AND ( `expires` = "0000-00-00 00:00:00" OR `expires` > NOW() )
               AND "\\_ARG1_\\" LIKE REPLACE( REPLACE( `address`, "?", "_" ), "*", "%" )
      GROUP BY `action`
      ORDER BY `id` DESC';
?>