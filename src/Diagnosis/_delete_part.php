<?php

$shapes = filter_input(INPUT_POST, 'shapes_to_delete');
if ($shapes !== false && $shapes !== NULL) { //Array not empty
    $json = false;
    try {
        $json = JsonHelper::decode($shapes, $filters); // JSON -> Array
    } catch (Exception $ex) {
        FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Delete failed: ' . $ex->getMessage(), 'icon' => 'exclamation-triangle']);
        $json = null; //Wrong Filter structure (or bad data structure)
    }
    if ($json) { //Array decoded without errors
        foreach ($json as $diagnosis) {
            try {
                if (!$diagnosis['id']) { //Empty field
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Error: identifier error', 'icon' => 'exclamation-triangle']);
                    continue;
                }

                $id = DiagnosisDAOPsql::delete($diagnosis['id']);

                if ($id) {
                    FlashMessageProvider::success(['message' => 'Delete successful', 'icon' => 'check']);
                } else {
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Could not delete diagnosis', 'icon' => 'exclamation-triangle']);
                }
            } catch (Exception $ex) {
                FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Could not delete diagnosis: ' . $ex->getMessage(), 'icon' => 'exclamation-triangle']);
            }
        }
    } elseif ($json === false) { //Filter failed
        FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Incorrect data.', 'icon' => 'exclamation-triangle']);
    }
}
unset($shapes);
unset($json);
