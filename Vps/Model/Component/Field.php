<?php
class Vps_Model_Component_Field extends Vps_Model_Field
{
    public function __construct(array $config = array())
    {
        $config['parentModel'] = new Vps_Model_Db(array(
            'table' => new Vps_Dao_ComponentField()
        ));
        $config['fieldName'] = 'data';
        parent::__construct($config);
    }
}
