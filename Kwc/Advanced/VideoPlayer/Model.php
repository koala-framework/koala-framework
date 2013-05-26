<?php
class Kwc_Advanced_VideoPlayer_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_advanced_video_player';
    protected $_toStringField = 'filename';

    protected $_referenceMap    = array(
        'FileMp4' => array(
            'column'           => 'mp4_kwf_upload_id',
            'refModelClass'     => 'Kwf_Uploads_Model',
        ),
        'FileWebm' => array(
            'column'           => 'webm_kwf_upload_id',
            'refModelClass'     => 'Kwf_Uploads_Model',
        ),
        'FileOgg' => array(
            'column'           => 'ogg_kwf_upload_id',
            'refModelClass'     => 'Kwf_Uploads_Model',
        )
    );
}
