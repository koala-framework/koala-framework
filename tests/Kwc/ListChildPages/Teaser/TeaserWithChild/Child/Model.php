<?php
class Kwc_ListChildPages_Teaser_TeaserWithChild_Child_Model extends Kwf_Model_FnF
{
    public function __construct($config = array())
    {
        $config['columns'] = array('id', 'component_id', 'visible');
        $config['namespace'] = 'listchildpages_teaser_teaserwithchild_model';
        $config['primaryKey'] = 'component_id';
        //'{component_id}-{id}
        $config['data'] = array(
            array('component_id'=>'401-600', 'visible' => false),
            array('component_id'=>'401-601', 'visible' => true),
            array('component_id'=>'401-602', 'visible' => true),
//             array('component_id'=>'401-603', 'visible' => true),
        );
        parent::__construct($config);
    }
}
