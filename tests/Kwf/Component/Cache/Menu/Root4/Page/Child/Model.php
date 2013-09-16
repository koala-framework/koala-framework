<?php
class Kwf_Component_Cache_Menu_Root4_Page_Child_Model extends Kwf_Model_FnF
{
    protected $_toStringField = 'id';

    public function __construct()
    {
        $config = array('data'=>array(
            array('id' => 1, 'visible' => true),
        ));
        parent::__construct($config);
    }
}
