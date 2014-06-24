<?php
class Main extends SQLite3{
   private $sqliteDB = sqliteDB;
   private $connect;
   public $misc;
   public $ldap;

   public function __construct(){
      $this->ldap = new ldap(); # include the ldap class
      $this->misc = new misc(); # include misc class, format, clean input, etc...

      $this->open($this->sqliteDB);
      $this->connect = new SQLite3($this->sqliteDB);
      if(isset($_POST['email'])){
         $this->checkuser($_POST);
         }
      }

   public function __destruct(){
      $this->connect->close();
      unset($this->connect);
      }



   # Main function to create user (adding them to Database)
   # Will return success or reason of failed action
   function create_user(){
      global $SESSION;
      $fn = $SESSION->get_var('firstname');
      $ln = $SESSION->get_var('lastname');
      $el = $SESSION->get_var('email');
      $ph = preg_replace('/[^0-9_]/','',$SESSION->get_var('phone'));
      $pw = $SESSION->get_var('password');
      $un = $SESSION->get_var('uname');
      $labgroup = $SESSION->get_var('group');

      $sql = "INSERT INTO users (fname,lname,email,passwd,uname,phone,active,iphost,labgroup) VALUES ";
      $sql .= "('$fn','$ln','$el','$pw','$un','$ph','NO','{$this->misc->getUserIP()}','$labgroup')";

      $ret = $this->connect->exec($sql);
      if(!$ret){ echo $db->lastErrorMsg(); }
      else { return "Record created successfully\n"; }
      }



   # Check user pre-req to request
   private function checkuser($vars){
	   global $SESSION;
	   $proceed = true;

	   # phone
	   if(!$this->checkPhone($vars["phone"])){
         $SESSION->set_var('phone',"Invalid Phone");
         $proceed = false;
         }

      # password
	   if(!$this->checkPassword($vars["pass1"],$vars["pass2"])){
		   $SESSION->set_var('password',"Invalid Password (>5 characters long, contains number and characters, & must match");
		   $proceed = false;
	      }

	   # email
      if(!preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/",$vars["email"])) {
	      $SESSION->set_var('email',"Invalid Email");
	      $proceed = false;
	      }
	   else{
	      $ret = $this->connect->query("SELECT email FROM users WHERE email LIKE '{$vars["email"]}' LIMIT 1");
         $row=$ret->fetchArray(SQLITE3_ASSOC);
         // check for duplicate email
         if($row != false){
            $SESSION->set_var('email',"Email Already Exists");
            $proceed = false;
            } else{;}
         }

      # Username check
      $linkb = $this->connect->query("SELECT uname FROM users WHERE uname LIKE '{$vars["uname"]}' LIMIT 1");
      $row=$linkb->fetchArray(SQLITE3_ASSOC);
      if(($row)||($this->ldap->check_LDAP_User($vars["uname"]))){
         $SESSION->set_var('uname',"Username Already Exists");
         $proceed = false;
         }

      /*
      # Check Group Selection - make sure atleast one is selected
      # Currently not needed, users can signup without choosing a group
      if((!isset($vars['group'])) || (count($vars['group'])<1)){
         $SESSION->set_var('group',"Please pick one or more group.");
         $proceed = false;
         }
      */


      # Check Captcha Code
      if($_POST['captcha'] != $_SESSION['captcha']['code']){
         $SESSION->set_var("captchacode", "Security Code Entered Incorrectly");
         $proceed = false;
         }


      # ok to add user to database, return true upon success session
      if($proceed){
         $labgroup="";
         if(isset($vars['group']) && count($vars['group'])){
            foreach ($vars['group'] as $g) { $labgroup.="$g,"; }
            }
         $SESSION->set_var('group',rtrim($labgroup,","));
	      $SESSION->set_var('firstname',$this->misc->cleanInput($vars["firstname"]));
	      $SESSION->set_var('lastname',$this->misc->cleanInput($vars["lastname"]));
	      $SESSION->set_var('email',$this->misc->cleanInput($vars["email"]));
	      $SESSION->set_var('phone',$this->misc->cleanInput($vars["phone"]));
	      $SESSION->set_var('password',sha1($vars["pass1"]));
	      $SESSION->set_var('uname',$this->misc->cleanInput($vars["uname"]));
	      $SESSION->set_var('proceed',true);
         }
      }



   # check password function, return true if meets parameters
   private function checkPassword($passwd1,$passwd2,$regidisp='',$regidispTF=''){
      $pattern_passwd = "/^.*(?=.{5,})(?=.*\d)(?=.*\D).*$/";
      if($passwd1 != $passwd2){ return false; }
      elseif(!preg_match($pattern_passwd, $passwd2)){ return false; }
      else{ return true; }
      }


   # Check & validate phone number, return true if valid
   private function checkPhone($string){
      $numbersOnly = preg_replace('/[^0-9_]/', '', $string);
      $numberOfDigits = strlen($numbersOnly);
      return ($numberOfDigits == 7 or $numberOfDigits == 10)?true:false;
      }

   }
?>
