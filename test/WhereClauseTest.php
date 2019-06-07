<?php


namespace test;


use c00\QueryBuilder\components\Where;
use c00\QueryBuilder\components\WhereClause;
use c00\QueryBuilder\components\WhereIn;
use c00\QueryBuilder\ParamStore;
use PHPUnit\Framework\TestCase;

class WhereClauseTest extends TestCase
{

    public function testFirstWhereIn(){
        $wc = new WhereClause();

        $wc->conditions[] = WhereIn::new('name', ['peter', 'william', 'jon']);
        $wc->conditions[] = Where::new('email', '=', 'test@covle.com');

        $ps = new ParamStore();
        $actual = $wc->toString($ps);

        $params = array_flip($ps->getParams());

        $expected = " WHERE `name` IN (:{$params['peter']}, :{$params['william']}, :{$params['jon']}) AND `email` = :{$params['test@covle.com']}";

        $this->assertEquals($expected, $actual);
    }

    public function testFirstWhereInEmpty(){
        $wc = new WhereClause();

        $wc->conditions[] = WhereIn::new('name', []);
        $wc->conditions[] = Where::new('email', '=', 'test@covle.com');

        $ps = new ParamStore();
        $actual = $wc->toString($ps);

        $params = array_flip($ps->getParams());

        $expected = " WHERE `email` = :{$params['test@covle.com']}";

        $this->assertEquals($expected, $actual);
    }

}