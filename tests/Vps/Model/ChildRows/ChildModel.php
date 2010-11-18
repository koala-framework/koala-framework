<?php
class Vps_Model_ChildRows_ChildModel extends Vps_Model_FnF
{
    protected $_referenceMap = array(
        'Parent'=>array(
            'column' => 'test_id',
            'refModelClass' => 'Vps_Model_ChildRows_Model'
        )
    );
}
