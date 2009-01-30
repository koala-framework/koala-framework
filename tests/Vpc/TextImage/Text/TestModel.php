<?php
class Vpc_TextImage_Text_TestModel extends Vpc_Basic_Text_Model
{
    public function __construct($config = array())
    {
        $this->_dependentModels['ChildComponents'] = new Vps_Model_FnF();

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root_textImage1-text', 'content'=>'<p>foo</p>')
                )
            ));
        parent::__construct($config);
    }
}
