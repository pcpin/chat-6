<?php
/**
 *    This file is part of "PCPIN Chat 6".
 *
 *    "PCPIN Chat 6" is free software; you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation; either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    "PCPIN Chat 6" is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

if (PCPIN_SLAVE_MODE) {
  // Not used in Slave mode
  echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
  <message>'.htmlspecialchars($l->g('slave_mode')).'</message>
  <status>1</status>
</pcpin_xml>';
  die();
}

/**
 * Update user data. Following variables will be used (if set)
 * @param   string    $gender       Gender
 * @param   string    $homepage     Homepage
 * @param   string    $age          Age
 * @param   string    $icq          ICQ
 * @param   string    $msn          MSN
 * @param   string    $aim          AIM
 * @param   string    $yim          YIM
 * @param   string    $location     Location
 * @param   string    $occupation   Occupation
 * @param   string    $interests    Interests
 */
$message=$l->g('access_denied');
$status='-1'; // -1: Session is invalid

if (empty($profile_user_id) || $current_user->is_admin!=='y') {
  $profile_user_id=$current_user->id;
}
if ($profile_user_id!=$current_user->id) {
  $profile_userdata=new PCPIN_UserData($session);
  $profile_userdata->_db_loadObj($profile_user_id, 'user_id');
} else {
  $profile_userdata=&$current_userdata;
}

if (!empty($profile_user_id)) {
  if (isset($gender) && is_scalar($gender)) {
    $profile_userdata->gender=trim($gender);
  }
  if (isset($homepage) && is_scalar($homepage)) {
    $profile_userdata->homepage=trim($homepage);
  }
  if (isset($age) && is_scalar($age)) {
    $profile_userdata->age=trim($age);
  }
  if (isset($icq) && is_scalar($icq)) {
    $profile_userdata->icq=trim($icq);
  }
  if (isset($msn) && is_scalar($msn)) {
    $profile_userdata->msn=trim($msn);
  }
  if (isset($aim) && is_scalar($aim)) {
    $profile_userdata->aim=trim($aim);
  }
  if (isset($yim) && is_scalar($yim)) {
    $profile_userdata->yim=trim($yim);
  }
  if (isset($location) && is_scalar($location)) {
    $profile_userdata->location=trim($location);
  }
  if (isset($occupation) && is_scalar($occupation)) {
    $profile_userdata->occupation=trim($occupation);
  }
  if (isset($interests) && is_scalar($interests)) {
    $profile_userdata->interests=trim($interests);
  }
  if ($profile_userdata->updateUserData($profile_user_id, true, true,
                                        $profile_userdata->gender,
                                        $profile_userdata->age,
                                        $profile_userdata->icq,
                                        $profile_userdata->msn,
                                        $profile_userdata->aim,
                                        $profile_userdata->yim,
                                        $profile_userdata->location,
                                        $profile_userdata->occupation,
                                        $profile_userdata->interests,
                                        $profile_userdata->homepage
                                        )) {
    $message=$l->g('changes_saved');
    $status=0;
  } else {
    $message=$l->g('error');
    $status=1;
  }
}

echo '<?xml version="1.0" encoding="UTF-8"?>
<pcpin_xml>
<message>'.htmlspecialchars($message).'</message>
<status>'.htmlspecialchars($status).'</status>
<gender>'.htmlspecialchars($profile_userdata->gender).'</gender>
<age>'.htmlspecialchars($profile_userdata->age).'</age>
<homepage>'.htmlspecialchars($profile_userdata->homepage).'</homepage>
<icq>'.htmlspecialchars($profile_userdata->icq).'</icq>
<msn>'.htmlspecialchars($profile_userdata->msn).'</msn>
<aim>'.htmlspecialchars($profile_userdata->aim).'</aim>
<yim>'.htmlspecialchars($profile_userdata->yim).'</yim>
<location>'.htmlspecialchars($profile_userdata->location).'</location>
<occupation>'.htmlspecialchars($profile_userdata->occupation).'</occupation>
<interests>'.htmlspecialchars($profile_userdata->interests).'</interests>
</pcpin_xml>';
die();
?>