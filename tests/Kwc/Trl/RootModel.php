<?php
class Kwc_Trl_RootModel extends Kwc_Root_TrlRoot_Model
{
    protected $_siblingModels = array('Kwc_Trl_RootSiblingModel');

    public function __construct(array $values = array())
    {
        parent::__construct($values);
        $siblings = $this->getSiblingModels();
        $data = array();
        foreach ($values as $key => $val) {
            $data[] = array('id' => $key, 'visible' => 1);
        }
        $this->_siblingModels = array(new Kwc_Trl_RootSiblingModel(array('data' => $data)));

        /*
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
        parent::__construct($config);
        */
    }
}
