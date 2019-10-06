<?php

if (!isset($dir)) {
    $dir = "../";
}

require_once $dir . 'bootstrap.php';
require_once COMPONENTS . 'Session.php';
require_once COMPONENTS . 'PostGisHelper.php';
require_once MODELS . 'PollutionSrc.php';
require_once MODELS . 'PollutionSrcDAO.php';
require_once DATABASE;

/**
 * This class contains the Pollution Source related methods for PostreSQL dbms
 * 
 * @var string $tablename Name of the table representing the Pollution Source entity in PostgreSQL dbms
 * 
 */
class PollutionDAOPsql implements PollutionSrcDAO {

    static private $tablename = "pollution_srcs";

    public static function get($id) {
        $result = NULL;
        $database = DbPgsql::getConnection();
        try {
            $sql = 'SELECT id, name, date_from, date_to, ST_AsText(location::geography) AS shape FROM ' . self::$tablename . ' '
                    . 'WHERE id = :id';
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
     * @param object $pollution The object to store in the database
     * @param string $type Type of shape to insert: marker, circle, polyline or polygon
     * @return integer Id of the inserted object, 0 if the operation failed
     * @throws Exception
     */
    public static function insert($pollution, $type) {
        $database = DbPgsql::getConnection();
        $result = null;
        try {
            $name = $pollution->getName();
            $dateFrom = $pollution->getDateFrom();
            $dateTo = $pollution->getDateTo();
            $location = $pollution->getLocation();
            $geom = "";
            $buffer = array($name, $dateFrom, $dateTo);
            switch ($type) {
                case 'marker':
                    $location_count = count($location);
                    if ($location_count != PostGisHelper::POINT_MIN_COORDINATES) {
                        throw new Exception("Error insert: not enough coordinates to make a point.");
                    }
                    $geom = PostGisHelper::makePoint();
                    $buffer[] = $location['lng'];
                    $buffer[] = $location['lat'];
//					print_r($buffer); exit();
                    break;
                case 'circle':
                    $location_count = count($location);
                    if ($location_count != PostGisHelper::POINT_MIN_COORDINATES + 1) { //Center + radius
                        throw new Exception("Error insert: not enough coordinates to make a point.");
                    }
                    $geom = PostGisHelper::makeCircle();
                    $buffer[] = $location['lng'];
                    $buffer[] = $location['lat'];
                    $buffer[] = $location['radius'];
                    break;
                case 'polyline':
                    $location = $location['points'];
                    $location_count = count($location);
                    $geom = PostGisHelper::makeLine($location_count);
                    for ($start = 0; $start < $location_count; $start++) {
                        $buffer[] = $location[$start]['lng'];
                        $buffer[] = $location[$start]['lat'];
                    }
                    break;
                case 'polygon':
                    $location = $location['points'];
                    $location_count = count($location);
                    $geom = PostGisHelper::makePolygon($location_count + 1); //One more point to end where all started
                    for ($start = 0; $start < $location_count; $start++) {
                        $buffer[] = $location[$start]['lng'];
                        $buffer[] = $location[$start]['lat'];
                    }
                    $buffer[] = $location[0]['lng']; //Last point
                    $buffer[] = $location[0]['lat'];
                    break;
            }
            $sql = "INSERT INTO " . self::$tablename . " (name, date_from, date_to, location) VALUES "
                    . "(?, ?, ?, $geom);";
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
            $sql = 'SELECT id, name, date_from, date_to, ST_AsText(location::geography) AS shape FROM ' . self::$tablename . ';';
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
            $sql = 'DELETE FROM ' . self::$tablename . ' '
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

    public static function edit($pollution) {
        $database = DbPgsql::getConnection();
        try {
            $id = $pollution->getId();
            $name = $pollution->getName();
            $dateFrom = $pollution->getDateFrom();
            $dateTo = $pollution->getDateTo();

            $sql = 'UPDATE ' . self::$tablename . ' '
                    . 'SET name = :name, date_from = :date_from, date_to = :date_to '
                    . 'WHERE id = :id';
            $stmt = $database->prepare($sql);
            $stmt->bindValue(':id', $id, $database::PARAM_STR);
            $stmt->bindValue(':name', $name, $database::PARAM_STR);
            $stmt->bindValue(':date_from', $dateFrom, $database::PARAM_STR);
            $stmt->bindValue(':date_to', $dateTo, $database::PARAM_STR);
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
     * @return array of Pollution Sources
     */
    public static function getByAreaPolygon($location, $except = array(), $date_from = false, $date_to = false) {
        $result = NULL;
        $database = DbPgsql::getConnection();
        $buffer = array();
        $jump = '';
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
                $jump = ' AND id NOT IN (' . implode(',', $qst_mark) . ')';
                $buffer = array_merge($buffer, $except);
            }
            if ($date_from) {
                $date_from_check = " AND date_from >= ? ";
                $buffer = array_merge($buffer, array($date_from));
            }
            if ($date_to) {
                $date_to_check = " AND date_to <= ? ";
                $buffer = array_merge($buffer, array($date_to));
            }
            $sql = 'SELECT id, name, date_from, date_to, ST_AsText(location::geography) AS shape '
                    . 'FROM ' . self::$tablename . ' AS s '
                    . 'WHERE ST_INTERSECTS(s.location, ' . $geom . ') ' . $jump . $date_from_check . $date_to_check . ' '
                    . 'ORDER BY ST_AREA(location) DESC;';
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
                $date_from_check = " AND date_from >= ? ";
                $buffer = array_merge($buffer, array($date_from));
            }
            if ($date_to) {
                $date_to_check = " AND date_to <= ? ";
                $buffer = array_merge($buffer, array($date_to));
            }
            $sql = 'SELECT id, name, date_from, date_to, ST_AsText(location::geography) AS shape '
                    . 'FROM ' . self::$tablename . ' AS s '
                    . 'WHERE ST_INTERSECTS(s.location, ' . $geom . ') ' . $jump . $date_from_check . $date_to_check . ' '
                    . 'ORDER BY ST_AREA(location) DESC;';
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
        try {
            if ($date_from) {
                $date_from_check = " AND date_from >= :date_from ";
            }
            if ($date_to) {
                $date_to_check = " AND date_to <= :date_to ";
            }
            $sql = "SELECT id, name, date_from, date_to, ST_AsText(location::geography) AS shape "
                    . " FROM " . self::$tablename . " "
                    . " WHERE ST_DWithin(location, :geography_as_text::geography, :range) $date_from_check $date_to_check";
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
            $sql = 'SELECT id, name, date_from, date_to, ST_AsText(location::geography) AS shape, ST_AsText(ST_Buffer(location::geography,:units_to_expand)::geography) AS shape_expanded '
                    . ' FROM ' . self::$tablename . ''
                    . ' WHERE id = :id';
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

}
