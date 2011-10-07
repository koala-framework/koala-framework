<?php
class Kwf_Model_ChildRows_ChildModel extends Kwf_Model_FnF
{
    protected $_referenceMap = array(
        'Parent'=>array(
            'column' => 'test_id',
            'refModelClass' => 'Kwf_Model_ChildRows_Model'
        )
    );
}
