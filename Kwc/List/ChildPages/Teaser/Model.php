<?php
class Kwc_List_ChildPages_Teaser_Model extends Kwf_Model_Abstract
{
    protected $_rowClass = 'Kwf_Model_Row_Data_Abstract';
    private $_data = array();

    public function getPrimaryKey()
    {
        return 'id';
    }

    protected function _getOwnColumns()
    {
        return array('id', 'pos', 'target_page_id', 'target_page_db_id', 'name');
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

            $childPages = array();

            $startPage = Kwf_Component_Data_Root::getInstance()
                ->getComponentById($whereEquals['parent_component_id'], array(
                    Kwf_Component_Select::IGNORE_VISIBLE => true
                ))
                ->getPage();

            //first access kwf_pages table directly to avoid creating data objects
            $generators = Kwf_Component_Data_Root::getInstance()->getPageGenerators();
            if ($generators) {
                $s = new Kwf_Model_Select();

                $s->whereEquals('parent_id', $startPage->dbId);
                if (!isset($whereEquals['ignore_visible']) || !$whereEquals['ignore_visible']) {
                    $s->whereEquals('visible', true);
                }
                $o = array(
                    'columns' => array('id', 'name')
                );
                                  //only the first
                $rows = $generators[0]->getModel()->export(Kwf_Model_Abstract::FORMAT_ARRAY, $s, $o);
                foreach ($rows as $row) {
                    $id = $row['id'];
                    $childPages[] = array(
                        'id' => $id,
                        'target_page_id' => $id,
                        'target_page_db_id' => $id,
                        'name' => $row['name']
                    );

                }
            }

            //then ask all other generators for additional pages
            //(you can not change the order currently)
            $childPagesComponentSelect = array();
            if ($whereId ||
                (isset($whereEquals['ignore_visible']) && $whereEquals['ignore_visible'])
            ) {
                $childPagesComponentSelect[Kwf_Component_Select::IGNORE_VISIBLE] = true;
            }
            $childPagesComponentSelect['pageGenerator'] = false; //already selected by accessing model direclty
            foreach ($startPage->getChildPages($childPagesComponentSelect) as $childPage) {
                if (is_numeric($childPage->dbId)) {
                    throw new Kwf_Exception("this must not happen, pages must be queried by accessing model directly");
                }
                $childPages[] = array(
                    'id' => substr(md5($childPage->dbId), 0, 5),
                    'target_page_id' => $childPage->componentId,
                    'target_page_db_id' => $childPage->dbId,
                    'name' => $childPage->name
                );
            }



            foreach ($childPages as $childPage) {
                $id = $childPage['id'];
                if (!$whereId || $id == $whereId) {
                    $i = 0;
                    $this->_data[$id] = array(
                        'id' => $id,
                        'pos' => $i++,
                        'target_page_id' => $childPage['target_page_id'],
                        'target_page_db_id' => $childPage['target_page_db_id'],
                        'name' => $childPage['name'],
                    );
                    $dataKeys[] = $id;
                }
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
}
