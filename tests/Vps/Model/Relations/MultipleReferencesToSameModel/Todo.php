<?php
class Vps_Model_Relations_MultipleReferencesToSameModel_Todo extends Vps_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'name'=>'foo', 'creator_user_id'=>100, 'assignee_user_id'=>101)
    );
    protected $_referenceMap = array(
        'Creator' => array(
            'column' => 'creator_user_id',
            'refModelClass' => 'Vps_Model_Relations_MultipleReferencesToSameModel_User',
        ),
        'Assignee' => 'assignee_user_id->Vps_Model_Relations_MultipleReferencesToSameModel_User'
    );
}
