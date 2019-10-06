<?php

require_once APP_ROOT_ABS . '/components/propertiesConfig.php';

class DbPgsql extends PDO {

    // Unica istanza di PDO
    static private $pdo = null;
    static private $IN_MEMORY_DB = false;
    private static $dbhost = DBHOST;
    private static $dbuser = DBUSER;
    private static $dbpass = DBPASS;
    private static $dbname = DBNAME;

    private function __construct() {
        if(DbPgsql::$IN_MEMORY_DB === true) {
            parent::__construct('sqlite::memory:');
        } else {
            parent::__construct("pgsql:host=" . DbPgsql::$dbhost . ";dbname=" . DbPgsql::$dbname, DbPgsql::$dbuser, DbPgsql::$dbpass);
        }
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
            if(DbPgsql::$IN_MEMORY_DB === true) {
                DbPgsql::initDatabase();
            }
        }
        return DbPgsql::$pdo;
    }

    /**
     * Inizializza il db in memoria.
     */
    final private static function initDatabase() {
        $query = "CREATE TABLE IF NOT EXISTS pathologies (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            name VARCHAR(64) NOT NULL	
                    );

                    CREATE TABLE IF NOT EXISTS diagnoses (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            date DATE NOT NULL,
                            id_pathology INTEGER REFERENCES pathologies(id) ON DELETE RESTRICT,
                            location GEOGRAPHY NOT NULL
                    );

                    CREATE TABLE IF NOT EXISTS pollution_srcs (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            name VARCHAR NOT NULL,
                            location GEOGRAPHY NOT NULL,
                            date_from DATE NOT NULL,
                            date_to DATE
                    );

                    CREATE TABLE IF NOT EXISTS users (
                            id INTEGER PRIMARY KEY AUTOINCREMENT,
                            username VARCHAR(16) NOT NULL,
                            password VARCHAR NOT NULL,
                            power INTEGER DEFAULT 1
                    );";
        $query .= " " . file_get_contents('src/sql/popolamento.sql',TRUE); /* Si prende i dati del popolamento dai file originali */
        DbPgsql::$pdo->exec($query);
    }

}
