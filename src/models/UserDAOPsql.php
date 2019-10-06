<?php

if (!isset($dir)) {
    $dir = "../";
}

require_once $dir . 'bootstrap.php';
require_once COMPONENTS . 'Session.php';
require_once MODELS . 'User.php';
require_once MODELS . 'UserDAO.php';
require_once DATABASE;

/**
 * This class contains the User related methods for PostreSQL dbms
 */
class UserDAOPsql implements UserDAO {

    /**
     * Checks if the couple username-password in $u is legit and saves username and power level into the Session
     * 
     * @param User $u
     * @return integer Privilege level of the logged user
     */
    public function checkLogin($u) {
        $check = 0;
        $database = DbPgsql::getConnection();
        try {
            $sql = 'SELECT power FROM users WHERE username = ? AND password = ?';
            $stmt = $database->prepare($sql);
            $stmt->bindValue(1, $u->getUsername(), $database::PARAM_STR);
            $stmt->bindValue(2, $u->getPassword(), $database::PARAM_STR);
            $stmt->execute();
            $result = $stmt->fetchAll();
            foreach ($result as $value) {
                if (!(empty($value['power'])) && ($value['power'] >= 1)) {
                    $_SESSION['username'] = $u->getUsername();
                    $_SESSION['power'] = $value['power'];
                    $check = $value['power'];
                }
            }
        } catch (Exception $exc) {
            echo $exc->getMessage();
        }

        $database = NULL;
        unset($database);
        return $check;
    }

}
