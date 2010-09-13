<?php
class Vps_Model_Relations_MultipleReferencesToSameModel_User extends Vps_Model_FnF
{
    protected $_data = array(
        array('id'=>100, 'name'=>'sepp'),
        array('id'=>101, 'name'=>'hias'),
    );
    protected $_dependentModels = array(
        'TodoCreator' => array(
            'model' => 'Vps_Model_Relations_MultipleReferencesToSameModel_Todo',
            'rule' => 'Creator'
        ),
        'TodoAssignee' => array(
            'model' => 'Vps_Model_Relations_MultipleReferencesToSameModel_Todo',
            'rule' => 'Assignee'
        ),
    );
}
