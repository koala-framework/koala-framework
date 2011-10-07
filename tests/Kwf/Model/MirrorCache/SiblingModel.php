<?php
class Kwf_Model_MirrorCache_SiblingModel extends Kwf_Model_FnF
{
    protected $_referenceMap = array(
        'main' => array(
            'column' => 'id',
            'refModelClass' => 'Kwf_Model_MirrorCache_MirrorCacheModel'
        )
    );
}
