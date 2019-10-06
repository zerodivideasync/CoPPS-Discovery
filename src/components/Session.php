<?php

require_once 'propertiesConfig.php';
require_once 'FlashMessageProvider.php';
require_once DATABASE;

/**
 * This class is a session wrapper.
 *
 */
class Session {

    /**
     * Starts a secure session.
     */
    public static function sec_session_start() {
        $session_name = 'BD2'; //generatePassword_base64(32); // Imposta un nome di sessione
        //$secure = true; // Imposta il parametro a true se vuoi usare il protocollo 'https'.
        $secure = false;  // if working on localhost

        $httponly = true; // Questo impedirà ad un javascript di essere in grado di accedere all'id di sessione.
        ini_set('session.use_only_cookies', 1); // Forza la sessione ad utilizzare solo i cookie.
        $cookieParams = session_get_cookie_params(); // Legge i parametri correnti relativi ai cookie.
        session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httponly);
        session_name($session_name); // Imposta il nome di sessione con quello prescelto all'inizio della funzione.
        session_start(); // Avvia la sessione php.
        session_regenerate_id(); // Rigenera la sessione e cancella quella creata in precedenza.
    }

    /**
     * Closes a secure session.
     */
    public static function sec_closeSession() {
        // Sarebbe session_start();
        self::sec_session_start();
        // Elimina tutti i valori della sessione.
        $_SESSION = array();
        session_unset();
        // Recupera i parametri di sessione.
        $params = session_get_cookie_params();
        // Cancella i cookie attuali.
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
        // Cancella la sessione.
        session_destroy();
    }

    /**
     * Retrieves $key value from session.
     * @param string $key the key to look for
     * @param int $index (optional) if $_SESSION[$key] is an array, $index is the element of the array to retrieve.
     * @throws InvalidArgumentTypeException if invalid parameters are supplied.
     * @return NULL|mixed depending on whether it finds the supplied key in session.
     */
    public static function read($key, $index = false) {
        if (!is_string($key)) {
            throw new InvalidArgumentTypeException('Key must be a string. Invalid value supplied.');
        }
        $value = NULL;
        if (isset($_SESSION[$key])) {
            if (!$index) {
                $value = $_SESSION[$key];
            } else {
                if (!is_int($index) || $index < 0) {
                    throw new InvalidArgumentTypeException('Index must be a non-negative integer. Invalid value supplied.');
                }
                if (isset($_SESSION[$key][$index])) {
                    $value = $_SESSION[$key][$index];
                }
            }
        }
        return $value;
    }

    /**
     * Writes a variable in session.
     * @param string $key the key of the variable to write.
     * @param mixed $value object/variable to write in session.
     * @throws InvalidArgumentTypeException if the $key is not a string.
     * @return mixed returns the value it wrote in session ($value).
     */
    public static function write($key, $value) {
        if (!is_string($key)) {
            throw new InvalidArgumentTypeException('Session key must be string value');
        }
        $_SESSION[$key] = $value;
        return $value;
    }

    /**
     * Deletes a key from session.
     * @param string $key the key to be deleted from session
     * @throws InvalidArgumentTypeException if the supplied key is not a string.
     */
    public static function delete($key) {
        if (!is_string($key)) {
            throw new InvalidArgumentTypeException('Key must be a string. Invalid value supplied.');
        }
        unset($_SESSION[$key]);
    }

    /**
     * Checks whether the currently logged-in user has privileges to access a given functionality.
     * If the access is denied, it redirects the user to the homepage.
     * In case of success, this function also defines two constants USERNAME and POWER accessible for the rest of the computation.
     * @param int $powerLevel the power lever to check against. If current power level is less than this parameters, access will be denied. Defaults to 1.
     * @throws Exception if the $powerLevel parameters is not a non-negative integer.
     */
    public static function check($powerLevel = 1) {
        if ((!is_int($powerLevel)) || $powerLevel < 0) {
            throw new InvalidArgumentException("sessionCheck: Invalid parameter powerLevel. Expecting non-negative integer, received: $powerLevel");
        }
        if (!isset($_SESSION) || !isset($_SESSION['power']) || $_SESSION['power'] < $powerLevel) {
            FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Permission denied', 'icon' => 'exclamation-triangle']);
            header('Location: ' . APP_ROOT . 'Users/login.php');
            exit;
        }
        if (!defined('USERNAME')) {
            $username = filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
            define('USERNAME', $username);
        }

        if (!defined('POWER')) {
            $power = filter_var($_SESSION['power'], FILTER_SANITIZE_NUMBER_INT);
            define('POWER', $power);
        }
    }

    /**
     * Checks whether the currently logged-in user has privileges to access a given functionality.
     * In case of success, this function also defines two constants USERNAME and POWER accessible for the rest of the computation.
     * @param int $powerLevel the power lever to check against. If current power level is less than this parameters, access will be denied. Defaults to 1.
     * @throws Exception if the $powerLevel parameters is not a non-negative integer.
     */
    public static function checkNoRedirect($powerLevel = 1, $writeSession = true) {
        if ((!is_int($powerLevel)) || $powerLevel < 0) {
            throw new InvalidArgumentException("sessionCheck: Invalid parameter powerLevel. Expecting non-negative integer, received: $powerLevel");
        }
        if (!isset($_SESSION) || !isset($_SESSION['power']) || $_SESSION['power'] < $powerLevel) {
            if ($writeSession) {
                FlashMessageProvider::error(['title' => 'Error!', 'message' => 'Permission denied. To prevent system abuse, only searches can be made; full CRUD operations are only available for administrator accounts.', 'icon' => 'exclamation-triangle']);
            }
            return false;
        }
        if (!defined('USERNAME')) {
            $username = filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
            define('USERNAME', $username);
        }

        if (!defined('POWER')) {
            $power = filter_var($_SESSION['power'], FILTER_SANITIZE_NUMBER_INT);
            define('POWER', $power);
        }
        return true;
    }

    public static function isLogged() {
        if (!isset($_SESSION['username']) || !isset($_SESSION['power'])) {
            return false;
        }
        $username = filter_var($_SESSION['username'], FILTER_SANITIZE_STRING);
        $power = filter_var($_SESSION['power'], FILTER_SANITIZE_NUMBER_INT);
        return ($username && $power > 0);
    }

}
