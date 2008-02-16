<?php
// Update version data
// Used in: PCPIN_Verion->setVersion()
//          PCPIN_Verion->setLastVersionCheckTime()
//          PCPIN_Verion->setNewestAvailableVersion()
//          PCPIN_Verion->setVersionCheckKey()
//          PCPIN_Verion->setNewVersionDownloadUrl()
$set=array();
if (!empty($argv[1])) {
  // Version
  $set[]='`version` = "\\_ARG1_\\"';
}
if (!empty($argv[2])) {
  // Last version check time
  $set[]='`last_version_check` = "\\_ARG2_\\"';
}
if (!empty($argv[3])) {
  // Newest available version
  $set[]='`new_version_available` = "\\_ARG3_\\"';
}
if (!empty($argv[4])) {
  // Version check security key
  $set[]='`version_check_key` = "\\_ARG4_\\"';
}
if (!empty($argv[5])) {
  // New version download URL
  $set[]='`new_version_url` = "\\_ARG5_\\"';
}
if (!empty($set)) {
  $query='UPDATE `'.PCPIN_DB_PREFIX.'version` SET '.implode(',', $set).' LIMIT 1';
} else {
  $query='';
}
?>