<?php

class DateHelper {
    
	static function convertData($data,$format_data,$format_output) {
		if(!$data) {
			return false;
		}
		return DateTime::createFromFormat($format_data, $data)->format($format_output);
	}
}
