<?php
class Kwf_Model_Events_UnionReFire_UnionModel extends Kwf_Model_Union
{
    protected $_columnMapping = 'Kwf_Model_Events_UnionReFire_TestMapping';
    protected $_models = array(
        't' => 'Kwf_Model_Events_UnionReFire_SourceModel',
    );
}
