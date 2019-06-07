<?php

namespace test;


use c00\common\AbstractDatabase;
use c00\dependencies\IDependency;
use c00\dependencies\TDependency;
use c00\sample\DatabaseWithInterface;
use c00\sample\DatabaseWithTrait;
use c00\sample\DependencyContainer;
use c00\sample\Team;
use PHPUnit\Framework\TestCase;

class DependeciesTest extends TestCase
{

    public function testInterface(){
        $db = new DatabaseWithInterface();

        $this->assertTrue($db instanceof AbstractDatabase);

        //No trait
        $this->assertFalse($this->hasTrait($db, TDependency::class));

        //Has interface
        $this->assertTrue($db instanceof IDependency);
    }

    public function testTrait(){
        $db = new DatabaseWithTrait();

        $this->assertTrue($db instanceof AbstractDatabase);

        //Has trait
        $this->assertTrue($this->hasTrait($db, TDependency::class));

        //Doesn't have interface
        $this->assertFalse($db instanceof IDependency);
    }

    private function hasTrait($dependency, $trait){
        $traits = class_uses($dependency);

        return(in_array($trait, $traits));
    }

    public function testAddToDC(){
        $dc = new DependencyContainer();
        $dc->add(new DatabaseWithTrait());
        $dc->add(new DatabaseWithInterface());

        //Add a non dependency
        $dc->add(new Team());

        //Check they were added
        $this->assertTrue($dc->getDependency(DatabaseWithTrait::class) instanceof DatabaseWithTrait);
        $this->assertTrue($dc->getDependency(DatabaseWithInterface::class) instanceof DatabaseWithInterface);
        $this->assertTrue($dc->getDependency(Team::class) instanceof Team);
        $this->assertNull($dc->getDependency("Not a class name"));

        //Check they have the DC as a member
        $dbWithTrait = $dc->getDependency(DatabaseWithTrait::class);
        $this->assertTrue(method_exists($dbWithTrait, "getDc"));
        $this->assertTrue($dbWithTrait->getDc() instanceof DependencyContainer);

        $dbWithInterface = $dc->getDependency(DatabaseWithInterface::class);
        $this->assertTrue(method_exists($dbWithInterface, "getDc"));
        $this->assertTrue($dbWithInterface->getDc() instanceof DependencyContainer);

        $team = $dc->getDependency(Team::class);
        $this->assertFalse(method_exists($team, "getDc"));
    }

    public function testAddToDCWithName(){
        $depName = "Megan";

        $dc = new DependencyContainer();
        $dc->add(new DatabaseWithTrait(), $depName);

        $this->assertNull($dc->getDependency(DatabaseWithTrait::class));
        $this->assertTrue($dc->getDependency($depName) instanceof DatabaseWithTrait);

    }

}