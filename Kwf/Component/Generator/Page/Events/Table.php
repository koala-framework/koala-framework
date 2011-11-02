<?php
class Kwf_Component_Generator_Page_Events_Table extends Kwf_Component_Generator_PseudoPage_Events_Table
{
    protected function _fireComponentEvent($event, $row)
    {
        parent::_fireComponentEvent($event, $row);
        $c = 'Kwf_Component_Event_Page_'.$event;
        $this->fireEvent(new $c($this->_getClassFromRow($row), $this->_getDbIdFromRow($row)));
    }
}
