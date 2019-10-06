<?php

require_once '../bootstrap.php';
require_once COMPONENTS . 'Session.php';
require_once MODELS . 'PathologyDAOPsql.php';

Session::sec_session_start();
Session::check(POWERGUEST); //every user will be able to access this functionality
require_once COMPONENTS . 'FlashMessageProvider.php';

$pathologies = PathologyDAOPsql::getAll(); //Shows all pathologies

include TEMPLATES . 'Pathology/pathologyTemplate.php';
