<?php
class Vps_Component_Cache_TestModelField extends Vps_Model_FnF
{
    protected $_data = array(
        array('id' => 1, 'foo' => 7),
        array('id' => 2, 'foo' => 8)
    );
    protected $_columns = array('id', 'foo');
    protected $_primaryKey = 'id';
}
