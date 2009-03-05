<?php
/**
 * macht nach $columns einen neuen $tag auf
 */
class Vps_Component_Dynamic_NewColumn
    extends Vps_Component_Dynamic_Abstract
{
    protected $_columns;
    protected $_tag;
    public function __construct($columns, $tag = 'ul')
    {
        $this->_content = $content;
        $this->_tag = $tag;
    }
    public function getContent()
    {
        if ($this->_partialInfo['number'] % ceil($this->_partialInfo['total'] / $this->_columns) == 0) {
            return "</$this->_tag><$this->_tag class=\"column"
                    .($this->_partialInfo['number'] / ceil($this->_partialInfo['total'] / $this->_columns) + 1)
                    .'">';
        }
        return '';
    }
}
