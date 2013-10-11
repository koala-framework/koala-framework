<?php
class Kwc_Basic_LinkIntern_LinkTag_Model extends Kwc_Basic_LinkTag_Intern_Model
{
    public function __construct()
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'primaryKey' => 'component_id',
            'columns' => array(),
            'data'=> array(
                array('component_id'=>'1', 'target'=>'3'),
            )
        ));
        parent::__construct($config);
    }
}
