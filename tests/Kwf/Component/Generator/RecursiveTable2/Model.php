<?php
class Vps_Component_Generator_RecursiveTable2_Model extends Vps_Model_FnF
{
    public function __construct()
    {
        $config = array('data'=>array(
            array('id'=>1, 'component_id'=>'root_page', 'filename' => 'table', 'component'=>'table'),
            array('id'=>2, 'component_id'=>'root_page', 'filename' => 'table', 'component'=>'flagged'),
            array('id'=>3, 'component_id'=>'root_page-1', 'filename' => 'table', 'component'=>'empty'),
        ));
        parent::__construct($config);
    }
}
