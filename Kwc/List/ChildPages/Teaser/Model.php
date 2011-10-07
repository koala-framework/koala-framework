<?php
class Vpc_List_ChildPages_Teaser_Model extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_Row_Data_Abstract';
    private $_data = array();

    public function getPrimaryKey()
    {
        return 'id';
    }

    protected function _getOwnColumns()
    {
        return array('id', 'pos', 'target_page_id', 'name');
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        if (!is_object($where) || $where instanceof Vps_Model_Select_Expr_Interface) {
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        $dataKeys = array();
        $whereEquals = $select->getPart(Vps_Model_Select::WHERE_EQUALS);
        if (isset($whereEquals['parent_component_id'])) {
            $whereId = null;
            if (isset($whereEquals['id'])) {
                $whereId = $whereEquals['id'];
            }

            $childPagesComponentSelect = array();

            if ($whereId ||
                (isset($whereEquals['ignore_visible']) && $whereEquals['ignore_visible'])
            ) {
                $childPagesComponentSelect[Vps_Component_Select::IGNORE_VISIBLE] = true;
            }
            $childPages = Vps_Component_Data_Root::getInstance()
                ->getComponentById($whereEquals['parent_component_id'], array(
                    Vps_Component_Select::IGNORE_VISIBLE => true
                ))
                ->getPage()
                ->getChildPages($childPagesComponentSelect);
            foreach ($childPages as $childPage) {
                if (is_numeric($childPage->dbId)) {
                    $id = $childPage->dbId;
                } else {
                    $id = substr(md5($childPage->dbId), 0, 5);
                }
                if (!$whereId || $id == $whereId) {
                    $i = 0;
                    $this->_data[$id] = array(
                        'id' => $id,
                        'pos' => $i++,
                        'target_page_id' => $childPage->componentId,
                        'name' => $childPage->name
                    );
                    $dataKeys[] = $id;
                }
            }
        } else {
            throw new Vps_Exception_NotYetImplemented();
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
}
