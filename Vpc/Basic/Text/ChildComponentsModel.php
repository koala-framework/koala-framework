<?php
class Vpc_Basic_Text_ChildComponentsModel extends Vps_Model_Db_Proxy
{
    protected $_table = 'vpc_basic_text_components';

    protected $_referenceMap = array(
        'Component' => array(
            'refModelClass' => 'Vpc_Basic_Text_Model',
            'column' => 'component_id'
        )
    );
}
