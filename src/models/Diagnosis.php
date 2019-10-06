<?php
/**
 * Class containing attributes and getter/setter methods of the Diagnosis entity
 * 
 * @var integer $id		Diagnosis Id
 * @var date $date		Date when the $pathology was diagnosed
 * @var integer $pathology	Id of the pathology diagnosed
 * @var object $position	Object containing the coordinates of the Diagnosis
 * 
 * @used-by DiagnosisDAO
 */
class Diagnosis {
	static public $dateFormat = 'Y/m/d';
	private $id;
	private $date;
	private $idPathology;
	private $location;
	
	public function __construct($id=null, $date=null, $idPathology=null, $location=null) {
		$this->id = $id;
		self::setDate($date);
		$this->idPathology = $idPathology;
		$this->location = $location;
	}
	
	public function __destruct() {
        
    }
	
	public function getId() {
		return $this->id;
	}

	public function getDate() {
		return $this->date;
	}

	public function getPathology() {
		return $this->idPathology;
	}

	public function getLocation() {
		return $this->location;
	}

	public function setId($id) {
		$this->id = $id;
	}

	public function setDate($date) {
		if(!$date) {
			$this->date = null;
		}
		else {
			$this->date = DateTime::createFromFormat(DATETIMEDMY, $date)->format(self::$dateFormat);
		}
	}

	public function setPathology($idPathology) {
		$this->idPathology = $idPathology;
	}

	public function setLocation($location) {
		$this->location = $location;
	}


}
