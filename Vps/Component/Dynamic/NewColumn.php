<?php
/**
 * macht nach $columns einen neuen $tag auf
 */
class Vps_Component_Dynamic_NewColumn
    extends Vps_Component_Dynamic_Abstract
{
    protected $_columns;
    protected $_tag;
    public function setArguments($columns, $tag = 'ul')
    {
        $this->_columns = $columns;
        $this->_tag = $tag;
    }
    public function getContent()
    {
        $info = $this->_info;
        // bei number == 0 nichts machen - das erste <ul> wird händisch hingeschrieben
        if ($info['number'] == 0) return '';

        $columnLimit = ceil($info['total'] / $this->_columns);
        $currentNumber = $info['number']+1; // info[number] fängt bei 0 zu zählen an, currentNumber nicht

        // wenn sichs genau ausgeht, hat man ohne diese if unten dran eine zusätzliche column
        if ($currentNumber == $info['total']) return '';

        if ($currentNumber % $columnLimit == 0) {
            $column = ($currentNumber / $columnLimit) + 1;
            return "</$this->_tag><$this->_tag class=\"column$column\">";
        }
        return '';
    }
}
