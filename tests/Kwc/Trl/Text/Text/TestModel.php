<?php
class Kwc_Trl_Text_Text_TestModel extends Kwc_Basic_Text_Model
{
    public function __construct($config = array())
    {
        $this->_dependentModels['ChildComponents'] = 'Kwc_Trl_Text_Text_TestChildComponentsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array('component_id', 'content', 'data'),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root-master_text', 'content'=>'<p>foo</p>'),
                    array('component_id'=>'root-en_text-child', 'content'=>'<p>fooen</p>')
                )
            ));
        parent::__construct($config);
    }
}
