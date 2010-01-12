<?php
class Vpc_Events_PagesModel extends Vps_Component_PagesModel
{
    public function __construct()
    {
        $config['proxyModel'] = new Vps_Model_FnF(array(
            'data' => array(
                array('id'=>3100, 'pos'=>1, 'visible'=>true, 'name'=>'EventsBar', 'filename' => 'events1',
                    'parent_id'=>'root', 'component'=>'events', 'is_home'=>false, 'hide'=>false),
            )
        ));
        parent::__construct($config);
    }
}
