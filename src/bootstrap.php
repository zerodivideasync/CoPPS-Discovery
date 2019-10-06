<?php
/**
 * This file defines some important constants for referring to files.
 * APP_ROOT_ABS is an absolute path to the root directory of the app.
 * APP_ROOT is the path to the root directory of the app relative to the server's document root (useful when writing css/js links i.e.)
 */
/**********************/
/*    DOMAIN SERVER   */
/**********************/
define("SITENAME", "CoPPS Discovery");
define('APP_ROOT_ABS', dirname(__FILE__));
//rel_root contains path to to the folder containing bootstrap.php relative to webserver DOCUMENT_ROOT
//$rel_root = preg_replace('/[\\\\\\/]+/', '/', '/' . substr(__DIR__, strlen($_SERVER['DOCUMENT_ROOT'])) . '/' );
//define('APP_ROOT', $rel_root);
define('APP_ROOT', preg_replace('/[\\\\\\/]+/', '/', '/' . substr(__DIR__, strlen(filter_INPUT(INPUT_SERVER, "DOCUMENT_ROOT"))) . '/'));
define('TEMPLATES', APP_ROOT_ABS . '/templates/');				  // All views
define('TPL_PARTS', APP_ROOT_ABS . '/templates/template-parts/');
define('HELPERS', APP_ROOT_ABS . '/templates/helpers/');
define('MODELS', APP_ROOT_ABS . '/models/');					   // All entity Classes
define('COMPONENTS', APP_ROOT_ABS . '/components/');
//define('ACTIONS', APP_ROOT_ABS . '/actions/' );                     // All Action process pages
define('DATABASE', APP_ROOT_ABS.'/components/DbPgsql.php');
define('HOME', APP_ROOT . 'Home/');

define('DATETIMEDMY','d/m/Y');
define('DATETIMEYMD','Y-m-d');

// autoload Composer packages
//require_once(dirname(__DIR__) . "/vendor/autoload.php");
require_once(APP_ROOT_ABS . "/vendor/autoload.php");