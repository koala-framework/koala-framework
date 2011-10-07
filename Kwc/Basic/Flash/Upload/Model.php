<?php
class Kwc_Basic_Flash_Upload_Model extends Kwf_Component_FieldModel
{
    protected $_referenceMap = array(
        'FileMedia' => array(
            'column'            => 'kwf_upload_id_media',
            'refModelClass'     => 'Kwf_Uploads_Model'
        )
    );
    protected $_default = array(
        'width' => 400,
        'height' => 300,
        'allow_fullscreen' => 0,
        'menu' => 1
    );

    protected $_rowClass = 'Kwc_Basic_Flash_Upload_Row';

    protected function _init()
    {
        parent::_init();
        $this->_dependentModels['FlashVars'] = new Kwf_Model_FieldRows(array(
            'fieldName' => 'flash_vars'
        ));
    }
}
