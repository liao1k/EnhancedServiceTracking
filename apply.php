<?php

if (match_referer() && isset($_POST)) {
  $frm = $_POST;
  
  // Validate the data
  $errormsg = validate_form($frm, $errors);
  if (empty($errormsg)) {
    apply_for_account($frm);
    // If everything works OK, then we can go to 
    // a message page.      
    $t->set_file("apply", "apply_success.html");

  }else{
    $t->set_var("errormsg", $errormsg);
    
    $t->set_var("firstname", nvl($_POST["firstname"]));
    $t->set_var("middlename", nvl($_POST["middlename"]));
    $t->set_var("lastname", nvl($_POST["lastname"]));
    $t->set_var("suffix", nvl($_POST["suffix"]));
    $t->set_var("email", nvl($_POST["email"]));
    $t->set_var("username", nvl($_POST["username"]));
    $t->set_var("title", nvl($_POST["title"]));
    $t->set_var("phone", nvl($_POST["phone"]));
    $t->set_var("beeper", nvl($_POST["beeper"]));
    $t->set_var("department", nvl($_POST["department"]));
		
    $t->set_file("apply", "apply.html");
  }
}else{
  $t->set_file("apply", "apply.html");
}

// build_group_options($groups, nvl('user'));
// $t->set_var("select_group", $groups);

$t->set_file("header", "header.html");
$t->set_file("footer", "footer.html");
$t->parse("Output", "header", true);
$t->parse("Output", "apply", true);
$t->parse("Output", "footer",true);

// Print the Output to the screen
$t->p("Output");

/******************************************************
 * FUNCTIONS
 *****************************************************/
function validate_form(&$frm, &$errors) {
  /* validate the forgot password form, and return the
   * error messages in a string. if the string is 
   * empty, then there are no errors */
  $errors = new Object;
  $errCount = 0;
  $msg = "";
  if (empty($frm["firstname"])) {
    $errCount++;
    $errors->firstname = true;
    $msg .= "<li>You did not specify your first name</li>";

  }
  if (empty($frm["lastname"])) {
    $errCount++;
    $errors->lastname = true;
    $msg .= "<li>You did not specify your last name</li>";

  }
  if (empty($frm["username"])) {
    $errCount++;
    $errors->username = true;
    $msg .= "<li>You did not specify your username</li>";

  } 
  if (empty($frm["email"])) {
    $errCount++;
    $errors->email = true;
    $msg .= "<li>You did not specify your email address</li>";

  } 
  if (empty($frm["phone"])) {
    $errCount++;
    $errors->phone = true;
    $msg .= "<li>You did not specify your phone number</li>";
	
  }

  if (empty($frm["department"])) {
    $errCount++;
    $errors->phone = true;
    $msg .= "<li>You did not specify your department</li>";
	
  }
  $frm["beeper"] = nvl( $frm["beeper"],"");
  $frm["title"] = nvl( $frm["title"],"");
  $frm["icom"] = nvl( $frm["icom"],"");

  /* Checks for uniqueness */
  // We have to do a series of selects on the _user_
  // table and on the Application table.
  $qid = db_query("SELECT email FROM _user_ WHERE email='$frm[email]'");
  if(db_num_rows($qid) != 0){
    $errCount++;
    $errors->email = true;
    $msg .= "<li>You did not specify a unique email address</li>";

  }


  $qid = db_query("SELECT username FROM _user_ 
                   WHERE username='$frm[username]'");

  if(db_num_rows($qid) != 0){
    $errCount++;
    $errors->username = true;
    $msg .= "<li>You did not specify a unique username address</li>";

  }


  if($errCount > 0){
    $msg = "<p>Invalid application data, please try again <dl>" . $msg .
      "</dl></p>";
  }
  return $msg;
}

function apply_for_account($frm) {

  global $CFG, $DOC_TITLE;
  $qid = db_query("INSERT INTO _user_
                   SET firstname= '$frm[firstname]',
                       middlename= '$frm[middlename]',
                       name_suffix= '$frm[suffix]',
                       lastname='$frm[lastname]',
                       department='$frm[department]',
                       email='$frm[email]', 
                       username='$frm[username]',
                       name_suffix='$frm[title]', 
                       phone='$frm[phone]', 
                       beeper='$frm[beeper]'");
 
  mail_user_activation($frm);
}

?>
