<?php
class Kwc_Trl_TextImage_TextImage_Text_TestModel extends Kwc_Basic_Text_Model
{
    public function __construct($config = array())
    {
        $this->_dependentModels['ChildComponents'] = 'Kwc_Trl_TextImage_TextImage_Text_ChildComponentsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root-master_test-text', 'content'=>'<p>foo</p>', 'data'=>''),
                    array('component_id'=>'root-en_test-text-text', 'content'=>'<p>fooen</p>')
                )
            ));
        parent::__construct($config);
    }
}
