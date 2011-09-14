<?php
class Vpc_Root_Category_GeneratorEvents extends Vps_Component_Generator_Page_Events_Table
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Vps_Component_Event_Row_Updated',
            'callback' => 'onPageRowUpdate'
        );
        return $ret;
    }

    public function onPageRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        if (in_array('parent_id', $event->row->getDirtyColumns())) {
            $this->fireEvent(
                new Vps_Component_Event_Page_ParentChanged($this->_class, $event->row)
            );
        }
    }
}
