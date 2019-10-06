<?php
require_once DATABASE;

/**
 * Class containing attributes and getter/setter methods of the Pathology entity
 * 
 * @var integer	$id		Pathology Id
 * @var string $name	Pathology name
 * 
 * @used-by PathologyDAO
 */
class Pathology {
	private $id;
	
	private $name;
	
	public function __construct($id=null, $name=null) {
		$this->id = $id;
		$this->name = $name;
	}
	
	public function __destruct() {
        
    }
		  
	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setName($name) {
		$this->name = $name;
	}


}
