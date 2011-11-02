<?php
class Kwf_Component_Events_PseudoPage_Model extends Kwf_Model_FnF
{
    public function __construct()
    {
        $config = array('data'=>array(
            array('id'=>1, 'name' => 'F1', 'pos' => 1, 'visible' => 1),
            array('id'=>2, 'name' => 'F2', 'pos' => 2, 'visible' => 1),
            array('id'=>3, 'name' => 'F3', 'pos' => 3, 'visible' => 0),
            array('id'=>4, 'name' => 'F4', 'pos' => 4, 'visible' => 1)
        ));
        parent::__construct($config);
    }
}
