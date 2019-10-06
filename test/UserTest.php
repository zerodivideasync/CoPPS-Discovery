<?php
require_once 'bootstrap.php';
require_once MODELS . 'User.php';
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUserGetId()
    {
        // id, username, password, privilegi
        $p = new User(3245, 'luca', '1234', 1);
        $this->assertEquals( 3245, $p->getId() );
    }
    
    public function testUserGetUsername()
    {
        // id pollution, nome, ubicazione, data da, data a
        $p = new User(3245, 'luca', '1234', 1);
        $this->assertEquals( 'luca', $p->getUsername() );
    }
    
    public function testUserGetPassword()
    {
        // id pollution, nome, ubicazione, data da, data a
        $p = new User(3245, 'luca', '1234', 1);
        $this->assertEquals( hash('sha512', '1234', false), $p->getPassword() );
    }
    
    public function testUserGetPower()
    {
        // id, username, password, privilegi
        $p = new User(3245, 'luca', '1234', 1);
        $this->assertEquals( 1, $p->getPower() );
    }
    
    public function testUserSetId()
    {
        // id, username, password, privilegi
        $p = new User(3245, 'luca', '1234', 1);
        $p->setId(2);
        $this->assertNotEquals( 3245, $p->getPower() );
    }
    
    public function testUserSetUsername()
    {
        // id, username, password, privilegi
        $p = new User(3245, 'luca', '1234', 1);
        $p->setUsername('simone');
        $this->assertNotEquals( 'luca', $p->getUsername() );
    }
    
    public function testUserSetPassword()
    {
        // id, username, password, privilegi
        $p = new User(3245, 'luca', '1234', 1);
        $p->setPassword('dsgsgeswgehsw');
        $this->assertNotEquals( hash('sha512', '1234', false), $p->getPassword() );
    }
    
    public function testUserSetPower()
    {
        // id, username, password, privilegi
        $p = new User(3245, 'luca', '1234', 1);
        $p->setPower(2);
        $this->assertNotEquals( 1, $p->getPower() );
    }
    
    public function testUserSetPasswordHash()
    {
        // id, username, password, privilegi
        $p = new User(3245, 'luca', '1234', 1);
        $p->setPasswordHash('dsgsgeswgehsw');
        $this->assertNotEquals( hash('sha512', '1234', false), $p->getPassword() );
    }
}
