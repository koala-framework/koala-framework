<?php
class Vps_Model_FnF_SiblingMasterModel extends Vps_Model_FnF
{
    protected $_siblingModels = array('Vps_Model_FnF_SiblingModel');
    protected $_columns = array('id', 'foo');
    protected $_data = array(
        array('id'=>1, 'foo'=>'foo1'),
        array('id'=>2, 'foo'=>'foo2'),
        array('id'=>3, 'foo'=>'foo3')
    );
}
