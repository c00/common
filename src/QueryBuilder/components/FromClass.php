<?php


namespace c00\QueryBuilder\components;


use c00\common\IDatabaseObject;
use c00\QueryBuilder\QueryBuilderException;

class FromClass extends From
{
    public $class;

    public static function new($table, $alias = null, $class = null) {
        //Class and alias are optional to keep the signature compatible, but really it's not optional.
        if (!$class) throw new QueryBuilderException('I need a class');
        if (!$alias) throw new QueryBuilderException('I need an alias');

        /** @var FromClass $f */
        $f = parent::new($table, $alias);
        $f->class = $class;

        return $f;
    }

    /**
     * @return Select[]
     * @throws QueryBuilderException
     */
    public function getClassColumns() {
        if (!$this->class) throw new QueryBuilderException('Like school in july. No class!');

        $o = new $this->class();
        if (!$o instanceof IDatabaseObject) {
            throw new QueryBuilderException("{$this->class} doesn't implement IDatabaseObject");
        }

        //To array should do the necessary mappings wth $_mappings.
        $keys = array_keys($o->toArray(true));

        $columns = [];


        foreach ($keys as $key) {
            $c = "{$this->alias}.{$key}";
            $columns[] = Select::new($c, $c);
        }


        return $columns;
    }


}