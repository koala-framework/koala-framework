<?php
class Kwf_Component_Cache_Chained_Master_ChildModel extends Kwf_Model_Fnf
{
    protected $_columns = array('id', 'value');
    protected $_data = array(
        array('id' => '1', 'value' => 'foo')
    );
    protected $_toStringField = 'value';
}
