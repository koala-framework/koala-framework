<?php
class Vpc_Basic_FlashMediaPlayer_Model extends Vpc_Table
{
    protected $_name = 'vpc_basic_flashmediaplayer';
    protected $_referenceMap    = array(
        'FileMedia' => array(
            'columns'           => array('vps_upload_id_media'),
            'refTableClass'     => 'Vps_Dao_File',
            'refColumns'        => array('id')
        )
    );
}
