<?php
class Vpc_Basic_TextSessionModel_TestModel extends Vpc_Basic_Text_Model
{
    public function __construct($config = array())
    {
        $this->_dependentModels['ChildComponents'] = 'Vpc_Basic_TextSessionModel_TestChildComponentsModel';

        $config['proxyModel'] = new Vps_Model_Session(array(
                'namespace' => 'TextSessionModel_TestModel',
                'columns' => array('component_id', 'content', 'data'),
                'primaryKey' => 'component_id',
                'defaultData'=> array(
                    array('component_id'=>'root-text', 'content'=>'<p>foo</p>')
                )
            ));
        parent::__construct($config);
    }
}
