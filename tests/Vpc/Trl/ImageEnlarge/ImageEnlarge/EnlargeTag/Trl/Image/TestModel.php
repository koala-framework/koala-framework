<?php
class Vpc_Trl_ImageEnlarge_ImageEnlarge_EnlargeTag_Trl_Image_TestModel extends Vpc_Abstract_Image_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnFFile(array(
            'primaryKey' => 'component_id',
            'uniqueIdentifier' => get_class($this).'-Proxy'
        ));
        $this->_referenceMap['Image']['refModelClass'] = 'Vpc_Trl_ImageEnlarge_UploadsModel';
        parent::__construct($config);
    }


    protected function _init()
    {
        parent::_init();
        $this->_siblingModels = array();
    }
}
