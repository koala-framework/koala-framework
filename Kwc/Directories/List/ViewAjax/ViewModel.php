<?php
class Kwc_Directories_List_ViewAjax_ViewModel extends Kwf_Model_Abstract
{
    protected $_rowClass = 'Kwc_Directories_List_ViewAjax_ViewModelRow';
    protected $_primaryKey = 'componentId';
    private $_itemDirectory;

    public function setItemDirectory($directory)
    {
        $this->_itemDirectory = $directory;
    }

    public function getItemDirectory()
    {
        return $this->_itemDirectory;
    }

    public function createRow(array $data=array()) {
        throw new Kwf_Exception('Not possible');
    }

    public function countRows($select = array())
    {
        $parentData = null;
        $itemDirectory = $this->getItemDirectory();
        if (is_string($itemDirectory)) {
            $itemDirectoryClass = $itemDirectory;
        } else {
            $itemDirectoryClass = $itemDirectory->componentClass;
            $parentData = $itemDirectory;
        }
        $gen = Kwf_Component_Generator_Abstract::getInstance($itemDirectoryClass, 'detail');
        return $gen->countChildData($parentData, $select);
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        if (!is_object($where)) {
            $select = $this->select();
            if ($where) $select->where($where);
            if ($order) $select->order($order);
            if ($limit || $start) $select->limit($limit, $start);
        } else {
            $select = $where;
        }

        $parentData = null;
        $itemDirectory = $this->getItemDirectory();
        if (is_string($itemDirectory)) {
            $itemDirectoryClass = $itemDirectory;
        } else {
            $itemDirectoryClass = $itemDirectory->componentClass;
            $parentData = $itemDirectory;
        }
        $gen = Kwf_Component_Generator_Abstract::getInstance($itemDirectoryClass, 'detail');
        $rowset = $gen->getChildData($parentData, $select);
        $rowset = array_values($rowset);
        return new $this->_rowsetClass(array(
            'dataKeys' => $rowset,
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function getRowByDataKey($component)
    {
        $key = $component->componentId;
        if (!isset($this->_rows[$key])) {
            $this->_rows[$key] = new $this->_rowClass(array(
                'data' => $component,
                'model' => $this
            ));
        }
        return $this->_rows[$key];
    }

    public function fetchCount($where = array())
    {
        throw new Kwf_Exception_NotYetImplemented();
    }
    public function fetchIds($where = array())
    {
        throw new Kwf_Exception_NotYetImplemented();
    }

    public function getPrimaryKey()
    {
        return 'componentId';
    }

    protected function _getOwnColumns()
    {
        return array('id', 'component_id');
    }
}
