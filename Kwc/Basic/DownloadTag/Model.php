<?php
class Vpc_Basic_DownloadTag_Model extends Vpc_Basic_LinkTag_Abstract_Model
{
    protected $_rowClass = 'Vpc_Basic_DownloadTag_Row';
    protected $_table = 'vpc_basic_downloadtag';
    protected $_toStringField = 'filename';

    protected $_referenceMap    = array(
        'File' => array(
            'column'           => 'vps_upload_id',
            'refModelClass'     => 'Vps_Uploads_Model',
        )
    );
}
