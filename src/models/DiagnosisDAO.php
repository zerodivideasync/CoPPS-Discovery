<?php

/**
 * Interface for the DAO Pattern of the Diagnosis entity
 * 
 * @used-by DiagnosisDAOPsql
 */
interface DiagnosisDAO {

    public static function get($id);

    public static function insert($diagnosis, $type);

    public static function delete($id);

    public static function edit($diagnosis);

    public static function getAll();

    public static function getByAreaPolygon($location, $except, $date_from, $date_to, $id_pathology);

    public static function getByAreaBuffer($location, $except, $date_from, $date_to);

    public static function getByElementDistance($geography_as_text, $range, $date_from, $date_to);

    public static function getGeographyAsText($id, $units_to_expand);

    public static function countByPathology($idPathology);
}
