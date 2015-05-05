<?php

/* AJAX check  */

$requestIsAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

if(! ($requestIsAjax && $_SERVER['REQUEST_METHOD'] == 'POST') ) {
  die(); // Don't allow requests to this page unless they're sent via ajax and as a POST
}

// Where are we sending these emails to?
$emailTo = 'nate@greenstreetagency.com';
// What is the email subject line?
$subject = "You've received a message from Greenstreetagency.com";

// If the form is submitted
if(isset($_POST['url']) && $_POST['url'] == '') {

	// Check to make sure sure that a valid email address is submitted
	if(trim($_POST['email']) == '')  {
		$hasError = true;
	} else if (!preg_match("/^[_\.0-9a-zA-Z-]+@([0-9a-zA-Z][0-9a-zA-Z-]+\.)+[a-zA-Z]{2,6}$/i", trim($_POST['email']))) {
		$hasError = true;
	} else {
		$email = trim($_POST['email']);
	}

	// Check to make sure comments were entered
	if(trim($_POST['message']) == '') {
		$hasError = true;
	} else {
		if(function_exists('stripslashes')) {
			$comments = stripslashes(trim($_POST['message']));
		} else {
			$comments = trim($_POST['message']);
		}
	}

	// If there is no error, send the email
	if(!isset($hasError)) {

		ob_start();
		include('contact_email_template.php');
		$body = ob_get_contents();
		ob_end_clean();

		$headers  = 'From: '.$email . "\r\n";
		$headers .= 'Reply-To: ' . $email . "\r\n";
		$headers .= 'Content-Type: text/html; charset=ISO-8859-1' . "\r\n";

		$emailSent = mail($emailTo, $subject, $body, $headers);
  }
  else {
  	$emailSent = false;
  }

  $response = array('success' => $emailSent);

  echo json_encode($response);
}

?>
