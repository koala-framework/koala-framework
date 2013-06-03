<?php
class Kwc_Root_Category_GeneratorEvents extends Kwf_Component_Generator_Page_Events_Table
{
    private $_deferredDeleteCacheIds = array();
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onPageRowUpdate'
        );
        $ret[] = array(
            'class' => null,
            'event' => 'Kwf_Component_Event_Row_UpdatesFinished',
            'callback' => 'onRowUpdatesFinished'
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
        return $ret;
    }

    public function onPageRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        //getComponentsByDbId is not required because those are sure to be numeric and thus exist only once
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($event->row->id);
        if ($c) {
            if ($event->isDirty('parent_id')) {
                $oldParentId = $event->row->getCleanValue('parent_id');
                if ($oldParentId) {
                    $oldParent = Kwf_Component_Data_Root::getInstance()->getComponentById($oldParentId, array('ignoreVisible'=>true));
                    $newParent = Kwf_Component_Data_Root::getInstance()->getComponentById($event->row->parent_id, array('ignoreVisible'=>true));
                    $this->fireEvent(
                        new Kwf_Component_Event_Page_ParentChanged(
                            $this->_class, $c, $newParent, $oldParent
                        )
                    );
                }
                $this->fireEvent(
                    new Kwf_Component_Event_Page_RecursiveUrlChanged($this->_class, $c)
                );
            }
            if ($event->isDirty('hide')) {
                $this->fireEvent(
                    new Kwf_Component_Event_Page_ShowInMenuChanged($this->_class, $c)
                );
            }
            if ($event->isDirty('device_visible')) {
                $this->fireEvent(
                    new Kwf_Component_Event_Page_ShowInMenuChanged($this->_class, $c)
                );
            }
            if ($event->isDirty('is_home')) {
                $this->fireEvent(
                    new Kwf_Component_Event_Page_UrlChanged($this->_class, $c)
                );
            }
        }

        if ($event->isDirty(array('parent_id', 'visible'))) {
            $this->_deletePageDataCacheRecursive($event->row->id);
        }
        if ($event->isDirty('parent_id')) {
            $oldParentId = $event->row->getCleanValue('parent_id');
            $newParentId = $event->row->parent_id;
            Kwf_Cache_Simple::delete('pcIds-'.$oldParentId);
            Kwf_Cache_Simple::delete('pcIds-'.$newParentId);
        }
        if ($event->isDirty('pos')) {
            //cache is ordered by pos
            Kwf_Cache_Simple::delete('pcIds-'.$event->row->parent_id);
        }
        if ($event->isDirty(array('parent_id', 'filename'))) {
            Kwf_Cache_Simple::delete('pcFnIds-'.$event->row->getCleanValue('parent_id').'-'.$event->row->getCleanValue('filename'));
        }
    }

    private function _deletePageDataCacheRecursive($id)
    {
        foreach ($this->_getGenerator()->getRecursivePageChildIds($id) as $i) {
            Kwf_Cache_Simple::delete('pd-'.$i);
        }
    }

    public function onPageDataChanged(Kwf_Component_Event_Row_Abstract $event)
    {
        Kwf_Cache_Simple::delete('pd-'.$event->row->id);
        if ($event instanceof Kwf_Component_Event_Row_Deleted) {
            $this->_deletePageDataCacheRecursive($event->row->id);
            $this->_deferredDeleteCacheIds[] = 'pcIds-'.$event->row->parent_id; //deferred delete, see comment in onRowUpdatesFinished
        } else if ($event instanceof Kwf_Component_Event_Row_Inserted) {
            Kwf_Cache_Simple::delete('pcIds-'.$event->row->parent_id);
        }
        $this->_getGenerator()->pageDataChanged();
    }

    protected function _getComponentsFromRow($row, $select)
    {
        $c = Kwf_Component_Data_Root::getInstance()->getComponentById($row->id, $select);
        if (!$c) return array();
        return array($c);
    }

    //requred to defer cache deletion based on Row_Delete events which is called in beforeDelete (as in afterDelete the row data is gone)
    //else the Generator would cache again with the *old* data as it's called from menu events
    public function onRowUpdatesFinished(Kwf_Component_Event_Row_UpdatesFinished $event)
    {
        Kwf_Cache_Simple::delete($this->_deferredDeleteCacheIds);
        $this->_deferredDeleteCacheIds = array();
    }
}
