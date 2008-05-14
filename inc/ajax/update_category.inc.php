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

_pcpin_loadClass('category'); $category=new PCPIN_Category($session);

if (!isset($category_id) || !pcpin_ctype_digit($category_id)) $category_id=0;
if (!isset($action) || !is_scalar($action)) $action='';
if (!isset($dir) || !pcpin_ctype_digit($dir)) $dir=0;
$parent_id=0; //todo
if (!isset($name) || !is_scalar($name)) $name='';
if (!isset($description) || !is_scalar($description)) $description='';
if (!isset($creatable_rooms) || !is_scalar($creatable_rooms)) $creatable_rooms='n';

$errortext=array();

if (!empty($current_user->id) && $current_user->is_admin==='y' && $session->_s_user_id==$current_user->id) {
  $xmlwriter->setHeaderStatus(1);
  $xmlwriter->setHeaderMessage($l->g('error'));
  if (!empty($category_id) && $category->_db_getList('id = '.$category_id)) {
    // Category exists
    $xmlwriter->setHeaderStatus(0);
    $xmlwriter->setHeaderMessage('OK');
    $category_data=$category->_db_list[0];
    $category->_db_freelist();
    switch ($action) {

      case 'change_listpos':
        if (empty($dir)) {
          // Move up
          if ($category->_db_getList('id,listpos',
                                     'parent_id = '.$category_data['parent_id'],
                                     'listpos < '.$category_data['listpos'],
                                     'listpos DESC',
                                     1)) {
            $higher_cat_id=$category->_db_list[0]['id'];
            $higher_cat_listpos=$category->_db_list[0]['listpos'];
            // Update category
            $category->updateCategory($category_id, false, true, null, null, null, null, $higher_cat_listpos);
            // Update higher category
            $category->updateCategory($higher_cat_id, false, true, null, null, null, null, $category_data['listpos']);
          }
        } else {
          // Move down
          if ($category->_db_getList('id,listpos',
                                     'parent_id = '.$category_data['parent_id'],
                                     'listpos > '.$category_data['listpos'],
                                     'listpos ASC',
                                     1)) {
            $lower_cat_id=$category->_db_list[0]['id'];
            $lower_cat_listpos=$category->_db_list[0]['listpos'];
            // Update category
            $category->updateCategory($category_id, false, true, null, null, null, null, $lower_cat_listpos);
            // Update lower category
            $category->updateCategory($lower_cat_id, false, true, null, null, null, null, $category_data['listpos']);
          }
        }
      break;

      case 'change_data':
        $errortext=array();
        $name=trim($name);
        $description=trim($description);
        $creatable_rooms=trim($creatable_rooms);
        if ($name=='') {
          $errortext[]=$l->g('category_name_empty');
        } elseif ($category->_db_getList('id != '.$category_id, 'parent_id = '.$parent_id, 'name LIKE '.$name, 1)) {
          $errortext[]=str_replace('[NAME]', $name, $l->g('category_name_exists'));
        }

        if (!empty($errortext)) {
          $xmlwriter->setHeaderStatus(1);
          $xmlwriter->setHeaderMessage(implode("\n", $errortext));
        } else {
          $xmlwriter->setHeaderStatus(0);
          $xmlwriter->setHeaderMessage($l->g('changes_saved'));
          $category->updateCategory($category_id, false, true, $parent_id, $name, $description, $creatable_rooms);
        }

      break;

    }
  }
}
?>