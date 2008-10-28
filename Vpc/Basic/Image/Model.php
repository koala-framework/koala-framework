<?php
class Vpc_Basic_Image_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_basic_image';
    protected $_rowClass = 'Vpc_Basic_Image_Row';
    protected $_referenceMap    = array(
        'Image' => array(
            'column'           => 'vps_upload_id',
            'refModelClass'     => 'Vps_Uploads_Model'
        )
    );
}
