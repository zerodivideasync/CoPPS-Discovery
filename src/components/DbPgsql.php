<?php

require_once APP_ROOT_ABS . '/components/propertiesConfig.php';

class DbPgsql extends PDO {

    // Unica istanza di PDO
    static private $pdo = null;
    private static $dbhost = DBHOST;
    private static $dbuser = DBUSER;
    private static $dbpass = DBPASS;
    private static $dbname = DBNAME;

    private function __construct() {
        parent::__construct("pgsql:host=" . DbPgsql::$dbhost . ";dbname=" . DbPgsql::$dbname, DbPgsql::$dbuser, DbPgsql::$dbpass);
        //We can now log any exceptions on Fatal error.
        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Disable emulation of prepared statements, use REAL prepared statements instead.
        $this->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        //Set fetch-assoc as default fetch mode
        $this->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }

    function __destruct() {
        
    }

    final static public function getConnection() {
        if (DbPgsql::$pdo === null) {
            DbPgsql::$pdo = new DbPgsql();
        }
        return DbPgsql::$pdo;
    }

}
