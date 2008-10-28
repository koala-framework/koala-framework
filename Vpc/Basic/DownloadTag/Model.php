<?php
class Vpc_Basic_DownloadTag_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_basic_downloadtag';
    protected $_referenceMap    = array(
        'File' => array(
            'column'           => 'vps_upload_id',
            'refModelClass'     => 'Vps_Uploads_Model',
        )
    );
}
