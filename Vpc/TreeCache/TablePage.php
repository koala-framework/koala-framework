<?php
abstract class Vpc_TreeCache_TablePage extends Vpc_TreeCache_Table
{
    protected $_showInMenu = false;
    protected $_nameColumn= 'name';
    protected $_filenameColumn= 'filename';
    protected $_uniqueFilename = false;

    protected function _getSelectFields()
    {
        $fields = parent::_getSelectFields();
        $info = $this->_table->info();

        $sql = "CONCAT(tc.component_id, '_', id)";
        $fields['component_id'] = new Zend_Db_Expr($sql);

        if ($this->_dbIdShortcut) {
            $sc = $this->_cache->getAdapter()->quote($this->_dbIdShortcut);
            $sql = "CONCAT($sc, id)";
        } else {
            $sql = "CONCAT(tc.db_id, '_', id)";
        }
        $fields['db_id'] = new Zend_Db_Expr($sql);

        if (in_array($this->_filenameColumn, $info['cols'])) {
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
        } else {
            $sqlUrl = 'id';
            $sqlPattern = 'id';
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
        $fields['name'] = 't.'.$this->_nameColumn;

        return $fields;
    }
}
