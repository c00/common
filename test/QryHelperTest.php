<?php

namespace test;


use c00\QueryBuilder\QryHelper;
use PHPUnit\Framework\TestCase;

class QryHelperTest extends TestCase
{

    public function testEncapsulation(){

        $inputs = [
            ['input' => 'someColumn', 'expected' => '`someColumn`'],
            ['input' => 'someTable.someColumn', 'expected' => '`someTable`.`someColumn`'],
            ['input' => 'table.column + column2', 'expected' => '`table`.`column` + `column2`'],
            ['input' => 'table.column + table.column2', 'expected' => '`table`.`column` + `table`.`column2`']
        ];

        foreach ($inputs as $item) {
            $this->assertEquals($item['expected'], QryHelper::encapStringWithOperators($item['input']));
        }
    }

}