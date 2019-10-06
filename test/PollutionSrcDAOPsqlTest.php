<?php

require_once 'AbstractDBTestCase.php';
$dir = "./"; //per bootstrap.php
require_once 'src/models/PollutionSrcDAOPsql.php';
require_once 'src/models/PollutionSrc.php';

class PollutionSrcDAOPsqlTest extends AbstractDBTestCase {

    public function testPollutionSrcDAOPsqlGetAll() {
        $p = PollutionDAOPsql::getAll();
        $this->assertTrue(is_array($p) && !empty($p));
    }

    public function testPollutionSrcDAOPsqlInsert() {
        // $id $name $location $dateFrom $dateTo
        $pollution = new PollutionSrc(null, 'phpunit test', array('lat' => 42.864963753648, 'lng' => 13.56432795535), '13/07/2018', null);
        $id = PollutionDAOPsql::insert($pollution, 'marker');
        $p = PollutionDAOPsql::get($id);
        PollutionDAOPsql::delete($id);
        $this->assertEquals($p[0]['name'], $pollution->getName());
    }

    public function testPollutionSrcDAOPsqlEdit() {
        // $id $name $location $dateFrom $dateTo
        $pollution = new PollutionSrc(null, 'phpunit test', array('lat' => 42.864963753648, 'lng' => 13.56432795535), '13/07/2018', null);
        $id = PollutionDAOPsql::insert($pollution, 'marker');
        $pollution->setId($id);
        $pollution->setDateFrom('01/02/2017');
        PollutionDAOPsql::edit($pollution);
        $d = PollutionDAOPsql::get($id);
        PollutionDAOPsql::delete($id);
        $this->assertNotEquals($d[0]['date_from'], '2018-07-13');
    }

}
