<?php
class Vpc_Cc_Paragraphs_Master_Paragraphs_TestModel extends Vpc_Paragraphs_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'columns' => array('id', 'component_id', 'pos', 'visible', 'component'),
            'primaryKey' => 'id',
        ));
        parent::__construct($config);
    }
}
