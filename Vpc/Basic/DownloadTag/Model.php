<?php
class Vpc_Basic_DownloadTag_Model extends Vpc_Table
{
    protected $_name = 'vpc_basic_downloadtag';
    protected $_referenceMap    = array(
        'File' => array(
            'columns'           => array('vps_upload_id'),
            'refTableClass'     => 'Vps_Dao_File',
            'refColumns'        => array('id')
        )
    );
}
