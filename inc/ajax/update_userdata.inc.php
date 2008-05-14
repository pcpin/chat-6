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
    $xmlwriter->setHeaderMessage($l->g('changes_saved'));
    $xmlwriter->setHeaderStatus(0);
  } else {
    $xmlwriter->setHeaderMessage($l->g('error'));
    $xmlwriter->setHeaderStatus(1);
  }
}
$xmlwriter->setData(array('gender'=>$profile_userdata->gender,
                          'age'=>$profile_userdata->age,
                          'homepage'=>$profile_userdata->homepage,
                          'icq'=>$profile_userdata->icq,
                          'msn'=>$profile_userdata->msn,
                          'aim'=>$profile_userdata->aim,
                          'yim'=>$profile_userdata->yim,
                          'location'=>$profile_userdata->location,
                          'occupation'=>$profile_userdata->occupation,
                          'interests'=>$profile_userdata->interests,
                          ));

?>