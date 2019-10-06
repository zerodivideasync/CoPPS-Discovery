<?php

require_once '../bootstrap.php';
require_once COMPONENTS . 'Session.php';
require_once MODELS . 'PathologyDAOPsql.php';

Session::sec_session_start();

// Creates a new pathology and returns the response as a JSON
if (Session::checkNoRedirect(POWERADMIN, false)) {
    $response = array();
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
    if ($name && strlen(trim($name)) > 0) {
        $name = trim($name);
        $res = PathologyDAOPsql::insert($name);
        if ($res) {
            $response["success"] = true;
            $response["msg"] = "Pathology '$name' has been created.";
            $response["id"] = $res;
            $response["name"] = $name;
        } else {
            $response["success"] = false;
            $response["msg"] = "Error: could not create pathology '$name'.";
            $response["name"] = $name;
        }
    } else {
        $response["success"] = false;
        $response["msg"] = "Error: invalid input.";
    }
} else {
    $response["success"] = false;
    $response["msg"] = "Permission denied. To prevent system abuse, only searches can be made; CRUD operations are only available for administrator accounts.";
}
echo json_encode($response);
exit();

