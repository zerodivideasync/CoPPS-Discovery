<?php

if (!isset($dir)) {
    $dir = "../";
}

require_once $dir . 'bootstrap.php';
require_once APP_ROOT_ABS . '/components/DateHelper.php';

require_once DATABASE;

class PostGisHelper {

    const SRID = 4326;
    const POINT_MIN_COORDINATES = 2;
    const LINE_MIN_POINTS = 2;
    const POLYGON_MIN_POINTS = 4;

    static public function setSrid($sql, $srid = self::SRID) {
        return "ST_SetSRID($sql, $srid)";
    }

    static public function makePoint() {
        return self::setSrid("ST_MakePoint(?, ?)");
    }

    static public function makeLine($points = self::LINE_MIN_POINTS) {
        if ($points < self::LINE_MIN_POINTS) {
            throw new Exception("PostGisHelper: not enough points to make a line.");
        }
        $p = array();
        while ($points-- > 0) {
            $p[] = self::makePoint();
        }
        return self::setSrid("ST_MakeLine(ARRAY[" . implode(',', $p) . '])');
    }

    static public function makeCircle() {
        return self::setSrid("ST_Buffer(" . self::makePoint() . "::geography, ?)");
    }

    static public function makePolygon($points = self::POLYGON_MIN_POINTS) {
        if ($points < self::POLYGON_MIN_POINTS) {
            throw new Exception("PostGisHelper: not enough points to make a polygon.");
        }
        $p = array();
        while ($points-- > 0) {
            $p[] = self::makePoint();
        }
        return self::setSrid("ST_MakePolygon(ST_MakeLine(ARRAY[" . implode(',', $p) . ']))');
    }

    static public function extractData($string) {
        if (!$string) {
            return false;
        }
        $first_p = stripos($string, "(");
        if ($first_p === false) {
            return false;
        }
        $shape_type = substr($string, 0, $first_p);
        switch ($shape_type) {
            case 'POINT':
            case 'LINESTRING':
                $string_points = substr($string, $first_p + 1, strlen($string) - $first_p - 2);
                $points = explode(",", $string_points);
                $coordinates = array();
                foreach ($points as $point) {
                    $coordinates[] = explode(" ", $point);
                }
                break;
            case 'POLYGON':
                $string_points = substr($string, $first_p + 2, strlen($string) - $first_p - 4);
                $points = explode(",", $string_points);
                $coordinates = array();
                foreach ($points as $point) {
                    $coordinates[] = explode(" ", $point);
                }
                break;
            default:
                return false;
        }
        return array('type' => $shape_type, 'coordinates' => $coordinates);
    }

}
