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

    public function onPageRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        $this->_orExpr[] = new Kwf_Model_Select_Expr_Like('page_id', str_replace('_', '\\_', $event->componentId).'%');
    }

    public function onComponentRecursiveRemoved(Kwf_Component_Event_Component_RecursiveRemoved $event)
    {
        $this->_orExpr[] = new Kwf_Model_Select_Expr_Like('page_id', str_replace('_', '\\_', $event->componentId).'%');
    }
}
