<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ErrorsMessages
 *
 * @author root
 */
abstract class ErrorMessages {

    public static function insertUser($value) {
        $result = "";
        switch ($value) {
            case "0":
                $result = "Stai inserendo una username già presente";
                break;
            case "-1":
                $result = "Indirizzo email già presente sul database";
                break;

            default:
                $result = "Errore inserimento utente";
        }
        return $result;
    }

    public static function updateUser($value) {
        $result = "";
        switch ($value) {
            case "0":
                $result = "Non puoi aggiornare un profilo che non esiste";
                break;
            case "-1":
                $result = "Indirizzo email già presente sul database";
                break;

            default:
                $result = "Errore aggiornamento utente";
        }
        return $result;
    }

    public static function login($value) {
        $result = "";
        switch ($value) {
            case "0":
                $result = "Username o email non validi.";
                break;
            case "-1":
                $result = "Hai esaurito il numero di tentativi possibili per il login, contatta un amministratore";
                break;
            default:
                $result = "Errore login utente";
        }
        return $result;
    }

    public static function changepw($value) {
        $result = "";
        switch ($value) {
            case "0":
                $result = "La vecchia password non corrisponde, se non la ricordi, contatta un amministratore";
                break;
            default:
                $result = "Impossibile cambiare la password";
        }
        return $result;
    }

}
