<?php
class Vpc_Basic_ImagePosition_Model extends Vps_Model_Field
{
    public function __construct(array $config = array())
    {
        $config['parentModel'] = new Vps_Model_Db(array(
            'table' => new Vps_Dao_Vpc()
        ));
        $config['fieldName'] = 'data';
        parent::__construct($config);
    }
    
    public function isEqual(Vps_Model_Interface $other) {
        return (
            $other instanceof Vps_Model_Field &&
            $this->_fieldName($other->_fieldName)
        );
    }
}
