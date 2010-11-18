<?php
class Vps_Model_ChildRows_Model extends Vps_Model_FnF
{
    protected $_dependentModels = array('Child'=>'Vps_Model_ChildRows_ChildModel');
    protected $_toStringField = 'foo';
}
