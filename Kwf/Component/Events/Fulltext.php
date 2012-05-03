<?php
class Kwf_Component_Events_Fulltext extends Kwf_Component_Events
{
    public function getListeners()
    {
        $ret = array();
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_ContentChanged',
            'callback' => 'onContentChanged'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_Added',
            'callback' => 'onComponentAddedOrRemoved'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_Removed',
            'callback' => 'onComponentAddedOrRemoved'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveRemoved',
            'callback' => 'onComponentRecursiveAddedOrRemoved'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveAdded',
            'callback' => 'onComponentRecursiveAddedOrRemoved'
        );
        return $ret;
    }

    public function onContentChanged(Kwf_Component_Event_Component_ContentChanged $ev)
    {
        if (!Kwc_Abstract::getFlag($ev->class, 'hasFulltext')) {
            //changed component doesn't have fulltext; ignore
            return;
        }
        $m = Kwc_FulltextSearch_MetaModel::getInstance();
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($ev->dbId, array('ignoreVisible'=>true)) as $data) {
            if ($page = $data->getPage()) {
                $row = $m->getRow(array('equals'=>array('page_id' => $page->componentId)));
                if (!$row) {
                    $row = $m->createRow();
                    $row->page_id = $page->componentId;
                }
                $row->changed_date = date('Y-m-d H:i:s');
                $row->save();
            }
        }
    }

    public function onComponentAddedOrRemoved(Kwf_Component_Event_Component_AbstractFlag $ev)
    {
        if (!Kwc_Abstract::getFlag($ev->class, 'hasFulltext')) {
            //changed component doesn't have fulltext; ignore
            return;
        }
        $m = Kwc_FulltextSearch_MetaModel::getInstance();
        foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($ev->dbId, array('ignoreVisible'=>true)) as $data) {
            if ($page = $data->getPage()) {
                $row = $m->getRow(array('equals'=>array('page_id' => $page->componentId)));
                if (!$row) {
                    $row = $m->createRow();
                    $row->page_id = $page->componentId;
                }
                $row->changed_date = date('Y-m-d H:i:s');
                $row->save();
            }
        }
    }

    public function onComponentRecursiveAddedOrRemoved(Kwf_Component_Event_Component_RecursiveAbstract $ev)
    {
        $m = Kwc_FulltextSearch_MetaModel::getInstance();
        $data = Kwf_Component_Data_Root::getInstance()->getComponentById($ev->componentId, array('ignoreVisible'=>true));
        if ($page = $data->getPage()) {
            $row = $m->getRow($page->componentId);
            if (!$row) {
                $row = $m->createRow();
                $row->page_id = $page->componentId;
            }
            $row->changed_date = date('Y-m-d H:i:s');
            $row->changed_recursive = true;
            $row->save();
        }
    }
}
