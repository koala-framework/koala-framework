<?php
class Kwc_Basic_DownloadTag_Model extends Kwc_Basic_LinkTag_Abstract_Model
{
    protected $_rowClass = 'Kwc_Basic_DownloadTag_Row';
    protected $_table = 'kwc_basic_downloadtag';
    protected $_toStringField = 'filename';

    protected $_referenceMap    = array(
        'File' => array(
            'column'           => 'kwf_upload_id',
            'refModelClass'     => 'Kwf_Uploads_Model',
        )
    );
}
