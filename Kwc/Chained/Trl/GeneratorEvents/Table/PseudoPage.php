<?php
class Kwc_Chained_Trl_GeneratorEvents_Table_PseudoPage extends Kwc_Chained_Trl_GeneratorEvents_Table
{
    protected $_nameColumn;

    public function onRowUpdate(Kwf_Events_Event_Row_Abstract $event)
    {
        parent::onRowUpdate($event);

        $dbId = $event->row->component_id;

        $nameChanged = false;
        $filenameChanged = false;

        if ($this->_nameColumn) {
            $nameColumn = $this->_nameColumn;
        } else {
            $nameColumn = $this->_getChainedGenerator()->getSetting('nameColumn');
        }
        if ($nameColumn && $this->_getGenerator()->getModel()->hasColumn($nameColumn)) { //hardcoded column names as in generator
            $nameChanged = $event->isDirty($nameColumn);
        }
        if ($this->_getGenerator()->getModel()->hasColumn('filename')) { //hardcoded column names as in generator
            $filenameChanged = $event->isDirty('filename');
        } else {
            $filenameChanged = $nameChanged;
        }

        if ($nameChanged) {
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId) as $c) {
                if ($c->generator === $this->_getGenerator()) {
                    $this->fireEvent(new Kwf_Component_Event_Page_NameChanged(
                        $this->_class, $c
                    ));
                }
            }
        }
        if ($filenameChanged) {
            foreach (Kwf_Component_Data_Root::getInstance()->getComponentsByDbId($dbId) as $c) {
                if ($c->generator === $this->_getGenerator()) {
                    $this->fireEvent(new Kwf_Component_Event_Page_RecursiveUrlChanged(
                        $this->_class, $c
                    ));
                }
            }
        }
    }

    public function onMasterRowUpdate(Kwf_Events_Event_Row_Updated $event)
    {
        parent::onMasterRowUpdate($event);
        $nameChanged = false;
        $filenameChanged = false;

        if ($this->_getGenerator()->getModel() && !$this->_getGenerator()->getModel()->hasColumn('name')) { //does trl have own name?
            $nameColumn = $this->_getChainedGenerator()->getNameColumn();
            if (!$nameColumn) {
                $nameColumn = $event->row->getModel()->getToStringField();
            }
            if (!$nameColumn) {
                foreach ($event->row->getModel()->getColumns() as $column) {
                    if (!in_array($column, array('id', 'component_id', 'component', 'visible', 'pos')) &&
                        $event->isDirty($column)
                    ) {
                        $nameChanged = true;
                    }
                }
            } else {
                $nameChanged = $event->isDirty($nameColumn);
            }
        }
        if ($this->_getGenerator()->getModel() && !$this->_getGenerator()->getModel()->hasColumn('filename')) { //does trl have own filename?
            $filenameColumn = $this->_getChainedGenerator()->getFilenameColumn();
            if (!$filenameColumn) {
                $filenameChanged = $nameChanged;
            } else {
                $filenameChanged = $event->isDirty($filenameColumn);
            }
        }

        if ($nameChanged) {
            foreach ($this->_getComponentsFromMasterRow($event->row, array('ignoreVisible'=>false)) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Page_NameChanged(
                    $this->_class, $c
                ));
            }
        }
        if ($filenameChanged) {
            foreach ($this->_getComponentsFromMasterRow($event->row, array('ignoreVisible'=>false)) as $c) {
                $this->fireEvent(new Kwf_Component_Event_Page_RecursiveUrlChanged(
                    $this->_class, $c
                ));
            }
        }

    }

}
