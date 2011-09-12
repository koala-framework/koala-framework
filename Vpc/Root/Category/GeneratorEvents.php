<?php
class Vpc_Root_Category_GeneratorEvents extends Vps_Component_Generator_Page_Events_Table
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => Vps_Component_Events::EVENT_ROW_UPDATE,
            'callback' => 'onPageRowUpdate'
        );
        return $ret;
    }

    public function onPageRowUpdate($event, $row)
    {
        if (in_array('parent_id', $row->getDirtyColumns())) {
            $this->fireEvent(
                Vps_Component_Events::EVENT_PAGE_PARENT_CHANGE,
                $this->_config['componentClass'],
                $row
            );
        }
    }
}
