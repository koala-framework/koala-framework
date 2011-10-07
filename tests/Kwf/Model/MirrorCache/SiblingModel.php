<?php
class Vps_Model_MirrorCache_SiblingModel extends Vps_Model_FnF
{
    protected $_referenceMap = array(
        'main' => array(
            'column' => 'id',
            'refModelClass' => 'Vps_Model_MirrorCache_MirrorCacheModel'
        )
    );
}
