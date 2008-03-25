<?php
abstract class Vpc_Abstract_TablePageFactory extends Vpc_Abstract_PageFactory
{
    protected $_tableName;
    protected $_componentClass;
    protected $_filenamePattern = '/^(\d+)_(.*)$/';
    protected $_showInMenu = false;
    protected $_additionalPageFactories = array('Vpc_Abstract_PagesFactory');
    protected $_orderBy;

    public function getChildPages()
    {
        return array_merge(parent::getChildPages(), $this->getDynamicChildPages());
    }

    public function getMenuChildPages()
    {
        $ret = parent::getMenuChildPages();
        if ($this->_showInMenu) {
            return array_merge($ret, $this->getDynamicChildPages());
        }
        return $ret;
    }

    protected function _getWhere()
    {
        return array();
    }

    public function getDynamicChildPages()
    {
        $ret = array();

        $rowset = $this->_getTable()->fetchAll($this->_getWhere(), $this->_orderBy);
        foreach ($rowset as $row) {
            $ret[] = $this->getChildPageByRow($row);
        }
        return $ret;
    }

    protected function _getWhereForFilenameMatch($matches)
    {
        $where = $this->_getWhere();
        $where['id = ?'] = $matches[1];
        return $where;
    }

    public function getChildPageByFilename($filename)
    {
        if (preg_match($this->_filenamePattern, $filename, $matches)) {
            $where = $this->_getWhereForFilenameMatch($matches);
            $row = $this->_getTable()->fetchAll($where)->current();
            if ($row) {
                $name = $this->_getFilenameByRow($row);
                if ($name != $filename) {
                    throw new Vpc_UrlNotFoundException($name);
                }
                return $this->getChildPageByRow($row);
            }
        }
        return parent::getChildPageByFilename($filename);
    }

    protected function _getFilenameByRow($row)
    {
        $filter = new Vps_Filter_Url();
        return $row->id . '_' . $filter->filter($this->_getNameByRow($row));
    }

    protected function _getNameByRow($row)
    {
        if (!isset($row->name)) {
            if (!method_exists($row, '__toString')) {
                throw new Vps_Exception(trlVps("Can't generate filename for row.
                    Add a name field, implement __toString for the row or
                    implement _getFilenameByRow for the PageFactory"));
            }
            return $row->__toString();
        }
        return $row->name;
    }

    public function getChildPageById($id)
    {
        $row = $this->_getTable()->find($id)->current();
        if (!$row) return parent::getChildPageById($id);
        return $this->getChildPageByRow($row);
    }

    public function getChildPageByRow($row)
    {
        $pc = $this->getPageCollection();

        // Gibt's die Page schon?
        $id = $this->_component->getId() . '_' . $row->id;
        if ($p = $pc->getExistingPageById($id)) {
            return $p;
        }

        // Page erstellen
        $filename = $this->_getFilenameByRow($row);
        if (!isset($this->_componentClass)) {
            throw new Vps_Exception(trlVps("No _componentClass specified for {0}", get_class($this)));
        }
        $page = $this->_createPage($this->_componentClass, $row->id);

        // Page hinzufügen
        $pc->addTreePage($page, $filename, $this->_getNameByRow($row), $this->_component);

        // Page zurückgeben
        return $page;
    }

    protected function _getTable()
    {
        return $this->_component->getTable($this->_tableName);
    }
}
