<?php

namespace test;

use c00\QueryBuilder\components\OrderByClause;
use PHPUnit\Framework\TestCase;

class OrderByClauseTest extends TestCase
{
    public function testOneColumn() {
        $clause = new OrderByClause();
        $clause->addColumn('name');
        $this->assertEquals(' ORDER BY `name` ASC', $clause->toString());

        $clause = new OrderByClause();
        $clause->addColumn('db.name');
        $this->assertEquals(' ORDER BY `db`.`name` ASC', $clause->toString());

        $clause = new OrderByClause();
        $clause->addColumn('name', false);
        $this->assertEquals(' ORDER BY `name` DESC', $clause->toString());

        $clause = new OrderByClause();
        $clause->addColumn('db.name', false);
        $this->assertEquals(' ORDER BY `db`.`name` DESC', $clause->toString());

        $clause = new OrderByClause();
        $clause->addColumn('name', true, 'IS NULL');
        $this->assertEquals(' ORDER BY `name` IS NULL ASC', $clause->toString());

        $clause = new OrderByClause();
        $clause->addColumn('name', false, 'IS NULL');
        $this->assertEquals(' ORDER BY `name` IS NULL DESC', $clause->toString());

        $clause = new OrderByClause();
        $clause->addColumn('db.name', false, 'IS NULL');
        $this->assertEquals(' ORDER BY `db`.`name` IS NULL DESC', $clause->toString());

        //Empty column will always result in nothing.
        $clause = new OrderByClause();
        $clause->addColumn('', false, 'IS NULL');
        $this->assertEquals('', $clause->toString());
    }

    public function testColumns() {
        $clause = new OrderByClause();
        $clause->addColumn('name')
            ->addColumn('lastName', false)
            ->addColumn('db.email')
            ->addColumn('db.dob', true, 'IS NULL')
            ->addColumn('favColor');

        $this->assertEquals(' ORDER BY `name` ASC, `lastName` DESC, `db`.`email` ASC, `db`.`dob` IS NULL ASC, `favColor` ASC', $clause->toString());

    }

}