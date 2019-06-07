<?php

namespace test;


use c00\QueryBuilder\components\Comparison;
use c00\QueryBuilder\components\WhereIn;
use c00\QueryBuilder\ParamStore;
use c00\QueryBuilder\QueryBuilderException;
use PHPUnit\Framework\TestCase;

class WhereInTest extends TestCase
{

    public function testEmptyNew(){
        $wi = WhereIn::new('name', []);
        $wi->isFirst = true;

        $expected = "";
        $actual = $wi->toString();
        $this->assertEquals($expected, $actual);

        $wi->isFirst = false;
        $expected = "";
        $actual = $wi->toString();
        $this->assertEquals($expected, $actual);

        $wi->type = Comparison::TYPE_OR;
        $expected = "";
        $actual = $wi->toString();
        $this->assertEquals($expected, $actual);


        $wi->type = Comparison::TYPE_OR;
        $wi->isNotIn = true;
        $expected = "";
        $actual = $wi->toString();
        $this->assertEquals($expected, $actual);
    }

    public function testNew(){
        $ps = new ParamStore();
        $wi = WhereIn::new('name', [1, 2, 3, 4]);
        $wi->isFirst = true;

        $actual = $wi->toString($ps);
        $params = array_keys($ps->getParams());

        $expected = " WHERE `name` IN (:{$params[0]}, :{$params[1]}, :{$params[2]}, :{$params[3]})";

        $this->assertEquals($expected, $actual);

        //Will cause an exception as there's no ParamStore.
        $this->expectException(\Exception::class);
        $wi->toString();
    }

    public function testNotIn(){
        $ps = new ParamStore();
        $wi = WhereIn::new('name', [1, 2, 3, 4]);
        $wi->isNotIn = true;

        $actual = $wi->toString($ps);
        $params = array_keys($ps->getParams());

        $expected = " AND `name` NOT IN (:{$params[0]}, :{$params[1]}, :{$params[2]}, :{$params[3]})";

        $this->assertEquals($expected, $actual);

        //Will cause an exception as there's no ParamStore.
        $this->expectException(QueryBuilderException::class);
        $wi->toString();
    }

}