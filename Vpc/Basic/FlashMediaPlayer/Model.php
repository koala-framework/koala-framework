<?php
class Vpc_Basic_FlashMediaPlayer_Model extends Vps_Model_Db
{
    protected $_table = 'vpc_basic_flashmediaplayer';
    protected $_referenceMap    = array(
        'FileMedia' => array(
            'column'            => 'vps_upload_id_media',
            'refModelClass'     => 'Vps_Uploads_Model'
        )
    );
}
