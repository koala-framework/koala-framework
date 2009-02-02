<?php
class Vpc_Basic_ImageEnlarge_SmallImage_TestModel extends Vpc_Abstract_Image_Model
{
    public function __construct($config = array())
    {
        $this->_referenceMap['Image']['refModelClass'] = 'Vpc_Basic_ImageEnlarge_UploadsModel';

        $config['proxyModel'] = Vps_Model_Abstract::getInstance('Vpc_Basic_ImageEnlarge_TestModel')
            ->getProxyModel();
        parent::__construct($config);
    }
}
