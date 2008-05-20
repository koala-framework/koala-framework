<?php
abstract class Vpc_TreeCache_TablePage extends Vpc_TreeCache_Table
{
    protected $_showInMenu = false;

    protected $_nameColumn= 'name';
    protected $_filenameColumn= 'filename';
    protected $_uniqueFilename = false;

    protected $_idSeparator = '_';

    protected function _getSelectFields()
    {
        $fields = parent::_getSelectFields();
        $info = $this->_table->info();

        if ($this->_uniqueFilename) {
            $sqlUrl = $this->_filenameColumn;
            $sqlPattern = $this->_filenameColumn;
            $sqlPattern = "REPLACE(REPLACE($this->_filenameColumn,
                                        '_', '\\_'),
                                        '%', '\\%')";
        } else {
            $sqlUrl = "id, '_', $this->_filenameColumn";
            $sqlPattern = "id, '\_%'";
        }

        $fields['url'] = new Zend_Db_Expr("CONCAT(tc.tree_url, '/', $sqlUrl)");
        $fields['url_preview'] = $fields['url'];
        $fields['url_match'] = $fields['url'];
        $fields['url_match_preview'] = $fields['url'];
        $fields['url_pattern'] = new Zend_Db_Expr("CONCAT(tc.tree_url_pattern, '/', $sqlPattern)");
        $fields['tree_url'] = $fields['url'];
        $fields['tree_url_pattern'] = $fields['url_pattern'];

        $fields['rel'] = new Zend_Db_Expr("''");
        if (is_bool($this->_showInMenu)) {
            if ($this->_showInMenu) {
                $fields['menu'] = new Zend_Db_Expr("1");
            } else {
                $fields['menu'] = new Zend_Db_Expr("0");
            }
        } else {
            $fields['menu'] = $this->_showInMenu;
        }
        if ($this->_nameColumn instanceof Zend_Db_Expr) {
            $fields['name'] = $this->_nameColumn;
        } else {
            $fields['name'] = 't.'.$this->_nameColumn;
        }

        return $fields;
    }
}
