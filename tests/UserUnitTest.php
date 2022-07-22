<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserUnitTest extends TestCase
{
    public function testIsTrue()
    {
        $user = new User();

        $user->setEmail('true@test.com')
             ->setPassword('password')
             ->setFirstname('firstname')
             ->setLastname('lastname')
             ->setPicture('picture');
        
        $this->assertTrue($user->getEmail() === 'true@test.com');
        $this->assertTrue($user->getPassword() === 'password');
        $this->assertTrue($user->getFirstname() === 'firstname');
        $this->assertTrue($user->getLastname() === 'lastname');
        $this->assertTrue($user->getPicture() === 'picture');
    }

    public function testIsFalse()
    {
        $user = new User();

        $user->setEmail('true@test.com')
             ->setPassword('password')
             ->setFirstname('firstname')
             ->setLastname('lastname')
             ->setPicture('picture');
        
        $this->assertFalse($user->getEmail() === 'false@test.com');
        $this->assertFalse($user->getPassword() === 'false');
        $this->assertFalse($user->getFirstname() === 'false');
        $this->assertFalse($user->getLastname() === 'false');
        $this->assertFalse($user->getPicture() === 'false');
    }

    public function testIsEmpty()
    {
        $user = new User();
       
        $this->assertEmpty($user->getEmail());
        //$this->assertEmpty($user->getPassword());
        $this->assertEmpty($user->getFirstname());
        $this->assertEmpty($user->getLastname());
        $this->assertEmpty($user->getPicture());
    }
}
