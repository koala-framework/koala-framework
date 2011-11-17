<?php
class Kwf_Component_Generator_PseudoPage_Events_Table extends Kwf_Component_Generator_Events_Table
{
    public function onRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        parent::onRowUpdate($event);
        $nameChanged = false;
        $filenameChanged = false;

        $nameColumn = $this->_getGenerator()->getNameColumn();
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
        $filenameColumn = $this->_getGenerator()->getFilenameColumn();
        if (!$filenameColumn) {
            $filenameChanged = $nameChanged;
        } else {
            $filenameChanged = $event->isDirty($filenameColumn);
        }
        if ($nameChanged) {
            foreach ($this->_getDbIdsFromRow($event->row) as $dbId) {
                $this->fireEvent(new Kwf_Component_Event_Page_NameChanged(
                    $this->_class, $dbId
                ));
            }
        }
        if ($filenameChanged) {
            foreach ($this->_getDbIdsFromRow($event->row) as $dbId) {
                $components = Kwf_Component_Data_Root::getInstance()
                    ->getComponentsByDbId($dbId);
                foreach ($components as $component) {
                    $this->fireEvent(new Kwf_Component_Event_Page_RecursiveUrlChanged(
                        $this->_class, $component->componentId
                    ));
                }
            }
        }
    }
}
