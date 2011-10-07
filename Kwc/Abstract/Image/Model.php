<?php
class Kwc_Abstract_Image_Model extends Kwf_Model_Db_Proxy
{
    protected $_primaryKey = 'component_id';
    protected $_table = 'kwc_basic_image';
    protected $_rowClass = 'Kwc_Abstract_Image_Row';
    protected $_referenceMap    = array(
        'Image' => array(
            'column'           => 'kwf_upload_id',
            'refModelClass'     => 'Kwf_Uploads_Model'
        )
    );
    protected function _init()
    {
        $this->_siblingModels = array(
            new Kwf_Model_Field(array(
                'fieldName' => 'data'
            ))
        );
        parent::_init();
    }
}
