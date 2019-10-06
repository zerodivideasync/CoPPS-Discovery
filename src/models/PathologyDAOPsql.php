<?php

if (!isset($dir)) {
    $dir = "../";
}

require_once $dir . 'bootstrap.php';
require_once COMPONENTS . 'Session.php';
require_once MODELS . 'Pathology.php';
require_once MODELS . 'PathologyDAO.php';
require_once DATABASE;

/**
 * This class contains the Pathology related methods for PostreSQL dbms
 * 
 * @var string $tablename Name of the table representing the Pathology entity in PostgreSQL dbms
 * 
 */
class PathologyDAOPsql implements PathologyDAO {

    static private $tablename = "pathologies";

    public static function get($id) {
        $result = NULL;
        $database = DbPgsql::getConnection();
        try {
            $sql = 'SELECT id, name FROM ' . self::$tablename . ' WHERE id = :id';
            $stmt = $database->prepare($sql);

            $stmt->bindValue(':id', $id, $database::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        } finally {
            $database = NULL;
            unset($database);
        }
        return $result;
    }

    public static function getAll() {
        $result = NULL;
        $database = DbPgsql::getConnection();
        try {
            $sql = 'SELECT id, name FROM ' . self::$tablename . ';';
            $stmt = $database->prepare($sql);

            $stmt->execute();
            $result = $stmt->fetchAll();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        } finally {
            $database = NULL;
            unset($database);
        }
        return $result;
    }

    public static function getByName($name) {
        $result = NULL;
        $database = DbPgsql::getConnection();
        try {
            $sql = 'SELECT id, name FROM ' . self::$tablename . ' WHERE LOWER(name) = :name';
            $stmt = $database->prepare($sql);

            $stmt->bindValue(':name', strtolower($name), $database::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        } finally {
            $database = NULL;
            unset($database);
        }
        return $result;
    }

    public static function insert($name) {
        $database = DbPgsql::getConnection();
        $result = null;
        try {
            $sql = 'INSERT INTO ' . self::$tablename . ' (name) VALUES (:name);';

            $stmt = $database->prepare($sql);

            $stmt->bindValue(':name', $name, $database::PARAM_STR);

            $stmt->execute();
            $res = $stmt->fetchAll();
            $result = $database->lastInsertId();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        } finally {
            $database = NULL;
            unset($database);
        }
        return $result;
    }

    /**
     * Edits and stores the user identified by $id with new values
     */
    public static function edit($id, $name) {
        $database = DbPgsql::getConnection();
        $result = null;
        try {
            $sql = 'UPDATE ' . self::$tablename . ' '
                    . 'SET name = :name '
                    . 'WHERE id = :id';
            $stmt = $database->prepare($sql);
            $stmt->bindValue(':id', $id, $database::PARAM_STR);
            $stmt->bindValue(':name', $name, $database::PARAM_STR);
            $stmt->execute();
            $result = $stmt->rowCount();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        } finally {
            $database = NULL;
            unset($database);
        }
        return $result;
    }

    public static function delete($id) {
        $database = DbPgsql::getConnection();
        $result = null;
        try {
            $sql = 'DELETE FROM ' . self::$tablename . ' '
                    . 'WHERE id = :id';
            $stmt = $database->prepare($sql);
            $stmt->bindValue(':id', $id, $database::PARAM_STR);
            $stmt->execute();
            $result = $stmt->rowCount();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        } finally {
            $database = NULL;
            unset($database);
        }
        return $result;
    }

}
