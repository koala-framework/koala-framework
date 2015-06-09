<?php
class Kwf_Component_Events_UrlCache extends Kwf_Events_Subscriber
{
    private $_orExpr;

    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'event' => 'Kwf_Events_Event_Row_UpdatesFinished',
            'callback' => 'onRowUpdatesFinished'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onPageRecursiveUrlChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_UrlChanged',
            'callback' => 'onPageUrlChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_NameChanged',
            'callback' => 'onPageNameChanged'
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

    public function onRowUpdatesFinished(Kwf_Events_Event_Row_UpdatesFinished $event)
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

    public function onPageUrlChanged(Kwf_Component_Event_Page_UrlChanged $event)
    {
        $c = $event->component->getPageOrRoot();
        $this->_orExpr[] = new Kwf_Model_Select_Expr_Equal('expanded_page_id', $c->getExpandedComponentId());
    }

    public function onPageNameChanged(Kwf_Component_Event_Page_NameChanged $event)
    {
        $c = $event->component->getPageOrRoot();
        $this->_orExpr[] = new Kwf_Model_Select_Expr_Equal('page_id', $c->componentId);
    }

    public function onPageRecursiveUrlChanged(Kwf_Component_Event_Page_RecursiveUrlChanged $event)
    {
        $c = $event->component->getPageOrRoot();
        $this->_orExpr[] = new Kwf_Model_Select_Expr_Like('expanded_page_id', $c->getExpandedComponentId().'%');
    }

    public function onComponentRecursiveRemoved(Kwf_Component_Event_Component_RecursiveRemoved $event)
    {
        $c = $event->component->getPageOrRoot();
        $this->_orExpr[] = new Kwf_Model_Select_Expr_Like('expanded_page_id', $c->getExpandedComponentId().'%');
    }
}
