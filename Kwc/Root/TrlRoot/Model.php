<?php
class Kwc_Root_TrlRoot_Model extends Kwf_Model_FnF
{
    protected $_columns = array('id', 'filename', 'name', 'master');
    protected $_primaryKey = 'id';
    protected $_toStringField = 'name';
    // TODO: Auskommentiert, muss an nicht mehr vorhandene component_id-Spalte angepasst werden
    //protected $_siblingModels = array('Kwc_Root_TrlRoot_FieldModel');

    public function __construct(array $values = array())
    {
        $config['data'] = array();
        $master = true;
        foreach ($values as $key => $value) {
            $config['data'][] = array(
                'id' => $key,
                'filename' => $key,
                'name' => $value,
                'master' => $master,
                'visible' => 1
            );
            $master = false;
        }
        $config['toStringField'] = 'name';
        parent::__construct($config);
    }
}
