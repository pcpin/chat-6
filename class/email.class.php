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
 * Class PCPIN_Email
 * Send emails
 * @static
 * @author Konstantin Reznichak <k.reznichak@pcpin.com>
 * @copyright Copyright &copy; 2007, Konstantin Reznichak
 */
class PCPIN_Email {


  /**
   * Send email
   * Send an email to specified recipients. Supports RFC821-conform envelops.
   *
   * Email address must be formatted as in one of the following examples:
   *      john.doe@some.domain.tld
   *      <john.doe@some.domain.tld>
   *      "John Doe" <john.doe@some.domain.tld>
   *      "John Doe" john.doe@some.domain.tld
   *      John Doe <john.doe@some.domain.tld>
   *      John Doe john.doe@some.domain.tld
   * NOTE: Sender name must have UTF-8 charset
   *
   * Attached files must be passed to this function as an array of following structure:
   *    array ( <file_1>, <file_2>, ... )
   * Single elements of that array must be an array of following structure:
   *    array ( 'filename'  => 'invoice.pdf',
   *            'mime_type' => 'application/pdf',
   *            'body'      => <file_contents_als_string> )
   *
   *
   * @param   string      $from       Sender email address
   * @param   mixed       $to         Receiver email address as string or multiple addresses as an array
   * @param   string      $subject    Subject
   * @param   mixed       $cc         CC Receiver email address as string or multiple addresses as an array
   * @param   mixed       $bcc        BCC Receiver email address as string or multiple addresses as an array
   * @param   string      $body       Email body
   * @param   array       $files      Attached files as array
   * @return  boolean   TRUE on success or FALSE on error
   */
  function send($from='', $to=null, $subject='', $cc=null, $bcc=null, $body='', $files=null) {
    $result=false;
    $from=trim($from);
    $from_strict=$from;
    $to_array=array();
    $to_strict_array=array();
    $cc_array=array();
    $cc_strict_array=array();
    $bcc_array=array();
    $bcc_strict_array=array();
    $default_mime='application/octet-stream';
    if (!empty($to)) {
      // From
      $from=PCPIN_Email::convertEmailAddressRFC($from, false);
      $from_strict=PCPIN_Email::convertEmailAddressRFC($from, true);
      // To
      if (!is_array($to)) {
        $to=trim($to);
        $to=$to!=''? explode(';', $to) : array();
      }
      foreach($to as $to_str) {
        $to_str=trim($to_str);
        if ($to_str!='') {
          $to_str=PCPIN_Email::convertEmailAddressRFC($to_str, false);
          if ($to_str!='') {
            $to_array[]=$to_str;
          }
          $to_str_strict=PCPIN_Email::convertEmailAddressRFC($to_str, true);
          if ($to_str_strict!='') {
            $to_strict_array[]=$to_str_strict;
          }
        }
      }
      // CC
      if (!is_array($cc)) {
        $cc=trim($cc);
        $cc=$cc!=''? explode(';', $cc) : array();
      }
      foreach ($cc as $cc_str) {
        $cc_str=trim($cc_str);
        if ($cc_str!='') {
          $cc_str=PCPIN_Email::convertEmailAddressRFC($cc_str, false);
          if ($cc_str!='') {
            $cc_array[]=$cc_str;
          }
          $cc_str_strict=PCPIN_Email::convertEmailAddressRFC($cc_str, true);
          if ($cc_str_strict!='') {
            $cc_strict_array[]=$cc_str_strict;
          }
        }
      }
      // BCC
      if (!is_array($bcc)) {
        $bcc=trim($bcc);
        $bcc=$bcc!=''? explode(';', $bcc) : array();
      }
      foreach ($bcc as $bcc_str) {
        $bcc_str=trim($bcc_str);
        if ($bcc_str!='') {
          $bcc_str=PCPIN_Email::convertEmailAddressRFC($bcc_str, false);
          if ($bcc_str!='') {
            $bcc_array[]=$bcc_str;
          }
          $bcc_str_strict=PCPIN_Email::convertEmailAddressRFC($bcc_str, true);
          if ($bcc_str_strict!='') {
            $bcc_strict_array[]=$bcc_str_strict;
          }
        }
      }
      // Boundary
      $boundary='==='.md5(PCPIN_Common::randomString(32));
      // Headers
      $headers=array('Content-Type: multipart/mixed; boundary="'.$boundary.'";',
                     'Content-Transfer-Encoding: 7bit',
                     'MIME-Version: 1.0',
                     'X-Generator: PCPIN'
                     );
      $headers_strict=$headers;
      // From
      if (!empty($from)) {
        $headers[]='From: '.$from;
      }
      if (!empty($from_strict)) {
        $headers_strict[]='From: '.$from_strict;
      }
      // CC
      if (!empty($cc_array)) {
        $headers[]='Cc: '.implode(', ', $cc_array);
      }
      if (!empty($cc_strict_array)) {
        $headers_strict[]='Cc: '.implode(', ', $cc_strict_array);
      }
      // BCC
      if (!empty($bcc_array)) {
        $headers[]='Bcc: '.implode(', ', $bcc_array);
      }
      if (!empty($bcc_strict_array)) {
        $headers_strict[]='Bcc: '.implode(', ', $bcc_strict_array);
      }
      // Create body
      $message='';
      if ($body!='') {
        $encoded_body='';
        $src=base64_encode($body);
        while (true) {
          $encoded_body.=substr($src, 0, 76);
          $src=substr($src, 76);
          if ($src!='') {
            $encoded_body.="\n";
          } else {
            break;
          }
        }
        $message.= '--'.$boundary."\n"
                  .'Content-Type: text/plain; charset=utf-8;'."\n"
                  .'Content-Transfer-Encoding: base64'."\n\n"
                  .$encoded_body
                  ."\n";
      }
      // Attachments
      if (!empty($files)) {
        foreach ($files as $file) {
          if (empty($file['mime'])) {
            $file['mime']=$default_mime;
          }
          if (empty($file['filename'])) {
            $file['filename']=md5(PCPIN_Common::randomString(32));
          }
          $file['mime']=str_replace('"', '\\"', $file['mime']);
          $file['filename']=str_replace('"', '\\"', PCPIN_Email::encodeHeaderValue($file['filename']));
          $encoded_body='';
          $src=base64_encode($file['body']);
          $encoded_body=wordwrap($src, 70, "\n", true);
          $message.= '--'.$boundary."\n"
                    .'Content-Type: '.$file['mime'].'; name="'.$file['filename'].'";'."\n"
                    .'Content-Transfer-Encoding: base64'."\n"
                    .'Content-Disposition: attachment; filename="'.$file['filename'].'"'."\n\n"
                    .$encoded_body
                    ."\n";
        }
      }
      if ($message!='') {
        $message.="\n".'--'.$boundary.'--'."\n";
      }
      // Trying to send mail
      if (false===$result=mail(implode(', ', $to_array), PCPIN_Email::encodeHeaderValue($subject), $message, implode("\n", $headers))) {
        // Failed. Trying to use RFC821-conform envelope.
        $result=mail(implode(', ', $to_strict_array), PCPIN_Email::encodeHeaderValue($subject), $message, implode("\n", $headers_strict));
      }
    }
    return $result;
  }


