<?php
class Vps_Component_Cache_ComponentLink_DbIdShortcut_Model extends Vps_Model_FnF
{
    protected $_data = array(
        array('id' => 1, 'name' => 'foo')
    );
    protected $_columns = array('id', 'name');
    protected $_toStringField = 'name';
}
