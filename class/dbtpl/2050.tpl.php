<?php
// Update all users' ignore list after deleting a user
// Used in: PCPIN_User->deleteUser()
$query='UPDATE `'.PCPIN_DB_PREFIX.'user`
           SET `muted_users` = TRIM( BOTH "," FROM REPLACE( CONCAT( ",", `muted_users`, "," ), ",\\_ARG1_\\,", "," ) )
         WHERE FIND_IN_SET( "\\_ARG1_\\", `muted_users` )';
?>