  /**
   * Convert email address into RFC-Compatible format
   * @param   string    $email    Email address
   * @param   boolean   $rfc821   Optional. Use strict RFC821 envelope format. Default is FALSE.
   * @return  string
   */
  function convertEmailAddressRFC($email='', $rfc821=false) {
    $out=trim($email);
    if ($out!='') {
      if (false!==strpos($out, ' ')) {
        $tmp=explode(' ', $out);
        $address=array_pop($tmp);
        $name=implode(' ', $tmp);
      } else {
        $name='';
        $address=$out;
      }
      if (substr($name, 0, 1)=='"' && substr($name, strlen($name)-1, 1)=='"') {
        $name=substr($name, 1, strlen($name)-2);
      }
      if (false!==strpos($name, '<')) {
        $name='"'.str_replace('"', '\\"', $name).'"';
      }
      $address='<'.trim(ltrim(rtrim(trim($address), '>'), '<')).'>';
      if ($rfc821 || $name=='' || $name=='""') {
        // Strict mode
        $out=$address;
      } else {
        // "Violative" mode, not RFC821-comform, but accepted by mosts servers :/
        $out=PCPIN_Email::encodeHeaderValue($name).' '.$address;
      }
    }
    return $out;
  }


  /**
   * Encodes string to use in header
   * @param     string    $str    String to encode
   * @return    string
   */
  function encodeHeaderValue($str='') {
    $out='=?UTF-8?B?'.base64_encode($str).'?=';
    return $out;
  }


}
?>