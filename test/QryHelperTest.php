<?php
/**
 * Created by PhpStorm.
 * User: Co
 * Date: 18/06/2016
 * Time: 01:12
 */

namespace test;


use c00\common\CovleDate;
use c00\common\Helper;
use c00\QueryBuilder\QryHelper;

class QryHelperTest extends \PHPUnit_Framework_TestCase
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