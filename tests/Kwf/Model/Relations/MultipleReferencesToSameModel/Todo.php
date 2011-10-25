<?php
class Kwf_Model_Relations_MultipleReferencesToSameModel_Todo extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'name'=>'foo', 'creator_user_id'=>100, 'assignee_user_id'=>101)
    );
    protected $_referenceMap = array(
        'Creator' => array(
            'column' => 'creator_user_id',
            'refModelClass' => 'Kwf_Model_Relations_MultipleReferencesToSameModel_User',
        ),
        'Assignee' => 'assignee_user_id->Kwf_Model_Relations_MultipleReferencesToSameModel_User'
    );
}
