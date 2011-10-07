<?php
class Vpc_Basic_LinkTagExtern_TestModel extends Vpc_Basic_LinkTag_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'columns' => array(),
            'data'=> array(
                array('component_id'=>'1200', 'target'=>'http://example.com',
                        'rel'=>'', 'param' => '', 'open_type' => 'self'),
                array('component_id'=>'1201', 'target'=>'http://example.com',
                        'rel'=>'', 'param' => '', 'open_type' => 'blank'),
                array('component_id'=>'1202', 'target'=>'http://example.com',
                        'rel'=>'', 'param' => '', 'open_type' => 'popup',
                        'width'=>200, 'height'=>300, 'menubar'=>1, 'toolbar'=>1,
                        'locationbar'=>0, 'statusbar'=>0, 'scollbars'=>1, 'resizable'=>1),
            )
        ));
        parent::__construct($config);
    }
}
