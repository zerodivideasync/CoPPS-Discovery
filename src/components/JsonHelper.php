<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of JsonHelper
 *
 * @author simone
 */
class JsonHelper {
	
	static public function decode($string=false,$filters=false) {
		if(!$string || !is_string($string)) {
			throw new Exception("JsonHelper: No data to decode.");
		}
		$json = json_decode($string,true);
		if(!$filters || !$json) {
			return $json;
		}
		$result = array();
		foreach ($json as $index => $value) {	
			if(is_numeric($index)) {
				$target_filter = $filters;
			} elseif(isset($filters[$index])) {
				$target_filter = $filters[$index];
			} else {
				throw new Exception("JsonHelper: Filter error. Array key: $index");
			}
			if(is_array($value)) {
				$result[$index] = self::subDecodeJson($value,$target_filter);
			} else {
				$result[$index] = filter_var($value,$target_filter);
			}		
			if($result[$index] === false) {
				unset($result);
				return false;
			}
		}
		return $result;
	}
	
	static private function subDecodeJson($array=false,$filters=false) {
		$result = array();
		foreach ($array as $key => $value) {
			if(is_numeric($key)) {
				$target_filter = $filters;
			} elseif(isset($filters[$key])) {
				$target_filter = $filters[$key];
			} else {
				unset($result);
				throw new Exception("JsonHelper: Filter error. Array key: $key");
			}
			if(is_array($value)) {
				$result[$key] = self::subDecodeJson($value,$target_filter);
			} else {
				$result[$key] = filter_var($value,$target_filter);
			}
			if($result[$key] === false) {
				unset($result);
				return false;
			}
		}
		return $result;
	}
}
