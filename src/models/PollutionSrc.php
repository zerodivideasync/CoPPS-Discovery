<?php

/**
 * Class containing attributes and getter/setter methods of the Pollution Src entity
 * 
 * @var integer	$id		Pollution Source Id
 * @var string $name	Pollution Source name
 * @var string $location	Object containing the geometry and coordinates of the Pollution Source
 * @var date $dateFrom		Date when the Pollution Source starts
 * @var date|null $dateTo		Date when the Pollution Source ends
 * 
 * @used-by PollutionSrcDAO
 */
class PollutionSrc {
	static public $dateFormat = 'Y/m/d';
	private $id;
	private $name;
	private $location;
	private $dateFrom;
	private $dateTo;
	
	public function __construct($id=null, $name=null, $location=null, $dateFrom=null, $dateTo=null) {
		$this->id = $id;
		$this->name = $name;
		$this->location = $location;
		self::setDateFrom($dateFrom);
		self::setDateTo($dateTo);
	}
	
	public function __destruct() {
        
    }
	
	public function getId() {
		return $this->id;
	}

	public function getName() {
		return $this->name;
	}

	public function getLocation() {
		return $this->location;
	}

	public function getDateFrom() {
		return $this->dateFrom;
	}

	public function getDateTo() {
		return $this->dateTo;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function setLocation($location) {
		$this->location = $location;
	}

	/**
	 * YYYY/MM/DD format
	 */
	public function setDateFrom($dateFrom) {
		$this->dateFrom = DateTime::createFromFormat(DATETIMEDMY, $dateFrom)->format(self::$dateFormat);
	}

	/**
	 * YYYY/MM/DD format
	 */
	public function setDateTo($dateTo) {
		if(!$dateTo) {
			$this->dateTo = null;
		}
		else {
			$this->dateTo = DateTime::createFromFormat(DATETIMEDMY, $dateTo)->format(self::$dateFormat);
		}
	}
}
