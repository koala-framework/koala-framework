<?php
class Vpc_Trl_TextImage_TextImage_Text_TestModel extends Vpc_Basic_Text_Model
{
    public function __construct($config = array())
    {
        $this->_dependentModels['ChildComponents'] = 'Vpc_Trl_TextImage_TextImage_Text_ChildComponentsModel';

        $config['proxyModel'] = new Vps_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root-master_test-text', 'content'=>'<p>foo</p>', 'data'=>''),
                    array('component_id'=>'root-en_test-text-text', 'content'=>'<p>fooen</p>')
                )
            ));
        parent::__construct($config);
    }
}
