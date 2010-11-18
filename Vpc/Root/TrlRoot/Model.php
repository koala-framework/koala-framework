<?php
class Vpc_Root_TrlRoot_Model extends Vps_Model_FnF
{
    protected $_columns = array('component_id', 'filename', 'name', 'master');
    protected $_primaryKey = 'component_id';
    protected $_siblingModels = array('Vpc_Root_TrlRoot_FieldModel');

    public function __construct(array $languages = array())
    {
        $config['data'] = array();
        $master = true;
        foreach ($languages as $key => $language) {
            $config['data'][] = array(
                'component_id' => 'root-' . $key,
                'filename' => $key,
                'name' => $language,
                'master' => $master
            );
            $master = false;
        }
        $config['toStringField'] = 'name';
        parent::__construct($config);
    }
}
