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
        return $ret;
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
