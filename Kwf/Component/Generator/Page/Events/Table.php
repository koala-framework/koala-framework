<?php
class Kwf_Component_Generator_Page_Events_Table extends Kwf_Component_Generator_Events_Table
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Kwf_Component_Event_Component_Added',
            'callback' => 'onComponentEvent'
        );
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Kwf_Component_Event_Component_Removed',
            'callback' => 'onComponentEvent'
        );
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Kwf_Component_Event_Component_ClassChanged',
            'callback' => 'onComponentEvent'
        );
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Kwf_Component_Event_Component_PositionChanged',
            'callback' => 'onComponentEvent'
        );
        $ret[] = array(
            'class' => $this->_config['componentClass'],
            'event' => 'Kwf_Component_Event_Page_FilenameChanged',
            'callback' => 'onPageFilenameChanged'
        );
        return $ret;
    }

    public function onComponentEvent(Kwf_Component_Event_Component_Abstract $event)
    {
        $eventsClass = null;
        if ($event instanceof Kwf_Component_Event_Component_Added) {
            $eventsClass = 'Kwf_Component_Event_Page_Added';
        } else if ($event instanceof Kwf_Component_Event_Component_Removed) {
            $eventsClass = 'Kwf_Component_Event_Page_Removed';
        } else if ($event instanceof Kwf_Component_Event_Component_ClassChanged) {
            $eventsClass = 'Kwf_Component_Event_Page_ClassChanged';
        } else if ($event instanceof Kwf_Component_Event_Component_PositionChanged) {
            $eventsClass = 'Kwf_Component_Event_Page_PositionChanged';
        }
        if ($eventsClass) {
            $this->fireEvent(new $eventsClass($this->_class, $event->dbId));
        }
    }

    public function onRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        parent::onRowUpdate($event);
        $dc = array_flip($event->row->getDirtyColumns());
        $nameChanged = false;
        $filenameChanged = false;

        $nameColumn = $this->_getGenerator()->getNameColumn();
        if (!$nameColumn) {
            $nameColumn = $event->row->getModel()->getToStringField();
        }
        if (!$nameColumn) {
            foreach ($event->row->getModel()->getColumns() as $column) {
                if (!in_array($column, array('id', 'component_id', 'component', 'visible', 'pos')) &&
                    isset($dc[$column])
                ) {
                    $nameChanged = true;
                }
            }
        } else {
            $nameChanged = isset($dc[$nameColumn]);
        }
        $filenameColumn = $this->_getGenerator()->getFilenameColumn();
        if (!$filenameColumn) {
            $filenameChanged = $nameChanged;
        } else {
            $filenameChanged = isset($dc[$filenameColumn]);
        }
        if ($nameChanged) {
            $this->fireEvent(new Kwf_Component_Event_Page_NameChanged(
                $this->_class, $this->_getDbId($event->row)
            ));
        }
        if ($filenameChanged) {
            $this->fireEvent(new Kwf_Component_Event_Page_FilenameChanged(
                $this->_class, $this->_getDbId($event->row)
            ));
        }
    }

    public function onPageFilenameChanged(Kwf_Component_Event_Page_FilenameChanged $event)
    {
        $components = Kwf_Component_Data_Root::getInstance()
            ->getComponentsByDbId($event->dbId);
        foreach ($components as $component) {
            $this->fireEvent(new Kwf_Component_Event_Page_RecursiveUrlChanged(
                $this->_class, $component->componentId
            ));
        }
    }
}