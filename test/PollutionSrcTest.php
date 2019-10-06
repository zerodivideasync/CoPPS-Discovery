<?php
require_once 'bootstrap.php';
require_once MODELS . 'PollutionSrc.php';
use PHPUnit\Framework\TestCase;

class PollutionSrcTest extends TestCase
{
    public function testPollutionSrcGetId()
    {
        // id pollution, nome, ubicazione, data da, data a
        $p = new PollutionSrc(383, 'Radioactive Waste', array('lat'=>44.476732613572, 'lng'=>10.428844892518) , '21/01/1997', '13/07/2018');
        $this->assertEquals( 383, $p->getId() );
    }
    
    public function testPollutionSrcGetName()
    {
        // id pollution, nome, ubicazione, data da, data a
        $p = new PollutionSrc(383, 'Radioactive Waste', array('lat'=>44.476732613572, 'lng'=>10.428844892518) , '21/01/1997', '13/07/2018');
        $this->assertEquals( 'Radioactive Waste', $p->getName() );
    }
    
    public function testPollutionSrcGetLocation()
    {
        // id pollution, nome, ubicazione, data da, data a
        $p = new PollutionSrc(383, 'Radioactive Waste', array('lat'=>44.476732613572, 'lng'=>10.428844892518) , '21/01/1997', '13/07/2018');
        $this->assertTrue(is_array($p->getLocation()));
    }
    
    public function testPollutionSrcGetDateFrom()
    {
        // id pollution, nome, ubicazione, data da, data a
        $p = new PollutionSrc(383, 'Radioactive Waste', array('lat'=>44.476732613572, 'lng'=>10.428844892518) , '21/01/1997', '13/07/2018');
        $this->assertEquals( '1997/01/21', $p->getDateFrom() );
    }
    
    public function testPollutionSrcGetDateTo()
    {
        // id pollution, nome, ubicazione, data da, data a
        $p = new PollutionSrc(383, 'Radioactive Waste', array('lat'=>44.476732613572, 'lng'=>10.428844892518) , '21/01/1997', '13/07/2018');
        $this->assertEquals( '2018/07/13', $p->getDateTo() );
    }
    
    public function testPollutionSrcSetId()
    {
        // id pollution, nome, ubicazione, data da, data a
        $p = new PollutionSrc(383, 'Radioactive Waste', array('lat'=>44.476732613572, 'lng'=>10.428844892518) , '21/01/1997', '13/07/2018');
        $p->setId(9283);
        $this->assertNotEquals( 383, $p->getId() );
    }
    
    public function testPollutionSrcSetName()
    {
        // id pollution, nome, ubicazione, data da, data a
        $p = new PollutionSrc(383, 'Radioactive Waste', array('lat'=>44.476732613572, 'lng'=>10.428844892518) , '21/01/1997', '13/07/2018');
        $p->setName('Wastewater and Sewage');
        $this->assertNotEquals( 'Radioactive Waste', $p->getName() );
    }
    
    public function testPollutionSrcSetLocation()
    {
        // id pollution, nome, ubicazione, data da, data a
        $p = new PollutionSrc(383, 'Radioactive Waste', array('lat'=>44.476732613572, 'lng'=>10.428844892518) , '21/01/1997', '13/07/2018');
        $p->setLocation(null);
        $this->assertFalse( is_array($p->getLocation()) );
    }
    
    public function testPollutionSrcSetDateFrom()
    {
        // id pollution, nome, ubicazione, data da, data a
        $p = new PollutionSrc(383, 'Radioactive Waste', array('lat'=>44.476732613572, 'lng'=>10.428844892518) , '21/01/1997', '13/07/2018');
        $p->setDateFrom('09/11/1947');
        $this->assertNotEquals( '21/01/1997', $p->getDateFrom() );
    }
    
    public function testPollutionSrcSetDateTo()
    {
        // id pollution, nome, ubicazione, data da, data a
        $p = new PollutionSrc(383, 'Radioactive Waste', array('lat'=>44.476732613572, 'lng'=>10.428844892518) , '21/01/1997', '13/07/2018');
        $p->setDateTo('11/07/2015');
        $this->assertNotEquals( '13/07/2018', $p->getDateTo() );
    }
}

?>