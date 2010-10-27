<?php
class Vpc_Basic_Flash_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_basic_flash';
    protected $_referenceMap = array(
        'FileMedia' => array(
            'column'            => 'vps_upload_id_media',
            'refModelClass'     => 'Vps_Uploads_Model'
        )
    );
    protected $_dependentModels = array(
        'FlashVars' => 'Vpc_Basic_Flash_FlashVarsModel'
    );
}
