<?php
class Kwf_Component_Generator_Box_Events_StaticSelect extends Kwf_Component_Generator_Events_Static
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onOwnRowUpdate'
        );
        return $ret;
    }

    public function onOwnRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        if ($event->isDirty('component')) {
            $id = $event->row->component_id;
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($id, array('ignoreVisible'=>true)) as $c) {
                if ($c->generator === $this->_getGenerator()) {
                    $this->fireEvent(new Kwf_Component_Event_Component_RecursiveRemoved($this->_getClassFromRow($event->row, true), $c->componentId));
                    $this->fireEvent(new Kwf_Component_Event_Component_RecursiveAdded($this->_getClassFromRow($event->row, false), $c->componentId));
                }
            }
        }
    }

    protected function _getClassFromRow($row, $cleanValue = false)
    {
        $classes = $this->_getGenerator()->getChildComponentClasses();
        if ($cleanValue) {
            $c = $row->getCleanValue('component');
        } else {
            $c = $row->component;
        }
        if (isset($classes[$c])) {
            return $classes[$row->component];
        }
        $class = array_shift($classes);
        return $class;
    }
}
