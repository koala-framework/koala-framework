<?php
class Kwc_ColumnsResponsive_Trl_Events extends Kwc_Chained_Abstract_Events
{
    public function onMasterContentChanged($event)
    {
        $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($event->component, 'Trl');
        foreach ($chained as $c) {
            $this->fireEvent(
                new Kwf_Component_Event_Component_ContentChanged($this->_class, $c)
            );
        }
    }
}