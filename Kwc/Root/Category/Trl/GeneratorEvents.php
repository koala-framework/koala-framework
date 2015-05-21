<?php
class Kwc_Root_Category_Trl_GeneratorEvents extends Kwc_Chained_Trl_GeneratorEvents_Table_Page
{
    protected $_nameColumn = 'name';
    protected $_filenameColumn = 'filename';

    public function getListeners()
    {
        $ret = parent::getListeners();
        $m = $this->_getGenerator()->getModel();
        $ret[] = array(
            'class' => get_class($m),
            'event' => 'Kwf_Events_Event_Row_Updated',
            'callback' => 'onRowUpdate'
        );
        $ret[] = array(
            'class' => get_class($m),
            'event' => 'Kwf_Events_Event_Row_Inserted',
            'callback' => 'onRowUpdate'
        );
        return $ret;
    }

}
