<?php
class Vps_Model_ChildRows_ChildModel extends Vps_Model_FnF
{
    protected $_referenceMap = array(
        'Parent'=>array(
            'column' => 'test_id',
            'refModelClass' => 'Vps_Model_ChildRows_Model'
        )
    );
    protected $_data = array(
        array('id'=>1, 'test_id'=>1, 'bar'=>'bar1'),
        array('id'=>2, 'test_id'=>1, 'bar'=>'bar2')
    );

}
