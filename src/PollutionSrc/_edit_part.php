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
        foreach ($json as $pollution) {
            try {
                if (!$pollution['name'] || !trim($pollution['name']) || !$pollution['dateFrom']) { //Empty field (old value deleted)
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Error: empty name or start-date. <br>Name: ' . $pollution['name'] . " - Start-date: " . $pollution['dateFrom'], 'icon' => 'exclamation-triangle']);
                    continue;
                }

                $p = new PollutionSrc($pollution['id'], $pollution['name'], null, $pollution['dateFrom'], $pollution['dateTo']);

                //exit($p->getDateFrom()->diff($p->getDateTo()));
                if ($p->getDateTo() && $p->getDateFrom() > $p->getDateTo()) {
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Error: wrong date. Start: ' . $pollution['dateFrom'] . ' - End: ' . $pollution['dateTo'], 'icon' => 'exclamation-triangle']);
                    continue;
                }
                $count = PollutionDAOPsql::edit($p);

                if ($count === 1) {
                    FlashMessageProvider::success(['message' => 'Edit successful: ' . $pollution['name'], 'icon' => 'check']);
                } else {
                    FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Could not edit pollution source: ' . $pollution['name'], 'icon' => 'exclamation-triangle']);
                }
            } catch (Exception $ex) {
                FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Could not edit pollution source: ' . $pollution['name'] . '<br>' . $ex->getMessage(), 'icon' => 'exclamation-triangle']);
            }
        }
    } elseif ($json === false) { //Filter failed
        FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Incorrect data.', 'icon' => 'exclamation-triangle']);
    }
}

unset($shapes);
unset($json);
