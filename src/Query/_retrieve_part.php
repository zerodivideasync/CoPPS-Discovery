<?php

$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
$range_element = filter_input(INPUT_POST, 'range_element', FILTER_SANITIZE_NUMBER_FLOAT); //Range from selected diagnosis or pollution (NOT the area range for circle/polygon)
$date_from = filter_input(INPUT_POST, 'date_from', FILTER_SANITIZE_STRING);
$date_to = filter_input(INPUT_POST, 'date_to', FILTER_SANITIZE_STRING);
$id_element = filter_input(INPUT_POST, 'id_element', FILTER_SANITIZE_NUMBER_INT); //Diagnosis or Pullution_src id

$error = false;
if ($type !== false && $type !== NULL) {
    if ($date_from) {
        $date_from = DateTime::createFromFormat(DATETIMEDMY, $date_from)->format(PollutionSrc::$dateFormat);
    }
    if ($date_to) {
        $date_to = DateTime::createFromFormat(DATETIMEDMY, $date_to)->format(PollutionSrc::$dateFormat);
    }
    if ($date_from && $date_to && $date_from > $date_to) { //Switch dates without warning the user
        $tmp = $date_from;
        $date_from = $date_to;
        $date_to = $tmp;
    }
    try {
        switch ($type) {
            case 'circle':
                $shapes = filter_input(INPUT_POST, 'shape_area');
                if ($shapes !== false && $shapes !== NULL) { //Array not empty
                    $json = false;
                    $json = JsonHelper::decode($shapes, $filters); // JSON -> Array
                    if ($json) {
                        $location = array_pop($json);
                        $pollutions_retrieved = PollutionDAOPsql::getByAreaBuffer($location['values'], null, $date_from, $date_to);
                        $diagnoses_retrieved = DiagnosisDAOPsql::getByAreaBuffer($location['values'], null, $date_from, $date_to);
                        $global_shapes['circle'] = $location['values'];
                    }
                }
                break;
            case 'polygon':
                $shapes = filter_input(INPUT_POST, 'shape_area');
                if ($shapes !== false && $shapes !== NULL) { //Array not empty
                    $json = false;
                    $json = JsonHelper::decode($shapes, $filters); // JSON -> Array
                    if ($json) {
                        $location = array_pop($json);
                        $pollutions_retrieved = PollutionDAOPsql::getByAreaPolygon($location['values']['points'], null, $date_from, $date_to);
                        $diagnoses_retrieved = DiagnosisDAOPsql::getByAreaPolygon($location['values']['points'], null, $date_from, $date_to);
                        $global_shapes['polygon'] = $location['values']['points'];
                    }
                }
                break;
            case 'diagnosis':
                if ($id_element === false || $id_element === NULL || $id_element <= 0) {
                    $error = "Retrieve failed: pollution's id is empty";
                }
                if ($range_element === false || $range_element === NULL) {
                    $error = "Retrieve failed: range is empty";
                }
                if (!$error) {
                    $range_element = abs($range_element) * 1000; /* Cast from km to m. */
                    $diagnosis_geography = DiagnosisDAOPsql::getGeographyAsText($id_element, $range_element);
                    if (!$diagnosis_geography) {
                        FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Diagnosis not found', 'icon' => 'exclamation-triangle']);
                    } else {
                        if ($diagnosis_geography['shape_expanded']) {
                            $diagnosis_geography['shape_expanded'] = PostGisHelper::extractData($diagnosis_geography['shape_expanded']);
                        }
                        $diagnoses_retrieved[] = $diagnosis_geography;
                        $pollutions_retrieved = PollutionDAOPsql::getByElementDistance($diagnosis_geography['shape'], $range_element, $date_from, $date_to);
                        $num_retrieved = count($pollutions_retrieved);
                        if ($num_retrieved > 0) {
                            FlashMessageProvider::success(['message' => 'Pollution sources found: ' . $num_retrieved, 'icon' => 'check']);
                        } else {
                            FlashMessageProvider::warning(['title' => 'Warning!', 'message' => 'No pollution source found', 'icon' => 'exclamation-triangle']);
                        }
                    }
                } else {
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => $error, 'icon' => 'exclamation-triangle']);
                }
                break;
            case 'pollution':
                if ($id_element === false || $id_element === NULL || $id_element <= 0) {
                    $error = "Retrieve failed: pollution's id is empty";
                }
                if ($range_element === false || $range_element === NULL) {
                    $error = "Retrieve failed: range is empty";
                }
                if (!$error) {
                    $range_element = abs($range_element) * 1000; /* Cast from km to m. */
                    $pollution_geography = PollutionDAOPsql::getGeographyAsText($id_element, $range_element);
                    if (!$pollution_geography) {
                        FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Pollution Source not found', 'icon' => 'exclamation-triangle']);
                    } else {
                        if ($pollution_geography['shape_expanded']) {
                            $pollution_geography['shape_expanded'] = PostGisHelper::extractData($pollution_geography['shape_expanded']);
                        }
                        $pollutions_retrieved[] = $pollution_geography;
                        $diagnoses_retrieved = DiagnosisDAOPsql::getByElementDistance($pollution_geography['shape'], $range_element, $date_from, $date_to);
                        $num_retrieved = count($diagnoses_retrieved);
                        if ($num_retrieved > 0) {
                            FlashMessageProvider::success(['message' => 'Diagnoses found: ' . $num_retrieved, 'icon' => 'check']);
                        } else {
                            FlashMessageProvider::warning(['title' => 'Warning!', 'message' => 'No diagnosis found', 'icon' => 'exclamation-triangle']);
                        }
                    }
                } else {
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => $error, 'icon' => 'exclamation-triangle']);
                }
                break;
            default:
                $error = "Retrieve failed: unkown type";
        }
    } catch (Exception $ex) {
        FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Retrieve failed: ' . $ex->getMessage(), 'icon' => 'exclamation-triangle']);
        $json = null; //Wrong Filter structure (or bad data structure)
    }
    if (isset($pollutions_retrieved)) {
        foreach ($pollutions_retrieved as $g) {
            $data = PostGisHelper::extractData($g['shape']);
            $g['shape'] = $data;
            $g['date_from'] = DateHelper::convertData($g['date_from'], DATETIMEYMD, DATETIMEDMY);
            $g['date_to'] = DateHelper::convertData($g['date_to'], DATETIMEYMD, DATETIMEDMY);
            $global_shapes['pollution_srcs'][$g['id']] = $g; //Writing js code for google maps
        }
    }
    if (isset($diagnoses_retrieved)) {
        foreach ($diagnoses_retrieved as $g) {
            $data = PostGisHelper::extractData($g['shape']);
            $g['shape'] = $data;
            $g['date'] = DateHelper::convertData($g['date'], DATETIMEYMD, DATETIMEDMY);
            $global_shapes['diagnoses'][$g['id']] = $g; //Writing js code for google maps
        }
    }
}
unset($shapes);
unset($json);
