<?php
class Kwc_Chained_Trl_GeneratorEvents extends Kwf_Component_Generator_Events
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $masterComponentClass = Kwc_Abstract::getSetting($this->_class, 'masterComponentClass');
        $generatorKey = $this->_getGenerator()->getGeneratorKey();
        $masterGenerator = Kwf_Component_Generator_Abstract::getInstance($masterComponentClass, $generatorKey);;

        if ($masterGenerator->getGeneratorFlag('table')) {
            if ($this->_getGenerator()->getModel()) {
                $ret[] = array(
                    'class' => get_class($this->_getGenerator()->getModel()),
                    'event' => 'Kwf_Component_Event_Row_Updated',
                    'callback' => 'onRowUpdate'
                );
            }
            if ($masterGenerator->getModel()) {
                $ret[] = array(
                    'class' => get_class($masterGenerator->getModel()),
                    'event' => 'Kwf_Component_Event_Row_Updated',
                    'callback' => 'onMasterRowUpdate'
                );
            }
        }
        return $ret;
    }

    public function onRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
//TODO alle weiteren events wie in TableGenerator und Page etc. (added, filename uvm.)
        if ($event->row->getModel()->hasColumn('component_id')) {
            $dbId = $event->row->component_id;
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId) as $c) {
                if ($c->generator === $this->_getGenerator()) {
                    $this->fireEvent(new Kwf_Component_Event_Component_RowUpdated(
                        $c->componentClass, $c
                    ));
                }
            }
        }
    }

    public function onMasterRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        $chainedType = 'Trl';

        if ($event->row->getModel()->hasColumn('component_id')) {
            $dbId = $event->row->component_id.$this->_getGenerator()->getIdSeparator().$event->row->{$event->row->getModel()->getPrimaryKey()};
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId) as $c) {
                $select = array('ignoreVisible'=>true);
                $chained = Kwc_Chained_Abstract_Component::getAllChainedByMaster($c, $chainedType, $select);
                foreach ($chained as $i) {
                    $this->fireEvent(new Kwf_Component_Event_Component_RowUpdated(
                        $i->componentClass, $i
                    ));
                }
            }
        }

    }
}
