<?php

// autoload Composer packages
require_once(dirname(__DIR__) . "/vendor/autoload.php");

use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\DataSet\QueryDataSet;
use PHPUnit\DbUnit\Database\Connection;

$dir = "./"; //per bootstrap.php

abstract class AbstractDBTestCase extends TestCase {

//    use TestCaseTrait;

//    // Unica istanza di PDO
//    static private $pdo = null;
//    // Istanza unica anche per PHPUnit_Extensions_Database_DB_IDatabaseConnection. Usato una volta per test
//    private $conn = null;
//
//    final public function getConnection() {
//        if ($this->conn === null) {
//            $this->conn = $this->createConnection();
//        }
//        return $this->conn;
//    }
//
//    /**
//     * Crea un database in memoria o crea tutto su un database reale. I dati del reale vengono presi da phpunit.xml
//     * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
//     */
//    private function createConnection() {
//        if ($GLOBALS['MAKE_IN_MEMORY_DB'] == 'yes') {
//            return $this->createInMemory();
//        } else {
//            return $this->createRealDbConnection();
//        }
//    }
//
//    private function createInMemory() {
//        if (self::$pdo == null) {
//            self::$pdo = new PDO('sqlite::memory:');
//            self::initDatabase();
//        }
//        return $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
//    }
//
//    private function createRealDbConnection() {
//        if (self::$pdo == null) {
//            self::$pdo = new PDO($GLOBALS['DB_DSN'], $GLOBALS['DB_USER'], $GLOBALS['DB_PASSWD']);
//        }
//        return $this->createDefaultDBConnection(self::$pdo, $GLOBALS['DB_DBNAME']);
//    }
//
//    /**
//     * Inizializza il db in memoria.
//     */
//    public static function initDatabase() {
//        $query = "CREATE TABLE IF NOT EXISTS pathologies (
//                            id SERIAL PRIMARY KEY,
//                            name VARCHAR(64) NOT NULL	
//                    );
//
//                    CREATE TABLE IF NOT EXISTS diagnoses (
//                            id SERIAL PRIMARY KEY,
//                            date DATE NOT NULL,
//                            id_pathology INTEGER REFERENCES pathologies(id) ON DELETE RESTRICT,
//                            location GEOGRAPHY NOT NULL
//                    );
//
//                    CREATE TABLE IF NOT EXISTS pollution_srcs (
//                            id SERIAL PRIMARY KEY,
//                            name VARCHAR NOT NULL,
//                            location GEOGRAPHY NOT NULL,
//                            date_from DATE NOT NULL,
//                            date_to DATE
//                    );
//
//                    CREATE TABLE IF NOT EXISTS users (
//                            id SERIAL PRIMARY KEY,
//                            username VARCHAR(16) NOT NULL,
//                            password VARCHAR NOT NULL,
//                            power INTEGER DEFAULT 1
//                    );";
//        self::$pdo->query($query);
//    }

}
