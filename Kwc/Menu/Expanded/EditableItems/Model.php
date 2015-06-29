<?php
class Kwc_Menu_Expanded_EditableItems_Model extends Kwf_Model_Abstract
{
    protected $_rowClass = 'Kwf_Model_Row_Data_Abstract';
    private $_data = array();

    public function getPrimaryKey()
    {
        return 'id';
    }

    protected function _getOwnColumns()
    {
        return array('id', 'pos', 'target_page_id', 'name', 'filename', 'parent_pos', 'parent_name');
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        if (!is_object($where) || $where instanceof Kwf_Model_Select_Expr_Interface) {
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        $dataKeys = array();
        $whereEquals = $select->getPart(Kwf_Model_Select::WHERE_EQUALS);
        if (isset($whereEquals['parent_component_id'])) {
            $whereId = null;
            if (isset($whereEquals['id'])) {
                $whereId = $whereEquals['id'];
            }

            $childPagesComponentSelect = array();
            $childPagesComponentSelect['showInMenu'] = true;

            if ($whereId ||
                (isset($whereEquals['ignore_visible']) && $whereEquals['ignore_visible']) ||
                $select->getPart(Kwf_Component_Select::IGNORE_VISIBLE)
            ) {
                $childPagesComponentSelect[Kwf_Component_Select::IGNORE_VISIBLE] = true;
            }
            $component = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($whereEquals['parent_component_id'], array(
                    Kwf_Component_Select::IGNORE_VISIBLE => true
                ));
            while ($component) {
                $component = $component->parent;
                if ($component->isPage) break;
                if (!$component->parent) break;
                if (Kwc_Abstract::getFlag($component->componentClass, 'menuCategory')) break;
            }
            $pages = $component->getChildPages($childPagesComponentSelect);
            $i = 0;
            foreach ($pages as $page) {
                $j = 0;
                foreach ($page->getChildPages($childPagesComponentSelect) as $childPage) {
                    if (isset($whereEquals['filename']) && $childPage->filename != $whereEquals['filename']) {
                        continue;
                    }
                    $id = $this->_getIdForPage($childPage);
                    if (!$whereId || $id == $whereId) {
                        $this->_data[$id] = array(
                            'id' => $id,
                            'pos' => $j++,
                            'target_page_id' => $childPage->componentId,
                            'name' => $childPage->name,
                            'filename' => $childPage->filename,
                            'parent_pos' => $i,
                            'parent_name' => $page->name,
                        );
                        $dataKeys[] = $id;
                    }
                }
                $i++;
            }
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
        return new $this->_rowsetClass(array(
            'dataKeys' => $dataKeys,
            'model' => $this
        ));
    }

    public function getRowByDataKey($key)
    {
        if (!isset($this->_rows[$key])) {
            $this->_rows[$key] = new $this->_rowClass(array(
                'data' => $this->_data[$key],
                'model' => $this
            ));
        }
        return $this->_rows[$key];
    }

    protected function _getIdForPage($page)
    {
        if (is_numeric($page->dbId)) {
            $id = $page->dbId;
        } else {
            $id = substr(md5($page->dbId), 0, 5);
        }
        return $id;
    }
}
