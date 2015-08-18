<?php
class Kwf_Component_Events_PagesMeta extends Kwf_Events_Subscriber
{
    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_ContentChanged',
            'callback' => 'onContentChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_UrlChanged',
            'callback' => 'onContentChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_Added',
            'callback' => 'onComponentAdded'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_Removed',
            'callback' => 'onComponentRemoved'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveRemoved',
            'callback' => 'onComponentRecursiveRemoved'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveAdded',
            'callback' => 'onComponentRecursiveAdded'
        );
        return $ret;
    }

    public function onContentChanged(Kwf_Component_Event_Component_Abstract $ev)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_PagesMetaModel');
        if ($page = $ev->component->getPage()) {
            $row = $m->getRow($page->componentId);
            if (!$row) {
                $row = $m->createRow();
            }
            $row->updateFromPage($page);
            $row->changed_date = date('Y-m-d H:i:s');
            $row->save();
        }
    }

    public function onComponentAdded(Kwf_Component_Event_Component_AbstractFlag $ev)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_PagesMetaModel');
        if ($page = $ev->component->getPage()) {
            $row = $m->getRow(array('equals'=>array('page_id' => $page->componentId)));
            if (!$row) {
                $row = $m->createRow();
            }
            $row->updateFromPage($page);
            $row->changed_date = date('Y-m-d H:i:s');
            $row->save();
        }
    }

    public function onComponentRemoved(Kwf_Component_Event_Component_AbstractFlag $ev)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_PagesMetaModel');
        if ($page = $ev->component->getPage()) {
            $row = $m->getRow(array('equals'=>array('page_id' => $page->componentId)));
            if ($row) {
                $row->deleted = true;
                $row->save();
            }
        }
    }

    public function onComponentRecursiveAdded(Kwf_Component_Event_Component_RecursiveAbstract $ev)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_PagesMetaModel');
        if ($page = $ev->component->getPage()) {
            $row = $m->getRow($page->componentId);
            if (!$row) {
                $row = $m->createRow();
            }
            $row->updateFromPage($page);
            $row->changed_date = date('Y-m-d H:i:s');
            $row->changed_recursive = true;
            $row->save();
        }
    }

    public function onComponentRecursiveRemoved(Kwf_Component_Event_Component_RecursiveAbstract $ev)
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Component_PagesMetaModel');
        if ($page = $ev->component->getPage()) {
            $row = $m->getRow($page->componentId);
            if (!$row) {
                $row->deleteRecursive();
            }
        }
    }
}
