<?php
class Kwc_Advanced_AudioPlayer_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwc_advanced_audio_player';

    protected $_referenceMap    = array(
        'FileMp3' => array(
            'column'           => 'mp3_kwf_upload_id',
            'refModelClass'     => 'Kwf_Uploads_Model',
        )
    );
}
