<?php

require_once '../bootstrap.php';
require_once COMPONENTS . 'Session.php';
require_once MODELS . 'PollutionSrcDAOPsql.php';

Session::sec_session_start();
Session::check(POWERGUEST); //every user will be able to access this functionality
require_once COMPONENTS . 'FlashMessageProvider.php';
require_once COMPONENTS . 'JsonHelper.php';

$filters = array(//This array is used for data-sanitizing. It represents the structure of the arrays contained in $_POST ('shapes_to_*')  
    'id' => FILTER_VALIDATE_INT,
    'name' => FILTER_SANITIZE_STRING,
    'dateFrom' => FILTER_SANITIZE_STRING, //'/([0-9]{2}\/){2}[0-9]{4}/',
    'dateTo' => FILTER_SANITIZE_STRING,
    'type' => FILTER_SANITIZE_STRING,
    'values' => array(
        'lat' => FILTER_VALIDATE_FLOAT,
        'lng' => FILTER_VALIDATE_FLOAT,
        'radius' => FILTER_VALIDATE_FLOAT,
        'points' => array(
            'lat' => FILTER_VALIDATE_FLOAT,
            'lng' => FILTER_VALIDATE_FLOAT
        )
    )
);

if (!empty($_POST) && Session::checkNoRedirect(POWERADMIN)) {
//Functionalities are splitted in different files for better readability
    require_once '_insert_part.php'; //Manages the 'shapes_to_insert' array
    require_once '_edit_part.php'; //Manages the 'shapes_to_edit' array
    require_once '_delete_part.php'; //Manages the 'shapes_to_delete' array
}
include TEMPLATES . 'PollutionSrc/indexTemplate.php';
