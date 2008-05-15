<?php
abstract class Vpc_TreeCache_StaticPage extends Vpc_TreeCache_Static
{
    protected function _getSelectFields($key)
    {
        $fields = parent::_getSelectFields($key);
        $class = $this->_classes[$key];
        if (!isset($class['name'])) {
            throw new Vps_Exception("'name' is required in _classes array");
        }

        $sql = "CONCAT(tc.component_id, '_', ";
        $sql .= $this->_cache->getAdapter()->quote($this->_getChildIdByKey($key));
        $sql .= ")";
        $fields['component_id'] = new Zend_Db_Expr($sql);

        $sql = 'CONCAT(';
        if (isset($class['dbIdShortcut'])) {
            $sql .= $this->_cache->getAdapter()->quote($class['dbIdShortcut']);
        } else {
            $sql .= "tc.db_id, '_'";
        }
        $sql .= ", ";
        $sql .= $this->_cache->getAdapter()->quote($this->_getChildIdByKey($key));
        $sql .= ")";
        $fields['db_id'] = new Zend_Db_Expr($sql);

        $fields['name'] = new Zend_Db_Expr($this->_cache->getAdapter()->quote($class['name']));
        if (isset($class['showInMenu']) && $class['showInMenu']) {
            $fields['menu'] = new Zend_Db_Expr('1');
        } else {
            $fields['menu'] = new Zend_Db_Expr('0');
        }
        $fields['rel'] = new Zend_Db_Expr("''");
        $url = $this->_getFilenameByKey($key);
        $urlPattern = str_replace(array('_', '%'), array('\_', '\%'), $url);
        $urlPattern = $this->_cache->getAdapter()->quote($urlPattern);
        $url = $this->_cache->getAdapter()->quote($url);
        $fields['url'] = new Zend_Db_Expr("CONCAT(tree_url, '/', $url)");
        $fields['url_preview'] = $fields['url'];
        $fields['url_match'] = $fields['url'];
        $fields['url_match_preview'] = $fields['url'];
        $fields['url_pattern'] = new Zend_Db_Expr("CONCAT(tree_url_pattern, '/', $urlPattern)");
        $fields['tree_url'] = $fields['url'];
        $fields['tree_url_pattern'] = $fields['url_pattern'];
        return $fields;
    }

    protected function _getFilenameByKey($key)
    {
        if (is_string($this->_classes[$key])) {
            return $key;
        }
        $class = $this->_classes[$key];
        if (isset($class['filename'])) {
            $filename = $class['filename'];
        } else {
            $filename = Vps_Filter::get($class['name'], 'Ascii');
        }
        return $filename;
    }

    protected function _getChildIdByKey($key)
    {
        $c = $this->_classes[$key];
        if (is_string($c)) return $key;
        if (isset($c['id'])) return $c['id'];
        return $this->_getFilenameByKey($key);
    }

}
