<?php
class Kwc_Root_Category_GeneratorEvents extends Kwf_Component_Generator_Page_Events_Table
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onPageRowUpdate'
        );
        array_unshift($ret, array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Row_Inserted',
            'callback' => 'onPageDataChanged'
        ));
        array_unshift($ret, array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Row_Deleted',
            'callback' => 'onPageDataChanged'
        ));
        array_unshift($ret, array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onPageDataChanged'
        ));
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveContentChanged',
            'callback' => 'onRecursiveEvent'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveHasContentChanged',
            'callback' => 'onRecursiveEvent'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Component_RecursiveMasterContentChanged',
            'callback' => 'onRecursiveEvent'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_RecursiveUrlChanged',
            'callback' => 'onRecursiveEvent'
        );
        $ret[] = array(
            'event' => 'Kwf_Component_Event_Page_ParentChanged',
            'callback' => 'onRecursiveEvent'
        );
        return $ret;
    }

    public function onRecursiveEvent(Kwf_Component_Event_Component_RecursiveAbstract $event)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($event->componentId, array('ignoreVisible'=>true));
        if (!$c->isPage && $c->componentId != 'root') {
            throw new Kwf_Exception("Recursive Events must be fired with a page componentId");
        }
        $childIds = $this->_getGenerator()->getVisiblePageChildIds($c->dbId);
        foreach($childIds as $childId) {
            $eventsClass = get_class($event);
            $this->fireEvent(new $eventsClass($event->class, $childId));
        }
    }

    public function onPageFilenameChanged(Kwf_Component_Event_Page_FilenameChanged $event)
    {
        $this->fireEvent(
            new Kwf_Component_Event_Page_RecursiveUrlChanged($this->_class, $event->dbId)
        );
    }

    public function onPageRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        if ($event->isDirty('parent_id')) {
            $this->fireEvent(
                new Kwf_Component_Event_Page_ParentChanged($this->_class, $event->row->id)
            );
            $this->fireEvent(
                new Kwf_Component_Event_Page_RecursiveUrlChanged($this->_class, $event->row->id)
            );
        }
    }

    public function onPageDataChanged(Kwf_Component_Event_Row_Abstract $event)
    {
        $this->_getGenerator()->pageDataChanged();
    }

    protected function _getDbIdsFromRow($row)
    {
        return array($row->id);
    }
}
