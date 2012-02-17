<?php
class Kwf_Component_Events_UrlCache extends Kwf_Component_Events
{
    private $_orExpr;

    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Row_UpdatesFinished',
            'callback' => 'onRowUpdatesFinished'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onPageRecursiveUrlChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveRemoved',
            'callback' => 'onComponentRecursiveRemoved'
        );
        return $ret;
    }

    protected function _init()
    {
        parent::_init();
        $this->_orExpr = new Kwf_Model_Select_Expr_Or(array());
    }

    public function onRowUpdatesFinished(Kwf_Component_Event_Row_UpdatesFinished $event)
    {
        if (count($this->_orExpr->getExpressions())) {
            $select = new Kwf_Model_Select();
            $select->where($this->_orExpr);
            $rows = Kwf_Component_Cache::getInstance()->getModel('url')->export(Kwf_Model_Abstract::FORMAT_ARRAY, $select);
            foreach ($rows as $row) {
                $cacheId = 'url-'.$row['url'];
                Kwf_Cache_Simple::delete($cacheId);
            }
        }
    }


    //usually child componets can be deleted using %, but not those from pages table as the ids always start with numeric
    //this method returns all child ids needed for deleting recursively
    private function _getIdsFromRecursiveEvent(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($event->componentId, array('ignoreVisible'=>true));
        $c = $c->getPageOrRoot();
        $ids = array($c->componentId);
        foreach (Kwf_Component_Data_Root::getInstance()->getPageGenerators() as $gen) {
            $ids = array_merge($ids, $gen->getVisiblePageChildIds($c->dbId));
        }
        return $ids;
    }

    public function onPageRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        foreach ($this->_getIdsFromRecursiveEvent($event) as $id) {
            $this->_orExpr[] = new Kwf_Model_Select_Expr_Like('page_id', $id.'%');
        }
    }

    public function onComponentRecursiveRemoved(Kwf_Component_Event_Component_RecursiveRemoved $event)
    {
        foreach ($this->_getIdsFromRecursiveEvent($event) as $id) {
            $this->_orExpr[] = new Kwf_Model_Select_Expr_Like('page_id', $id.'%');
        }
    }
}
