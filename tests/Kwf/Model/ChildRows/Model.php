<?php
class Kwf_Model_ChildRows_Model extends Kwf_Model_FnF
{
    protected $_dependentModels = array('Child'=>'Kwf_Model_ChildRows_ChildModel');
    protected $_toStringField = 'foo';
}
