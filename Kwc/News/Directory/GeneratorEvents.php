<?php
class Kwc_News_Directory_GeneratorEvents extends Kwf_Component_Generator_PseudoPage_Events_Table
{
    public function onRowUpdate(Kwf_Events_Event_Row_Updated $event)
    {
        parent::onRowUpdate($event);

        $value = $event->row->publish_date;
        $cleanValue = $event->row->getCleanValue('publish_date');
        $date = date('Y-m-d');
        if ($value > $date && $cleanValue <= $date) {
            $this->_fireAddedRemovedEvents($event->row, 'Added');
        } else if ($value <= $date && $cleanValue > $date) {
            $this->_fireAddedRemovedEvents($event->row, 'Removed');
        }

        if (Kwc_Abstract::getSetting($this->_class, 'enableExpireDate')) {
            $value = $event->row->expiry_date;
            $cleanValue = $event->row->getCleanValue('expiry_date');
            $date = date('Y-m-d');
            if ($value <= $date && $cleanValue > $date) {
                $this->_fireAddedRemovedEvents($event->row, 'Added');
            } else if ($value >= $date && $cleanValue < $date) {
                $this->_fireAddedRemovedEvents($event->row, 'Removed');
            }
        }
    }

    private function _fireAddedRemovedEvents($row, $type)
    {
        foreach ($this->_getComponentsFromRow($row, array('ignoreVisible'=>true)) as $c) {
            $class = "Kwf_Component_Event_Component_$type";
            $this->fireEvent(new $class(
                $c->componentClass, $c, Kwf_Component_Event_Component_AbstractFlag::FLAG_VISIBILITY_CHANGED
            ));
            $class = "Kwf_Component_Event_Page_$type";
            $this->fireEvent(new $class(
                $c->componentClass, $c, Kwf_Component_Event_Component_AbstractFlag::FLAG_VISIBILITY_CHANGED
            ));
        }
    }
}
