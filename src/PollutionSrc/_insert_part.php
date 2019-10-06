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
        foreach ($json as $pollution) {
            try {
                if (!$pollution['name'] || !trim($pollution['name']) || !$pollution['dateFrom']) { //Empty field
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Error: empty name or start-date', 'icon' => 'exclamation-triangle']);
                    continue;
                }

                $p = new PollutionSrc(null, $pollution['name'], $pollution['values'], $pollution['dateFrom'], $pollution['dateTo']);

                //exit($p->getDateFrom()->diff($p->getDateTo()));
                if ($p->getDateTo() && $p->getDateFrom() > $p->getDateTo()) {
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Error: wrong date. Start: ' . $pollution['dateFrom'] . ' - End: ' . $pollution['dateTo'], 'icon' => 'exclamation-triangle']);
                    continue;
                }

                $id = PollutionDAOPsql::insert($p, $pollution['type']);

                if ($id) {
                    FlashMessageProvider::success(['message' => 'Insert successful: ' . $pollution['name'], 'icon' => 'check']);
                } else {
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Could not insert pollution source: ' . $pollution['name'], 'icon' => 'exclamation-triangle']);
                }
            } catch (Exception $ex) {
                FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Could not insert pollution source: ' . $pollution['name'] . '<br>' . $ex->getMessage(), 'icon' => 'exclamation-triangle']);
            }
        }
    } elseif ($json === false) { //Filter failed
        FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Incorrect data.', 'icon' => 'exclamation-triangle']);
    }
}

unset($shapes);
unset($json);
