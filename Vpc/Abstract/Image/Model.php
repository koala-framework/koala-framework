<?php
class Vpc_Abstract_Image_Model extends Vps_Model_Db_Proxy
{
    protected $_primaryKey = 'component_id';
    protected $_table = 'vpc_basic_image';
    protected $_rowClass = 'Vpc_Abstract_Image_Row';
    protected $_referenceMap    = array(
        'Image' => array(
            'column'           => 'vps_upload_id',
            'refModelClass'     => 'Vps_Uploads_Model'
        )
    );
    protected function _init()
    {
        $this->_siblingModels = array(
            new Vps_Model_Field(array(
                'fieldName' => 'data'
            ))
        );
        parent::_init();
    }
}
