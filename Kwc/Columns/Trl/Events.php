<?php
class Kwc_Columns_Trl_Events extends Kwc_Abstract_List_Trl_Events
{
    protected function onMasterRowUpdate(Kwf_Component_Event_Row_Abstract $event)
    {
        parent::onMasterRowUpdate($event);

        if ($event->isDirty('visible')) { //trl doesn't have own visible, master visible is used

            $chainedType = 'Trl';

            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($event->row->component_id) as $c) {
                $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($c, $chainedType);
                foreach ($chained as $c) {
                    $this->fireEvent(
                        new Kwf_Component_Event_Component_ContentChanged($this->_class, $c)
                    );
                }
            }
        }
    }
}
