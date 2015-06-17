<?php
class Kwf_Component_Cache_Fnf_Model extends Kwf_Model_FnF
{
    protected $_primaryKey = 'fakeId';
    protected $_columns = array('fakeId', 'component_id', 'db_id', 'page_db_id', 'expanded_component_id', 'component_class', 'renderer', 'type', 'value', 'tag', 'microtime', 'expire', 'deleted', 'content');
    protected $_uniqueColumns = array('component_id', 'type', 'value');
    protected $_default = array('deleted' => false);

    // zum Testen
    public function countActiveRows()
    {
        return $this->countRows($this->select()->whereEquals('deleted', false));
    }

    // zum Testen
    public function getContent($select = array())
    {
        $ret = array();
        foreach ($this->getRows($select) as $row) {
            $ret[] = $row->component_id . '(' . $row->type . ' - ' . $row->value . ') ' . $row->component_class . ': ' . $row->deleted;
        }
        return $ret;
    }
}
