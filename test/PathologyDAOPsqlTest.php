<?php

require_once 'AbstractDBTestCase.php';
$dir = "./"; //per bootstrap.php
require_once 'src/models/PathologyDAOPsql.php';

class PathologyDAOPsqlTest extends AbstractDBTestCase {

    public function testPathologyDAOPsqlGet() {
        $p = PathologyDAOPsql::get(1);
        $this->assertEquals($p[0]['name'], 'Cholera');
    }

    public function testPathologyDAOPsqlGetAll() {
        $p = PathologyDAOPsql::getAll();
        $this->assertTrue(is_array($p) && !empty($p));
    }

    public function testPathologyDAOPsqlInsert() {
        $id = PathologyDAOPsql::insert('Pathology PHPUnit');
        $p = PathologyDAOPsql::get($id);
        PathologyDAOPsql::delete($id);
        $this->assertEquals($p[0]['name'], 'Pathology PHPUnit');
    }

    public function testPathologyDAOPsqlEdit() {
        $id = PathologyDAOPsql::insert('Pathology PHPUnit');
        PathologyDAOPsql::edit($id, 'Pathology PHPUnit Modded');
        $p = PathologyDAOPsql::get($id);
        PathologyDAOPsql::delete($id);
        $this->assertNotEquals($p[0]['name'], 'Pathology PHPUnit');
    }

    public function testPathologyDAOPsqlDelete() {
        $id = PathologyDAOPsql::insert('Pathology PHPUnit');
        PathologyDAOPsql::delete($id);
        $p = PathologyDAOPsql::get($id);
        $this->assertFalse(!empty($p));
    }

}
