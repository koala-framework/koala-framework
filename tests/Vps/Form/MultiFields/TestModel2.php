<?php
class Vps_Form_MultiFields_TestModel2 extends Vps_Model_FnF
{
    protected $_referenceMap = array(
        'Model1' => array(
            'refModelClass' => 'Vps_Form_MultiFields_TestModel1',
            'column' => 'model1_id'
        )
    );
}
