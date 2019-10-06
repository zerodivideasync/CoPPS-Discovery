<?php

/**
 * Interface for the DAO Pattern of the Pollution Src entity
 * 
 * @used-by PollutionSrcDAOPsql
 */
interface PollutionSrcDAO {

    public static function get($id);

    public static function insert($pollution, $type);

    public static function delete($id);

    public static function edit($pollution);

    public static function getAll();

    public static function getByAreaPolygon($location, $except, $date_from, $date_to);

    public static function getByAreaBuffer($location, $except, $date_from, $date_to);

    public static function getByElementDistance($geography_as_text, $range, $date_from, $date_to);

    public static function getGeographyAsText($id, $units_to_expand);
}
