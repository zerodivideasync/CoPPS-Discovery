<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once MODELS . 'PathologyDAOPsql.php';

// autoload Composer packages
require_once(dirname(__DIR__) . "/vendor/autoload.php");

require_once COMPONENTS . 'FlashMessageProvider.php';
require_once COMPONENTS . 'JsonHelper.php';

use tpmanc\csvhelper\CsvHelper;

Session::check(POWERGUEST); //every user will be able to access this functionality


$json = array();
try {
    $validator = new PhpCsvValidator();
    $validator->loadSchemeFromFile("pathology_schema.json");
    if (!$validator->isValidFile($tempFile)) {
        $json['error'] = "Error: File is not well-formed.";
    } else {
        switch ($type) {
            case 'pathology':
                $json['success'] = true;
                $json['pathologies'] = array();
                CsvHelper::open($tempFile)->delimiter(',')->parse(function($line) use(&$json) {
                    // ID : NAME
                    $name = $line[1];
                    $exists = PathologyDAOPsql::getByName($name);
                    if ($exists) {
                        $json['pathologies'][] = array('id' => $line[0], 'name' => $line[1], 'status' => '<span style="color: #ffc107;">Already inserted.</span>');
                    } else {
                        $id = false;
                        $id = PathologyDAOPsql::insert($name);
                        if (!$id) {
                            $json['pathologies'][] = array('id' => $line[0], 'name' => $line[1], 'status' => '<span style="color: #dc3545;">Insert error.</span>');
                        } else {
                            $json['pathologies'][] = array('id' => $line[0], 'name' => $line[1], 'status' => '<span style="color: #17a2b8;">Ok.</span>');
                        }
                    }
                });
                break;
            default:
                $json['error'] = "Error: unkown type.";          
        }
    }
} catch (PhpCsvValidatorException $ex) {
    $json['error'] = "Error: " . $ex->getExceptionMessage();
} finally {
    if (file_exists($tempFile)) {
        unlink($tempFile);
    }
}

echo json_encode($json);
