<?php
// Get IP addresses list from "ipfilter" table
// Used in: PCPIN_IPFilter->readAddresses()
$orderby='';
$orderdir='';
if (!empty($argv[2])) {
  $orderdir='DESC';
} else {
  $orderdir='ASC';
}
if (!isset($argv[1])) {
  $argv[1]=0;
}
switch($argv[1]) {
  default   :
  case  0   :   $orderby= '  INET_ATON( `address` ) '.$orderdir;
                break;
  case  1   :   $orderby= '  `action` '.$orderdir.', INET_ATON( `address` ) '.$orderdir;
                break;
  case  2   :   $orderby= '  `expires` '.$orderdir.', INET_ATON( `address` ) '.$orderdir;
                break;
  case  3   :   $orderby= '  `description` '.$orderdir.', INET_ATON( `address` ) '.$orderdir;
                break;
  case  4   :   $orderby= '  `added_on` '.$orderdir.', INET_ATON( `address` ) '.$orderdir;
                break;
  case  5   :   $orderby= '  `type` '.$orderdir.', INET_ATON( `address` ) '.$orderdir;
                break;
}
$query='SELECT *,
               `address` AS `address`,
               CONVERT( SUBSTRING_INDEX( `address`, ".", 1 ), UNSIGNED ) AS `ip_part1`,
               CONVERT( SUBSTRING_INDEX( SUBSTRING_INDEX( `address`, ".", 2), ".", -1 ), UNSIGNED ) AS `ip_part2`,
               CONVERT( SUBSTRING_INDEX( SUBSTRING_INDEX( `address`, ".", -2), ".", 1 ), UNSIGNED ) AS `ip_part3`,
               CONVERT( SUBSTRING_INDEX( `address`, ".", -1 ), UNSIGNED ) AS `ip_part4`
          FROM `'.PCPIN_DB_PREFIX.'ipfilter`
      ORDER BY '.$orderby;
?>