<?php
/**
 * Interface for the DAO Pattern of the User entity
 * 
 * @used-by UserDAOPsql
 */
interface UserDAO {
    
    public function checkLogin($user);
}
