<?php

namespace App\Tests;

use App\Entity\Category;
use PHPUnit\Framework\TestCase;

class CategoryUnitTest extends TestCase
{
    public function testIsTrue()
    {
        $category = new Category();

        $category->setName('name');
        
        $this->assertTrue($category->getName() === 'name');
    }

    public function testIsFalse()
    {
        $category = new Category();

        $category->setName('name');
        
        $this->assertFalse($category->getName() === 'false');
    }

    public function testIsEmpty()
    {
        $category = new Category();
       
        $this->assertEmpty($category->getName());
    }
}
