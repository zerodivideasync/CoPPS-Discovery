<?php

require_once '../bootstrap.php';
require_once COMPONENTS . 'Session.php';
require_once MODELS . 'PathologyDAOPsql.php';

Session::sec_session_start();
//Edits the pathology identified by $id and returns the response as a JSON
if (Session::checkNoRedirect(POWERADMIN, false)) {
    $response = array();
    $id = filter_input(INPUT_POST, "id", FILTER_SANITIZE_NUMBER_INT);
    $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
    if ($id && $name && $id > 0 && strlen(trim($name)) > 0) {
        $name = trim($name);
        $res = PathologyDAOPsql::edit($id, $name);
        if ($res) {
            $response["success"] = true;
            $response["msg"] = "Pathology '$name' has been edited.";
            $response["id"] = $id;
            $response["name"] = $name;
        } else {
            $response["success"] = false;
            $response["msg"] = "Error: could not edit pathology '$name' ($id).";
            $response["id"] = $id;
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
