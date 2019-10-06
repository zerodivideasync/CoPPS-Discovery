<?php

require_once '../bootstrap.php';
require_once MODELS . 'PathologyDAOPsql.php';
// autoload Composer packages
require_once(dirname(__DIR__) . "/vendor/autoload.php");

use tpmanc\csvhelper\CsvHelper;

$filename = "pathologies_export_" . date("Y-m-d") . ".csv";
// disable caching
$now = gmdate("D, d M Y H:i:s");
header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
header("Last-Modified: {$now} GMT");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Disposition: attachment;filename={$filename}");
header("Content-Transfer-Encoding: binary");

$file = CsvHelper::create()->delimiter(',');
$file->encode('cp1251', 'utf-8'); // change encoding
$pathologies = PathologyDAOPsql::getAll();
if ($pathologies) {
    foreach ($pathologies as $pathology) {
        $file->addLine("$pathology[id],$pathology[name]"); // add row to file by string
    }
}
$file->save("php://output");
die();
