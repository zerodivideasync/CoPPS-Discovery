<?php
require_once 'src/models/Diagnosis.php';
use PHPUnit\Framework\TestCase;

class DiagnosisTest extends TestCase
{
    public function testDiagnosisGetId()
    {
        // id diagnosi, data, id patologia, coordinate
        $diagnosis = new Diagnosis(20, '13/07/2018', 75, array('lat'=>44.476732613572, 'lng'=>10.428844892518) );
        $this->assertEquals( 20, $diagnosis->getId() );
    }
    
    public function testDiagnosisGetDate()
    {
        // id diagnosi, data, id patologia, coordinate
        $diagnosis = new Diagnosis(20, '13/07/2018', 75, array('lat'=>44.476732613572, 'lng'=>10.428844892518) );
        $this->assertEquals( '2018/07/13', $diagnosis->getDate() );
    }
    
    public function testDiagnosisGetPathology()
    {
        // id diagnosi, data, id patologia, coordinate
        $diagnosis = new Diagnosis(20, '13/07/2018', 75, array('lat'=>44.476732613572, 'lng'=>10.428844892518) );
        $this->assertEquals( 75, $diagnosis->getPathology() );
    }
    
    public function testDiagnossisGetLocation()
    {
        // id diagnosi, data, id patologia, coordinate
        $diagnosis = new Diagnosis(20, '13/07/2018', 75, array('lat'=>44.476732613572, 'lng'=>10.428844892518) );
        $this->assertTrue(is_array($diagnosis->getLocation()) );
    }
    
    public function testDiagnosisSetId()
    {
        // id diagnosi, data, id patologia, coordinate
        $diagnosis = new Diagnosis(20, '13/07/2018', 75, array('lat'=>44.476732613572, 'lng'=>10.428844892518) );
        $diagnosis->setId(754);
        $this->assertNotEquals( 20, $diagnosis->getId() );
    }
    
    public function testDiagnosisSetDate()
    {
        // id diagnosi, data, id patologia, coordinate
        $diagnosis = new Diagnosis(20, '13/07/2018', 75, array('lat'=>44.476732613572, 'lng'=>10.428844892518) );
        $diagnosis->setDate('');
        $this->assertNotEquals( '2018/07/13', $diagnosis->getDate() );
    }
    
    public function testDiagnosisSetPathology()
    {
        // id diagnosi, data, id patologia, coordinate
        $diagnosis = new Diagnosis(20, '13/07/2018', 75, array('lat'=>44.476732613572, 'lng'=>10.428844892518) );
        $diagnosis->setPathology(784);
        $this->assertNotEquals( 75, $diagnosis->getPathology() );
    }
    
    public function testDiagnossisSetLocation()
    {
        // id diagnosi, data, id patologia, coordinate
        $diagnosis = new Diagnosis(20, '13/07/2018', 75, array('lat'=>44.476732613572, 'lng'=>10.428844892518) );
        $diagnosis->setLocation('');
        $this->assertFalse(is_array($diagnosis->getLocation()) );
    }
    
}

?>