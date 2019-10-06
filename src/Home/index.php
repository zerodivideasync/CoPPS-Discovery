<?php

require_once '../bootstrap.php';
require_once COMPONENTS . 'Session.php';

Session::sec_session_start();
Session::check(POWERGUEST); //every user will be able to access this functionality
require_once COMPONENTS . 'FlashMessageProvider.php';

include TEMPLATES . 'Home/homeTemplate.php';
?>