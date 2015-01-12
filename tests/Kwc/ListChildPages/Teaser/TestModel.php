<?php
class Kwc_ListChildPages_Teaser_TestModel extends Kwc_List_ChildPages_Teaser_Model
{
    public function __construct(array $config = array())
    {
        $config['proxyModel'] = new Kwf_Model_FnF(array(
            'columns' => array('id', 'child_id', 'component_id', 'target_page_id', 'visible'=>true),
            'data' => array(
            )
        ));
        parent::__construct($config);
    }
}
