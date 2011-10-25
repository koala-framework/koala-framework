<?php
class Kwc_Basic_FlashMediaPlayer_Model extends Kwf_Model_Db
{
    protected $_table = 'kwc_basic_flashmediaplayer';
    protected $_referenceMap    = array(
        'FileMedia' => array(
            'column'            => 'kwf_upload_id_media',
            'refModelClass'     => 'Kwf_Uploads_Model'
        )
    );
    protected $_default = array(
        'width' => 400,
        'height' => 300
    );
}
