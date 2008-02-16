<?php
// Update language expression
// Used in: PCPIN_Language_Expression->updateExpression()
$query='UPDATE `'.PCPIN_DB_PREFIX.'language_expression`
           SET `value` = "\\_ARG3_\\"
         WHERE `language_id` = "\\_ARG1_\\"
               AND `code` = BINARY "\\_ARG2_\\"
         LIMIT 1';
?>