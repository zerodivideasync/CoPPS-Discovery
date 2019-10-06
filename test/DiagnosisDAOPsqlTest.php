<?php

require_once 'AbstractDBTestCase.php';
$dir = "./"; //per bootstrap.php
require_once 'src/models/DiagnosisDAOPsql.php';
require_once 'src/models/Diagnosis.php';

class DiagnosisDAOPsqlTest extends AbstractDBTestCase {

    public function testDiagnosisDAOPsqlTestGet() {
        // $id $date $idPathology $location
        $diagnosis = new Diagnosis(null, '13/07/2018', 1, array('lat' => 42.864963753648, 'lng' => 13.56432795535));
        $id = DiagnosisDAOPsql::insert($diagnosis, 'marker');
        $d = DiagnosisDAOPsql::get($id);
        DiagnosisDAOPsql::delete($id);
        $this->assertEquals($d[0]['date'], DateTime::createFromFormat('Y/m/d', $diagnosis->getDate())->format('Y-m-d'));
    }

    public function testPathologyDAOPsqlGetAll() {
        $d = DiagnosisDAOPsql::getAll();
        $this->assertTrue(is_array($d) && !empty($d));
    }

    public function testPathologyDAOPsqlInsert() {
        // $id $date $idPathology $location
        $diagnosis = new Diagnosis(null, '13/07/2018', 1, array('lat' => 42.864963753648, 'lng' => 13.56432795535));
        $id = DiagnosisDAOPsql::insert($diagnosis, 'marker');
        $d = DiagnosisDAOPsql::get($id);
        DiagnosisDAOPsql::delete($id);
        $this->assertEquals($d[0]['id_pathology'], $diagnosis->getPathology());
    }

    public function testPathologyDAOPsqlEdit() {
        // $id $date $idPathology $location
        $diagnosis = new Diagnosis(null, '13/07/2018', 1, array('lat' => 42.864963753648, 'lng' => 13.56432795535));
        $id = DiagnosisDAOPsql::insert($diagnosis, 'marker');
        $diagnosis->setId($id);
        $diagnosis->setDate('01/02/2017');
        DiagnosisDAOPsql::edit($diagnosis);
        $d = DiagnosisDAOPsql::get($id);
        DiagnosisDAOPsql::delete($id);
        $this->assertNotEquals($d[0]['date'], '2018-07-13');
    }

    public function testDiagnosisDAOPsqlTestGetByAreaPolygon() {
        $location = array(
            array('lat' => 42.864963753648, 'lng' => 13.56432795535),
            array('lat' => 41.364962753648, 'lng' => 11.56432795535),
            array('lat' => 41.864963753648, 'lng' => 12.56432795535)
        );
        $date_from = '2011-07-01';
        $date_to = null;
        $d = DiagnosisDAOPsql::getByAreaPolygon($location, $except = array(), $date_from, $date_to);
        $this->assertFalse(!is_array($d) || empty($d));
    }

    public function testDiagnosisDAOPsqlTestGetByAreaBuffer() {
        $location = array('lat' => 42.864963753648, 'lng' => 13.56432795535, 'radius' => 100000);
        $date_from = '2011-07-01';
        $date_to = null;
        $d = DiagnosisDAOPsql::getByAreaBuffer($location, $except = array(), $date_from, $date_to);
        $this->assertFalse(!is_array($d) || empty($d));
        unset($d);
        $location = array('lat' => 42.864963753648, 'lng' => 13.56432795535, 'radius' => 10);
        $d = DiagnosisDAOPsql::getByAreaBuffer($location, $except = array(), $date_from, $date_to);
        $this->assertTrue(!is_array($d) || empty($d));
    }

    public function testDiagnosisDAOPsqlTestGetByElementDistance() {
        $geography_as_text = '0101000020E610000004D96894FDDF2C40855DD69EAD6D4440';
        $range = 1000;
        $date_from = '2011-07-01';
        $date_to = null;
        $d = DiagnosisDAOPsql::getByElementDistance($geography_as_text, $range, $date_from, $date_to);
        $this->assertFalse(!is_array($d) || empty($d));
    }

    public function testDiagnosisDAOPsqlTestGetGeographyAsText() {
        // $id $date $idPathology $location
        $diagnosis = new Diagnosis(null, '13/07/2018', 1, array('lat' => 42.864963753648, 'lng' => 13.56432795535));
        $id = DiagnosisDAOPsql::insert($diagnosis, 'marker');
        $d = DiagnosisDAOPsql::getGeographyAsText($id, 10);
        DiagnosisDAOPsql::delete($id);
        $this->assertTrue(is_string($d['shape']));
    }

    public function testPathologyDAOPsqlCountByPathology() {
        $c = DiagnosisDAOPsql::countByPathology(1);
        $this->assertTrue(is_numeric($c));
    }

}
