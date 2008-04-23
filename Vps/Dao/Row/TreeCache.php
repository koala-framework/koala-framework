<?php
class Vps_Dao_Row_TreeCache extends Vps_Db_Table_Row_Abstract
{
    public function findParentComponent()
    {
        return $this->findParentRow('Vps_Dao_TreeCache', 'Parent');
    }

    protected function _addDecorators(Vpc_Interface $page)
    {
        if (!Zend_Registry::get('config')->vpc->pageDecorators) return $page;
        $classes = Zend_Registry::get('config')->vpc->pageDecorators;
        foreach ($classes as $class) {
            try {
                $page = new $class($page);
            } catch (Zend_Exception $e) {
                throw new Vpc_ComponentNotFoundException("Decorator $class not found");
            }
        }
        return $page;
    }

    public function getComponent()
    {
        $c = new $this->component_class($this);
        if (!is_null($this->url_match)) {
            $c = $this->_addDecorators($c);
        }
        return $c;
    }

    public function getTitle()
    {
        $title = array();
        $row = $this;
        do {
            $title[] = $row->name;
        } while ($row = $row->findParentPage());
        return implode(' - ', $title);
    }

    public function findParentPage()
    {
        if (!$this->parent_url) return null;
        return $this->getTable()->findPageByPath($this->parent_url);
    }

    public function findChildComponents()
    {
        $where = array('parent_component_id = ?' => $this->component_id);
        return $this->getTable()->fetchAll($where, 'pos');
    }

    public function findChildPages()
    {
        $where = array('parent_component_id = ?' => $this->component_id);
        $where[] = 'NOT ISNULL(url)';
        return $this->getTable()->fetchAll($where, 'pos');
    }

    public function findMenuChildPages()
    {
        $where = array('parent_component_id = ?' => $this->component_id);
        $where[] = 'NOT ISNULL(url)';
        $where[] = 'menu = 1';
        return $this->getTable()->fetchAll($where, 'pos');
    }

    protected function _postUpdate()
    {
        Zend_Db_Table_Row_Abstract::_postUpdate();
    }
    protected function _postInsert()
    {
        Zend_Db_Table_Row_Abstract::_postInsert();
    }
    protected function _postDelete()
    {
        Zend_Db_Table_Row_Abstract::_postDelete();
    }
}
