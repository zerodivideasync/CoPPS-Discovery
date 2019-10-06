<?php

/**
 * Controller LOGIN for User
 */
require_once '../bootstrap.php';
require_once COMPONENTS . 'Session.php';
require_once MODELS . 'User.php';
require_once MODELS . 'UserDAOPsql.php';
require_once COMPONENTS . 'ErrorMessages.php';
require_once COMPONENTS . 'FlashMessageProvider.php';

Session::sec_session_start();
//there is not need to use Session:check on login functionality! Users accessing it won't be logged in
if( Session::isLogged() ) {
	FlashMessageProvider::success(['message' => 'User already logged. Redirected.', 'icon' => 'check']);
	header('Location: ' . HOME); //Location: / would not work if app is not installed in document root.
}

$RequestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_STRING);

if ($RequestMethod === 'POST') { //then we try to authenticate the user
	$username = filter_input(INPUT_POST, 'username');
	$password = filter_input(INPUT_POST, 'password');

	if (!empty($username) && !empty($password)) {

		$u = new User(NULL, $username, $password, NULL);
		$dao = new UserDAOPsql();
		$res = $dao->checkLogin($u);

		if ($res > 0) { //user authenticated. We set a success message and redirect to the homepage
			FlashMessageProvider::success(['message' => 'Login successful ', 'icon' => 'check']);
			header('Location: ' . HOME); //Location: / would not work if app is not installed in document root. Use APP_ROOT instead
		} else { //authentication failed. We set a message to be displayed and display the login form.
			FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Login failed ' . ErrorMessages::login($res), 'icon' => 'exclamation-triangle']);
			include_once TEMPLATES . 'Users/loginTemplate.php';
			exit;
		}
	} else {
		// if some smart guy sent the form with empty values, like a pro!!    
		FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Login failed ' . ErrorMessages::login(0), 'icon' => 'exclamation-triangle']);
		include_once TEMPLATES . 'Users/loginTemplate.php';
		exit;
	}
} else { //we show the login page
	include_once TEMPLATES . 'Users/loginTemplate.php';
}