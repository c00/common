<?php

namespace test;

use PHPUnit\Framework\TestCase;
use c00\common\Helper;
use c00\sample\User;

class HelperTest extends TestCase
{

    public function testUniqueIds(){
        $iterations = 10000;

        $array = [];

        for ($i = 0; $i < $iterations; $i++){
            $id = Helper::getUniqueId($array);
            $array[$id] = "meh";
        }

        $this->assertEquals($iterations, count($array));

    }

    public function testGroupingArray(){
        $input = [
            ['name' => 'co', 'sex' => 'male'],
            ['name' => 'Bessy', 'sex' => 'female'],
            ['name' => 'Mel', 'sex' => 'female', 'hasPets' => true],
            ['name' => 'Rob', 'sex' => 'male'],
            ['name' => 'Tassa', 'sex' => 'female']
        ];

        $expected = [
            'male' => [
                ['name' => 'co', 'sex' => 'male'],
                ['name' => 'Rob', 'sex' => 'male']
            ],
            'female' => [
                ['name' => 'Bessy', 'sex' => 'female'],
                ['name' => 'Mel', 'sex' => 'female', 'hasPets' => true],
                ['name' => 'Tassa', 'sex' => 'female']
            ]
        ];

        $grouped = Helper::groupArray($input, 'sex');
        $this->assertEquals($expected, $grouped);

        //Sorting on something some don't have will go wrong:
        $this->expectException(\Exception::class);
        Helper::groupArray($input, 'hasPets');
    }

    public function testGroupingObjects(){

        $input = [
            User::newInstance('John', 'john1@example.com'),
            User::newInstance('Jane', 'jane1@example.com'),
            User::newInstance('John', 'john2@example.com'),
            User::newInstance('Jane', 'jane2@example.com'),
            User::newInstance('Mitchel', 'mitchel@example.com')
        ];

        $expected = [
            'John' => [
                $input[0],
                $input[2]
            ],
            'Jane' => [
                $input[1],
                $input[3]
            ],
            'Mitchel' => [
                $input[4]
            ]
        ];

        $grouped = Helper::groupArray($input, 'name');
        $this->assertEquals($expected, $grouped);

        //Sorting on something some don't have will go wrong:
        $this->expectException(\Exception::class);
        Helper::groupArray($input, 'hasPets');
    }

}