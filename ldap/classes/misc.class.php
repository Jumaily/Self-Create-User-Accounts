<?php
class misc{

   # function format phone number (10 or 7 Digits)
   function formatPhoneNumber($phoneNumber) {
      $phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);
      if(strlen($phoneNumber) > 10) {
         $countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
         $areaCode = substr($phoneNumber, -10, 3);
         $nextThree = substr($phoneNumber, -7, 3);
         $lastFour = substr($phoneNumber, -4, 4);
         $phoneNumber = '+'.$countryCode.' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
         }
      else if(strlen($phoneNumber) == 10) {
         $areaCode = substr($phoneNumber, 0, 3);
         $nextThree = substr($phoneNumber, 3, 3);
         $lastFour = substr($phoneNumber, 6, 4);
         $phoneNumber = '('.$areaCode.')'.$nextThree.'-'.$lastFour;
         }
      else if(strlen($phoneNumber) == 7) {
         $nextThree = substr($phoneNumber, 0, 3);
         $lastFour = substr($phoneNumber, 3, 4);
         $phoneNumber = $nextThree.'-'.$lastFour;
         }
      return $phoneNumber;
      }


   # Clean & Sanitize then Return Input
   function cleanInput($input) {
      $search = array(
                '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
                '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
                );
      $output = preg_replace($search, '', $input);
      return $output;
      }


   # Return Formated Timestamp
   function format_datetime($x){ return date("M j, Y - g:ia",strtotime($x)); }


   # Get & Return Remote Request IP
   function getUserIP() {
      if( array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ) {
         if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')>0) {
            $addr = explode(",",$_SERVER['HTTP_X_FORWARDED_FOR']);
            return trim($addr[0]);
            }
         else { return $_SERVER['HTTP_X_FORWARDED_FOR']; }
         }
      else{ return $_SERVER['REMOTE_ADDR']; }
      }

   }

?>
