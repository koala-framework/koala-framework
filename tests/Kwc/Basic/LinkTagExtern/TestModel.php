<?php
class Kwc_Basic_LinkTagExtern_TestModel extends Kwc_Basic_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'columns' => array(),
            'data'=> array(
                array('component_id'=>'1200', 'target'=>'http://example.com',
                        'rel'=>'', 'param' => '', 'open_type' => 'self'),
                array('component_id'=>'1201', 'target'=>'http://example.com',
                        'rel'=>'', 'param' => '', 'open_type' => 'blank'),
                array('component_id'=>'1202', 'target'=>'http://example.com',
                        'rel'=>'', 'param' => '', 'open_type' => 'popup',
                        'width'=>200, 'height'=>300),
            )
        ));
        parent::__construct($config);
    }
}
