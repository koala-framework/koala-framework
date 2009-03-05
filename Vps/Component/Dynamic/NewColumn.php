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
        $info = $this->_info['partial'];
        if ($info['number'] % ceil($info['total'] / $this->_columns) == 0) {
            $column = $info['number'] / ceil($info['total'] / $this->_columns) + 1;
            return "</$this->_tag><$this->_tag class=\"column$column\">";
        }
        return '';
    }
}
