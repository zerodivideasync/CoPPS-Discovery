<?php

/**
 * Controller LOGOUT for User
 */
require_once '../bootstrap.php';
require_once COMPONENTS . 'Session.php';
require_once COMPONENTS . 'FlashMessageProvider.php';

Session::sec_closeSession(); //close previous session

FlashMessageProvider::success(['message' => 'Logout successful']);
header('Location: ' . APP_ROOT); //redirect to root
exit;
