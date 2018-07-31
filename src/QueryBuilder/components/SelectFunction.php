<?php


namespace c00\QueryBuilder\components;


use c00\QueryBuilder\QryHelper;

class SelectFunction extends Select
{
    public $keyword;
    public $function;
    public $params;

    public function __construct($function, $column, $alias = null, $keyword = null, $params = null)
    {
        $this->function = $function;
        $this->column = $column;
        $this->alias = $alias;
        $this->keyword = $keyword;
        $this->params = $params;

    }

    public function getColumnName($encapped = true)
    {
        $column = "{$this->function}({$this->column})";

        if ($encapped) return ($this->alias)? "`{$this->alias}`" : QryHelper::encap($column);

        return ($this->alias)? $this->alias : $column;
    }

    public function toString($ps = null)
    {
        $distinctKeyword = ($this->keyword) ? "{$this->keyword} " : "";
        $column = QryHelper::encapStringWithOperators($this->column);
        $alias = ($this->alias) ? " AS `{$this->alias}`" : "";
        $paramString = $this->getParamString();

        return "{$this->function}({$distinctKeyword}{$column}{$paramString}){$alias}";
    }

    private function getParamString() {
    	$string = '';

    	if (is_string($this->params)) {
    		$string = " {$this->params}";
	    } else if (is_array($this->params)){
    		$strings = [];
		    foreach ( $this->params as $key => $value ) {
			    $strings[] = "{$key} '{$value}'";
    		}
    		$string = ' '. implode(' ', $strings);
	    }

    	return $string;
    }


}