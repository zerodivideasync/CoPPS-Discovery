<?php

if (!isset($dir)) {
    $dir = "../";
}

require_once $dir . 'bootstrap.php';
require_once COMPONENTS . 'Session.php';

Session::sec_session_start();
Session::check(POWERGUEST); //every user will be able to access this functionality

$ds = DIRECTORY_SEPARATOR;
$storeFolder = 'uploads';
//print_r($_FILES);
if (!empty($_FILES)) {
    $type = false;
    $tempFile = false;
    $targetPath = $storeFolder . $ds;
    if (isset($_FILES['file_pathologies'])) {
        $tempFile = $_FILES['file_pathologies']['tmp_name'];
        $targetFile = $targetPath . $_FILES['file_pathologies']['name'];
        $type = 'pathology';
    }
//    if (isset($_FILES['file_diagnoses'])) {
//        $tempFile = $_FILES['file_diagnoses']['tmp_name'];
//        $targetFile = $targetPath . $_FILES['file_diagnoses']['name'];
//        $type = 'diagnosis';
//    }
//    if (isset($_FILES['file_pollutionsrcs'])) {
//        $tempFile = $_FILES['file_pollutionsrcs']['tmp_name'];
//        $targetFile = $targetPath . $_FILES['file_pollutionsrcs']['name'];
//        $type = 'pollution_src';
//    }

    if (file_exists($tempFile)) {
//        if( !move_uploaded_file($tempFile, $targetFile)) {
//            print_r("error upload file: $targetFile");
//        }
    } else {
        print_r("error file: $tempFile");
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
    }

    if ($type !== false) {
        require_once '_import.php';
    }
}