<?php

$shapes = filter_input(INPUT_POST, 'shapes_to_edit');
if ($shapes !== false && $shapes !== NULL) { //Array not empty
    $json = false;
    try {
        $json = JsonHelper::decode($shapes, $filters); // JSON -> Array
    } catch (Exception $ex) {
        FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Edit failed: ' . $ex->getMessage(), 'icon' => 'exclamation-triangle']);
        $json = null; //Wrong Filter structure (or bad data structure)
    }
    if ($json) { //Array decoded without errors
        foreach ($json as $diagnosis) {
            try {
                if (!$diagnosis['id'] || !$diagnosis['idPathology'] || !$diagnosis['date']) { //Empty field (old value deleted)
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Error: wrong data', 'icon' => 'exclamation-triangle']);
                    continue;
                }

                $d = new Diagnosis($diagnosis['id'], $diagnosis['date'], $diagnosis['idPathology'], null);

                if ($d->getDate() > date('Y/m/d')) {
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Error: date must not be greater than today (' . $d->getDate() . ')', 'icon' => 'exclamation-triangle']);
                    continue;
                }
                $count = DiagnosisDAOPsql::edit($d);

                if ($count === 1) {
                    FlashMessageProvider::success(['message' => 'Edit successful', 'icon' => 'check']);
                } else {
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Could not edit diagnosis', 'icon' => 'exclamation-triangle']);
                }
            } catch (Exception $ex) {
                FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Could not edit diagnosis. ' . $ex->getMessage(), 'icon' => 'exclamation-triangle']);
            }
        }
    } elseif ($json === false) { //Filter failed
        FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Incorrect data.', 'icon' => 'exclamation-triangle']);
    }
}

unset($shapes);
unset($json);
