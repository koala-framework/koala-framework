<?php
class Kwf_Component_Generator_Page_Events_Table extends Kwf_Component_Generator_PseudoPage_Events_Table
{
    protected function _fireComponentEvent($event, $row, $flag)
    {
        parent::_fireComponentEvent($event, $row, $flag);
        $c = 'Kwf_Component_Event_Page_'.$event;
        foreach ($this->_getDbIdsFromRow($row) as $dbId) {
            $this->fireEvent(new $c($this->_getClassFromRow($row), $dbId, $flag));
        }
    }
}
