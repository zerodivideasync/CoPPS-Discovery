<?php

if (!isset($dir)) {
    $dir = "../";
}

require_once $dir . 'bootstrap.php';
require_once COMPONENTS . 'Session.php';
require_once COMPONENTS . 'PostGisHelper.php';
require_once MODELS . 'Diagnosis.php';
require_once MODELS . 'DiagnosisDAO.php';
require_once DATABASE;

/**
 * Description of DiagnosisDAOPgsql
 *
 */
class DiagnosisDAOPsql implements DiagnosisDAO {

    static private $tablename_diagnoses = "diagnoses";
    static private $tablename_pathologies = "pathologies";

    public static function get($id) {
        $result = NULL;
        $database = DbPgsql::getConnection();
        try {
            $sql = "SELECT d.id, d.date, d.id_pathology, p.name, ST_AsText(location::geography) AS shape "
                    . " FROM " . self::$tablename_diagnoses . " AS d JOIN " . self::$tablename_pathologies . " AS p ON p.id = d.id_pathology "
                    . " WHERE d.id = :id";
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

    /**
     * 
     * @param object $diagnosis The object to store in the database
     * @param string $type Type of shape to insert: marker, circle, polyline or polygon
     * @return integer Id of the inserted object, 0 if the operation failed
     * @throws Exception
     */
    public static function insert($diagnosis, $type) {
        $database = DbPgsql::getConnection();
        $result = null;
        try {
            $date = $diagnosis->getDate();
            $location = $diagnosis->getLocation();
            $idPathology = $diagnosis->getPathology();
            $geom = "";
            $buffer = array($date, $idPathology);
            switch ($type) {
                case 'marker':
                    $location_count = count($location);
                    if ($location_count != PostGisHelper::POINT_MIN_COORDINATES) {
                        throw new Exception("Error insert: not enough coordinates to make a point.");
                    }
                    $geom = PostGisHelper::makePoint();
                    $buffer[] = $location['lng'];
                    $buffer[] = $location['lat'];
                    break;
                case 'circle':
                case 'polyline':
                case 'polygon':
                    throw new Exception("Error insert: shape not supported ($type).");
            }
            $sql = "INSERT INTO " . self::$tablename_diagnoses . " (date, id_pathology, location) VALUES "
                    . "(?, ?, $geom);";
            $stmt = $database->prepare($sql);
            $stmt->execute($buffer);
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

    public static function getAll() {
        $result = NULL;
        $database = DbPgsql::getConnection();
        try {
            $sql = "SELECT d.id, d.date, d.id_pathology, p.name, ST_AsText(location::geography) AS shape "
                    . " FROM " . self::$tablename_diagnoses . " AS d JOIN " . self::$tablename_pathologies . " AS p ON p.id = d.id_pathology;";
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

    public static function delete($id) {
        $database = DbPgsql::getConnection();
        try {
            $sql = 'DELETE FROM ' . self::$tablename_diagnoses . ' '
                    . 'WHERE id = :id';
            $stmt = $database->prepare($sql);
            $stmt->bindValue(':id', $id, $database::PARAM_STR);
            $stmt->execute();
            $res = $stmt->rowCount();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        } finally {
            $database = NULL;
            unset($database);
        }
        return $res;
    }

    public static function edit($diagnosis) {
        $database = DbPgsql::getConnection();
        try {
            $id = $diagnosis->getId();
            $idPathology = $diagnosis->getPathology();
            $date = $diagnosis->getDate();

            $sql = 'UPDATE ' . self::$tablename_diagnoses . ' '
                    . 'SET id_pathology = :id_pathology, date = :date '
                    . 'WHERE id = :id';
            $stmt = $database->prepare($sql);
            $stmt->bindValue(':id', $id, $database::PARAM_STR);
            $stmt->bindValue(':id_pathology', $idPathology, $database::PARAM_STR);
            $stmt->bindValue(':date', $date, $database::PARAM_STR);
            $stmt->execute();
            $res = $stmt->rowCount();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        } finally {
            $database = NULL;
            unset($database);
        }
        return $res;
    }

    /**
     * Retrieves all shapes that intersects the specified area
     * 
     * @param array $location Polygon expressed as array of points ('lat' and 'lng' for each one)
     * @param array $except Array of Id of points to not retrieve because are already loaded on the map
     * @param boolean $id_pathology Id of pathology
     * @return array of Diagnoses
     */
    public static function getByAreaPolygon($location, $except = array(), $date_from = false, $date_to = false, $id_pathology = false) {
        $result = NULL;
        $database = DbPgsql::getConnection();
        $buffer = array();
        $jump = '';
        $id_pathology_check = '';
        $date_from_check = '';
        $date_to_check = '';
        try {
            $location_count = count($location);
            $geom = PostGisHelper::makePolygon($location_count + 1);
            for ($start = 0; $start < $location_count; $start++) {
                $buffer[] = $location[$start]['lng'];
                $buffer[] = $location[$start]['lat'];
            }
            $buffer[] = $location[0]['lng']; //Last point
            $buffer[] = $location[0]['lat'];
            if (is_array($except) && count($except) > 0) { //If there are points to exclude from the query
                $qst_mark = [];
                for ($i = count($except) - 1; $i >= 0; $i--) {
                    $qst_mark[] = '?'; //Array of question marks that will 'implode' to make parameters binding
                }
                $jump = ' AND d.id NOT IN (' . implode(',', $qst_mark) . ')';
                $buffer = array_merge($buffer, $except);
            }
            if ($id_pathology && is_numeric($id_pathology)) {
                $id_pathology_check = " AND d.id_pathology = $id_pathology ";
            }
            if ($date_from) {
                $date_from_check = " AND d.date >= ? ";
                $buffer = array_merge($buffer, array($date_from));
            }
            if ($date_to) {
                $date_to_check = " AND d.date <= ? ";
                $buffer = array_merge($buffer, array($date_to));
            }
            $sql = 'SELECT d.id, d.date, d.id_pathology, p.name, ST_AsText(location::geography) AS shape '
                    . " FROM " . self::$tablename_diagnoses . " AS d JOIN " . self::$tablename_pathologies . " AS p ON p.id = d.id_pathology "
                    . 'WHERE ST_INTERSECTS(d.location, ' . $geom . ') ' . $id_pathology_check . $jump . $date_from_check . $date_to_check . ' '
                    . 'ORDER BY ST_AREA(d.location) DESC;';
            $stmt = $database->prepare($sql);
            $stmt->execute($buffer);
            $result = $stmt->fetchAll();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        } finally {
            $database = NULL;
            unset($database);
        }
        return $result;
    }

    /**
     * Retrieves all shapes that intersects the specified area
     * 
     * @param array $location Circle expressed as array of points ('lat' and 'lng' for the center, 'radius' for the radius (you don't say!))
     * @param array $except Array of Id of points to not retrieve because are already loaded on the map
     * @return array of Pollution Sources
     */
    public static function getByAreaBuffer($location, $except = array(), $date_from = false, $date_to = false) {
        $result = NULL;
        $database = DbPgsql::getConnection();
        $buffer = array();
        $jump = '';
        $date_from_check = '';
        $date_to_check = '';
        try {
            $location_count = 2;
            $geom = PostGisHelper::makeCircle();
            $buffer[] = $location['lng'];
            $buffer[] = $location['lat'];
            $buffer[] = $location['radius'];
            if (is_array($except) && count($except) > 0) { //If there are points to exclude from the query
                $qst_mark = [];
                for ($i = count($except) - 1; $i >= 0; $i--) {
                    $qst_mark[] = '?'; //Array of question marks that will 'implode' to make parameters binding
                }
                $jump = ' AND id NOT IN (' . implode(',', $qst_mark) . ')';
                $buffer = array_merge($buffer, $except);
            }
            if ($date_from) {
                $date_from_check = " AND d.date >= ? ";
                $buffer = array_merge($buffer, array($date_from));
            }
            if ($date_to) {
                $date_to_check = " AND d.date <= ? ";
                $buffer = array_merge($buffer, array($date_to));
            }
            $sql = 'SELECT d.id, d.date, d.id_pathology, p.name, ST_AsText(location::geography) AS shape '
                    . " FROM " . self::$tablename_diagnoses . " AS d JOIN " . self::$tablename_pathologies . " AS p ON p.id = d.id_pathology "
                    . 'WHERE ST_INTERSECTS(d.location, ' . $geom . ') ' . $jump . $date_from_check . $date_to_check . ' '
                    . 'ORDER BY ST_AREA(d.location) DESC;';
            $stmt = $database->prepare($sql);
            $stmt->execute($buffer);
            $result = $stmt->fetchAll();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        } finally {
            $database = NULL;
            unset($database);
        }
        return $result;
    }

    public static function getByElementDistance($geography_as_text, $range, $date_from, $date_to) {
        $result = NULL;
        $database = DbPgsql::getConnection();
        $date_from_check = '';
        $date_to_check = '';
        $date_from_check_subquery = '';
        $date_to_check_subquery = '';
        try {
            if ($date_from) {
                $date_from_check = " AND d.date >= :date_from ";
                $date_from_check_subquery = " AND t.date >= :date_from ";
            }
            if ($date_to) {
                $date_to_check = " AND d.date <= :date_to ";
                $date_to_check_subquery = " AND t.date <= :date_to ";
            }
            $sql = "SELECT d.id, d.date, ST_AsText(d.location::geography) AS shape, pathologies_occurrences.*
					FROM " . self::$tablename_diagnoses . " AS d JOIN (
							SELECT p.id as id_pathology, p.name,  COUNT(*) AS pathology_occurrence
							FROM " . self::$tablename_diagnoses . " AS t JOIN " . self::$tablename_pathologies . " AS p ON id_pathology = p.id
							WHERE ST_DWithin(t.location, :geography_as_text::geography, :range) $date_from_check_subquery $date_to_check_subquery
							GROUP BY p.id
					) as pathologies_occurrences ON d.id_pathology = pathologies_occurrences.id_pathology
					WHERE ST_DWithin(d.location, :geography_as_text::geography, :range) $date_from_check $date_to_check
					ORDER BY pathologies_occurrences.pathology_occurrence DESC, pathologies_occurrences.id_pathology ASC";
            $stmt = $database->prepare($sql);
            $stmt->bindValue(':geography_as_text', $geography_as_text, $database::PARAM_STR);
            $stmt->bindValue(':range', $range, $database::PARAM_STR);
            if ($date_from) {
                $stmt->bindValue(':date_from', $date_from, $database::PARAM_STR);
            }
            if ($date_to) {
                $stmt->bindValue(':date_to', $date_to, $database::PARAM_STR);
            }

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

    public static function getGeographyAsText($id, $units_to_expand = 0) {
        $result = NULL;
        $database = DbPgsql::getConnection();
        try {
            $sql = 'SELECT d.id, p.name, d.date, ST_AsText(d.location::geography) AS shape, ST_AsText(ST_Buffer(d.location::geography,:units_to_expand)::geography) AS shape_expanded '
                    . " FROM " . self::$tablename_diagnoses . " AS d JOIN " . self::$tablename_pathologies . " AS p ON p.id = d.id_pathology"
                    . ' WHERE d.id = :id';
            $stmt = $database->prepare($sql);
            $stmt->bindValue(':id', $id, $database::PARAM_STR);
            $stmt->bindValue(':units_to_expand', $units_to_expand, $database::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $exc) {
            echo $exc->getMessage();
        } finally {
            $database = NULL;
            unset($database);
        }
        return $result;
    }

    public static function countByPathology($idPathology) {
        $result = 0;
        $database = DbPgsql::getConnection();
        try {
            $sql = "SELECT COUNT(*) AS counter "
                    . " FROM " . self::$tablename_diagnoses . " AS d"
                    . " WHERE d.id_pathology = :idPathology;";
            $stmt = $database->prepare($sql);
            $stmt->bindValue(':idPathology', $idPathology, $database::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetchColumn();
        } catch (Exception $exc) {
            echo $exc->getMessage();
        } finally {
            $database = NULL;
            unset($database);
        }
        return $result;
    }

}
