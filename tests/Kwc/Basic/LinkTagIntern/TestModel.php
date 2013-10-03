<?php
class Kwc_Basic_LinkTagIntern_TestModel extends Kwc_Basic_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'columns' => array(),
            'data'=> array(
                array('component_id'=>'1300', 'target'=>'1310'),
                array('component_id'=>'1302', 'target'=>'1399'), //nicht vorhandene seite
                array('component_id'=>'1303', 'target'=>'1311'), //unsichtbar
            )
        ));
        parent::__construct($config);
    }
}
