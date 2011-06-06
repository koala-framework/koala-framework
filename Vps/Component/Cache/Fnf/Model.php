<?php
class Vps_Component_Cache_Fnf_Model extends Vps_Component_Cache_Mysql_Model
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'fakeId',
            'columns' => array('fakeId', 'component_id', 'db_id', 'component_class', 'type', 'value', 'expire', 'deleted', 'content'),
            'uniqueColumns' => array('component_id', 'type', 'value'),
            'default' => array('deleted' => false)
        ));
        parent::__construct($config);
    }

    // zum Testen
    public function countActiveRows()
    {
        return $this->countRows($this->select()->whereEquals('deleted', false));
    }

    // zum Testen
    public function getContent($select = array())
    {
        foreach ($this->getRows($select) as $row) {
            $ret[] = $row->component_id . '(' . $row->type . ' - ' . $row->value . ') ' . $row->component_class . ': ' . $row->deleted;
        }
        return $ret;
    }
}
