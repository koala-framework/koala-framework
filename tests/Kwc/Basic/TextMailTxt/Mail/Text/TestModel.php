<?php
class Kwc_Basic_TextMailTxt_Mail_Text_TestModel extends Kwc_Basic_Text_Model
{
    public function __construct($config = array())
    {
        $this->_dependentModels['ChildComponents'] = 'Kwc_Basic_TextMailTxt_Mail_Text_TestChildComponentsModel';

        $config['proxyModel'] = new Kwf_Model_FnF(array(
                'columns' => array('component_id', 'content', 'data'),
                'primaryKey' => 'component_id',
                'data'=> array(
                    array('component_id'=>'root_mail1-content', 'content'=>'<p>xxy <a href="root_mail1-content-l1">foo</a> yyx</p>'),
                    array('component_id'=>'root_mail2-content', 'content'=>'<p>xxy<a href="root_mail1-content-l1">foo</a>yyx</p>')
                )
            ));
        parent::__construct($config);
    }
}
