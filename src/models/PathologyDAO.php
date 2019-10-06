<?php

/**
 * Interface for the DAO Pattern of the Pathology entity
 * 
 * @used-by PathologyDAOPsql
 */
interface PathologyDAO {

    public static function get($id);

    public static function getAll();

    public static function getByName($name);

    public static function insert($name);

    public static function edit($id, $name);

    public static function delete($id);
}
