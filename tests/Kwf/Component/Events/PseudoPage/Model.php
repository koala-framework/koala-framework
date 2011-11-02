<?php
class Kwf_Component_Events_PseudoPage_Model extends Kwf_Model_FnF
{
    public function __construct()
    {
        $config = array('data'=>array(
            array('id'=>1, 'name' => 'F1', 'pos' => 1, 'visible' => 1)
        ));
        parent::__construct($config);
    }
}
