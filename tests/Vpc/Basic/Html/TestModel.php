<?php
class Vpc_Basic_Html_TestModel extends Vpc_Basic_Html_Model
{
    public function __construct($config = array())
    {
        $this->_default = array('content'=>'ShouldGetOverwritten');
        $config['proxyModel'] = new Vps_Model_FnF(array(
                'columns' => array('component_id', 'content'),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'2000', 'content'=>'<p>foo</p>'),
                    array('component_id'=>'2001', 'content'=>'<p>foo{test}bar</p>'),
                    array('component_id'=>'2002', 'content'=>'<p>foo{testx}bar</p>'),
                    array('component_id'=>'2003', 'content'=>'<p>foo{testbar</p>')
                )
            ));
        parent::__construct($config);
    }
}
