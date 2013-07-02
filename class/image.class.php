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
 * GD Version
 */
define('PCPIN_GD_VERSION', PCPIN_Image::whichGD());

/**
 * Image check results
 */
define('PCPIN_IMAGE_CHECK_OK',              0); // Image OK
define('PCPIN_IMAGE_CHECK_ERROR_FILE',      1); // File is not an image or file does not exists / not readable
define('PCPIN_IMAGE_CHECK_ERROR_MIME',      2); // MIME not allowed
define('PCPIN_IMAGE_CHECK_ERROR_WIDTH',     3); // Image width larger than allowed
define('PCPIN_IMAGE_CHECK_ERROR_HEIGHT',    4); // Image height larger than allowed
define('PCPIN_IMAGE_CHECK_ERROR_FILESIZE',  5); // Image file size larger than allowed
define('PCPIN_IMAGE_CHECK_ERROR_NOT_IMAGE', 6); // File is not an image or has an uncompatible format

/**
 * Class PCPIN_Image
 * Contains static image manipulation methods
 * @static
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Image {


  /**
   * Check server for GD functions support
   * @return  int   0: Server does not supports GD, 1: Server supports GD1 only, 2: Server supports GD2
   */
  function whichGD() {
    if (defined('GD_MAJOR_VERSION')) {
      return GD_MAJOR_VERSION;
    }
    if (function_exists('gd_info')) {
      $gd_info=gd_info();
      if (!empty($gd_info['GD Version'])) {
        $version = explode('.', preg_replace('/[^0-9\.]/', '', $gd_info['GD Version']));
        return $version[0];
      }
    }
    return 0;
  }


  /**
   * Create a thumb of specified size and type
   * Requires GD2 !!!
   * @param   string      $dst_img              A reference to a variable where destination image
   *                                            will be stored (as raw string)
   * @param   string      $dst_file             If specified, then the thumb will be stored into that file,
   *                                            instead of $dst_img variable
   * @param   string      $src_file             Source image file name
   * @param   string      $src_img              Source image as string
   * @param   string      $width                Desired thumb width
   * @param   string      $height               Desired thumb height
   * @param   string      $type                 Desired thumb image type
   * @param   int         $bgcolor_r            Red component of background color
   * @param   int         $bgcolor_g            Green component of background color
   * @param   int         $bgcolor_b            Blue component of background color
   * @param   string      $fallback_src_file    Will be used if $src_file and $src_img are errornous.
   * @param   string      $fallback_src_img     Will be used if $src_file and $src_img are errornous.
   * @return  boolean  TRUE on success or false on error
   */
  function makeThumb(&$dst_img, $dst_file=null, $src_file=null, $src_img=null, $width=0, $height=0,
                     $type='jpg', $bgcolor_r=255, $bgcolor_g=255, $bgcolor_b=255,
                     $fallback_src_file=null, $fallback_src_img=null) {
    $result=false;
    if (   PCPIN_GD_VERSION==2
        && (!empty($src_img) || !empty($src_file) && file_exists($src_file) && is_readable($src_file))
        && !empty($width) && !empty($height)) {
      // Get an image
      $im=null;
      if (!empty($src_file) && file_exists($src_file) && is_readable($src_file)) {
        // Image filename supplied. Read it into a string.
        $src_img=file_get_contents($src_file);
      }
      if (!empty($src_img)) {
        // Create image resource
        $im=imagecreatefromstring($src_img);
      }
      if (empty($im)) {
        // Use fallback
        if (!empty($fallback_src_file) && file_exists($fallback_src_file) && is_readable($fallback_src_file)) {
          $src_img=file_get_contents($fallback_src_file);
        } elseif (!empty($fallback_src_img)) {
          $src_img=$fallback_src_img;
        }
        if (!empty($src_img)) {
          // Create image resource
          $im=imagecreatefromstring($src_img);
        }
      }
      if (empty($im)) {
        $im=imagecreate($width, $height);
        imagecolorallocate($im, $bgcolor_r, $bgcolor_g, $bgcolor_b);
      }
      if (!empty($im)) {
        $copy_width=$width;
        $copy_height=$height;
        // Create new image
        $new_im_tmp=imagecreate($width, $height);
        imagecolorallocate($new_im_tmp, $bgcolor_r, $bgcolor_g, $bgcolor_b);
        $new_im=imagecreatetruecolor($width, $height);
        imagecopy($new_im, $new_im_tmp, 0, 0, 0, 0, $width, $height);
        imagedestroy($new_im_tmp);
        unset($new_im_tmp);
        $src_width=imagesx($im);
        $src_height=imagesy($im);
        if ($width>$src_width) {
          $copy_width=$src_width;
        }
        if ($height>$src_height) {
          $copy_height=$src_height;
        }
        if ($src_width/$copy_width<$src_height/$copy_height) {
          $copy_width=$copy_height*($src_width/$src_height);
        } else {
          $copy_height=$copy_width/($src_width/$src_height);
        }
        $dst_x=0;
        $dst_y=0;
        if ($width>$copy_width) {
          $dst_x+=round(($width-$copy_width)/2);
        }
        if ($height>$copy_height) {
          $dst_y+=round(($height-$copy_height)/2);
        }
        imagecopyresampled($new_im, $im, $dst_x, $dst_y, 0, 0, $copy_width, $copy_height, $src_width, $src_height);
        imagedestroy($im);
        unset($im);
        // Create thumb image
        switch(strtolower($type)) {
          case  'jpg'   :
          case  'jpeg'  :   // JPEG image
                            if (!is_null($dst_file)) {
                              $result=imagejpeg($new_im, $dst_file, 95);
                              $dst_img=file_get_contents($dst_file);
                            } else {
                              ob_start();
                              $result=imagejpeg($new_im, null, 95);
                              $dst_img=ob_get_clean();
                            }
          break;
          case  'gif'   :   // GIF image
                            if (!is_null($dst_file)) {
                              $result=imagegif($new_im, $dst_file);
                              $dst_img=file_get_contents($dst_file);
                            } else {
                              ob_start();
                              $result=imagegif($new_im);
                              $dst_img=ob_get_clean();
                            }
          break;
          case  'png'   :   // PNG image
                            if (!is_null($dst_file)) {
                              $result=imagepng($new_im, $dst_file);
                              $dst_img=file_get_contents($dst_file);
                            } else {
                              ob_start();
                              $result=imagepng($new_im);
                              $dst_img=ob_get_clean();
                            }
          break;
        }
      }
    }
    return $result;
  }


  /**
   * Check image / uploaded image
   * @param   array     $img_data   Image data will be stored here
   * @param   string    $fname      Path to image file
   * @param   string    $mime       List of MIME types separated by "|" character. Empty value: all "image/*" MIME types.
   * @param   int       $max_w      Maximum allowed image width in pixels. 0: no limit.
   * @param   int       $max_h      Maximum allowed image height in pixels. 0: no limit.
   * @param   int       $max_s      Maximum allowed image file size in bytes. 0: no limit.
   * @param   boolean   $local      Set it to TRUE if you want to bypass is_uploaded_file() check
   * @return  int       Check result (see defined constants above)
   */
   function checkImage(&$img_data, $fname='', $mime='', $max_w=0, $max_h=0, $max_s=0, $local=false) {
     $result=PCPIN_IMAGE_CHECK_OK;
     if (   $fname!=''
         && file_exists($fname)
         && is_file($fname)
         && is_readable($fname)
         && ($local || is_uploaded_file($fname))
         && 0<($fsize=filesize($fname))
         && false!==$img_data=getimagesize($fname)) {
       $img_data['width']=$img_data[0];
       unset($img_data[0]);
       $img_data['height']=$img_data[1];
       unset($img_data[1]);
       // Check MIME type
       $mime_ok=false;
       $mime=strtolower(trim($mime));
       if ($mime=='') {
         // All "image/*" MIME types
         $mime_ok='image/'==substr($img_data['mime'], 0, 6);
       } else {
         // MIME was specified
         $mime_array=explode('|', $mime);
         foreach ($mime_array as $mime) {
           if (trim($mime)==$img_data['mime']) {
             // MIME OK
             $mime_ok=true;
             break;
           }
         }
       }
       if (true===$mime_ok) {
         if (!empty($max_w) && $max_w<$img_data['width']) {
           $result=PCPIN_IMAGE_CHECK_ERROR_WIDTH;
         } elseif (!empty($max_h) && $max_h<$img_data['height']) {
           $result=PCPIN_IMAGE_CHECK_ERROR_HEIGHT;
         } elseif (!empty($max_s) && $max_s<$fsize) {
           $result=PCPIN_IMAGE_CHECK_ERROR_FILESIZE;
         }
       } else {
         $result=PCPIN_IMAGE_CHECK_ERROR_MIME;
       }
     } else {
       if (file_exists($fname) && is_readable($fname)) {
         $result=PCPIN_IMAGE_CHECK_ERROR_NOT_IMAGE;
       } else {
         $result=PCPIN_IMAGE_CHECK_ERROR_FILE;
       }
     }
     return $result;
   }


}
?>