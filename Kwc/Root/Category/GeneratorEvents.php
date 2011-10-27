<?php
class Kwc_Root_Category_GeneratorEvents extends Kwf_Component_Generator_Page_Events_Table
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Row_Updated',
            'callback' => 'onPageRowUpdate'
        );
        array_unshift($ret, array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Row_Inserted',
            'callback' => 'onPageDataChanged'
        ));
        array_unshift($ret, array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Kwf_Component_Event_Row_Deleted',
            'callback' => 'onPageDataChanged'
        ));
        return $ret;
    }

    public function onPageFilenameChanged(Kwf_Component_Event_Page_FilenameChanged $event)
    {
        parent::onPageFilenameChanged($event);
        if (is_numeric($event->dbId)) {
            foreach ($this->_getRecursiveChildIds($event->dbId, $this->_getGenerator()->getModel()) as $id) {
                $this->fireEvent(
                    new Kwf_Component_Event_Page_RecursiveFilenameChanged($this->_class, $id)
                );
            }
        }
    }

    public function onPageRowUpdate(Kwf_Component_Event_Row_Updated $event)
    {
        if (in_array('parent_id', $event->row->getDirtyColumns())) {
            foreach ($this->_getRecursiveChildIds($event->row->id, $event->row->getModel()) as $id) {
                $this->fireEvent(
                    new Kwf_Component_Event_Page_ParentChanged($this->_class, $id)
                );
            }
        }
    }

    public function onPageDataChanged(Kwf_Component_Event_Row_Abstract $event)
    {
        $this->_getGenerator()->pageDataChanged();
    }

    private function _getRecursiveChildIds($id, $model)
    {
        $ids = array();
        foreach ($model->getRows() as $row) {
            $ids[$row->parent_id][] = $row->id;
        }
        return $this->_rekGetRecursiveChildIds($id, $ids);
    }

    private function _rekGetRecursiveChildIds($id, $ids)
    {
        $ret = array($id);
        if (isset($ids[$id])) {
            foreach ($ids[$id] as $id) {
                $ret = array_merge($ret, $this->_rekGetRecursiveChildIds($id, $ids));
            }
        }
        return $ret;
    }
}
