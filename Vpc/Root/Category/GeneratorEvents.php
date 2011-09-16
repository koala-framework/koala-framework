<?php
class Vpc_Root_Category_GeneratorEvents extends Vps_Component_Generator_Page_Events_Table
{
    public function getListeners()
    {
        $ret = parent::getListeners();
        $ret[] = array(
            'class' => get_class($this->_getGenerator()->getModel()),
            'event' => 'Vps_Component_Event_Row_Updated',
            'callback' => 'onPageRowUpdate'
        );
        $ret[] = array(
            'event' => 'Vps_Component_Event_Page_FilenameChanged',
            'callback' => 'onPageFilenameChanged'
        );
        return $ret;
    }

    public function onPageFilenameChanged(Vps_Component_Event_Page_FilenameChanged $event)
    {
        d($event);
    }

    public function onPageRowUpdate(Vps_Component_Event_Row_Updated $event)
    {
        if (in_array('parent_id', $event->row->getDirtyColumns())) {
            $ids = array();
            foreach ($event->row->getModel()->getRows() as $row) {
                $ids[$row->parent_id][] = $row->id;
            }
            foreach ($this->_getRecursiveChildIds($event->row->id, $ids) as $id) {
                $this->fireEvent(
                    new Vps_Component_Event_Page_ParentChanged($this->_class, $id)
                );
            }
        }
    }

    private function _getRecursiveChildIds($id, $ids)
    {
        $ret = array($id);
        if (isset($ids[$id])) {
            foreach ($ids[$id] as $id) {
                $ret = array_merge($ret, $this->_getRecursiveChildIds($id, $ids));
            }
        }
        return $ret;
    }
}
