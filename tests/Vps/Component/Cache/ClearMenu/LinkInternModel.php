<?php
class Vps_Component_Cache_ClearMenu_LinkInternModel extends Vpc_Basic_LinkTag_Intern_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'primaryKey' => 'component_id',
            'data'=> array(
                array('component_id'=>'1-link', 'target'=>'1', 'rel'=>null, 'param'=>null)
            )
        ));
        parent::__construct($config);
    }
}
