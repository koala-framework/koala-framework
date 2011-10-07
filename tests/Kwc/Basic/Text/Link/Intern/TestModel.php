<?php
class Kwc_Basic_Text_Link_Intern_TestModel extends Kwc_Basic_LinkTag_Intern_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
            )
        ));
        parent::__construct($config);
    }
}

