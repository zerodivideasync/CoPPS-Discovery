<?php

$shapes = filter_input(INPUT_POST, 'shapes_to_insert');
if ($shapes !== false && $shapes !== NULL) { //Array not empty
    $json = false;
    try {
        $json = JsonHelper::decode($shapes, $filters); // JSON -> Array
    } catch (Exception $ex) {
        FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Insert failed: ' . $ex->getMessage(), 'icon' => 'exclamation-triangle']);
        $json = null; //Wrong Filter structure (or bad data structure)
    } finally {
        
    }
    if ($json) { //Array decoded without errors
        foreach ($json as $diagnosis) {
            try {
                if ($diagnosis['idPathology'] <= 0 || !$diagnosis['date']) { //Empty field
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Error: empty name or start-date', 'icon' => 'exclamation-triangle']);
                    continue;
                }

                $d = new Diagnosis(null, $diagnosis['date'], $diagnosis['idPathology'], $diagnosis['values']);

                if ($d->getDate() > date('Y/m/d')) {
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Error: date must not be greater than today (' . $d->getDate() . ')', 'icon' => 'exclamation-triangle']);
                    continue;
                }

                $id = DiagnosisDAOPsql::insert($d, $diagnosis['type']);

                if ($id) {
                    FlashMessageProvider::success(['message' => 'Insert successful!', 'icon' => 'check']);
                } else {
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Could not insert diagnosis' . $pollution['name'], 'icon' => 'exclamation-triangle']);
                }
            } catch (Exception $ex) {
                FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Could not insert diagnosis.<br>' . $ex->getMessage(), 'icon' => 'exclamation-triangle']);
            }
        }
    } elseif ($json === false) { //Filter failed
        FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Incorrect data.', 'icon' => 'exclamation-triangle']);
    }
}

unset($shapes);
unset($json);
