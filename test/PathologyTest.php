<?php
require_once 'bootstrap.php';
require_once MODELS . 'Pathology.php';
use PHPUnit\Framework\TestCase;

class PathologyTest extends TestCase
{
    public function testPathologyGetId()
    {
        // id patologia, nome
        $pathology = new Pathology(2120, 'Epatite');
        $this->assertEquals( 2120, $pathology->getId() );
    }
    
    public function testPathologyGetName()
    {
        // id patologia, nome
        $pathology = new Pathology(2120, 'Rosolia');
        $this->assertEquals( 'Rosolia', $pathology->getName() );
    }
    
    public function testPathologySetId()
    {
        // id patologia, nome
        $pathology = new Pathology(2120, 'Epatite');
        $pathology->setId(9283);
        $this->assertNotEquals( 2120, $pathology->getId() );
    }
    
    public function testPathologySetName()
    {
        // id patologia, nome
        $pathology = new Pathology(2120, 'Epatite');
        $pathology->setName('Cancro ai polmoni');
        $this->assertNotEquals( 'Epatite', $pathology->getName() );
    }
}

?>