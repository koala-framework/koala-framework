<?php
class Vpc_Events_PagesModel extends Vpc_Root_Category_GeneratorModel
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
