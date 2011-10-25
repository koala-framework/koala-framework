<?php
class Kwf_Model_Mongo_ChildRowsWithMirrorCacheSimple_SourceChildModel extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>100, 'source_id'=>1, 'blub'=>'blub'),
    );

    protected $_referenceMap = array(
        'Source' => 'source_id->Kwf_Model_Mongo_ChildRowsWithMirrorCacheSimple_MongoModel'
    );
}