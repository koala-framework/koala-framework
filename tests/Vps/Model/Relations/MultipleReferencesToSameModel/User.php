<?php
class Vps_Model_Relations_MultipleReferencesToSameModel_User extends Vps_Model_FnF
{
    protected $_data = array(
        array('id'=>100, 'name'=>'sepp'),
        array('id'=>101, 'name'=>'hias'),
    );
    protected $_dependentModels = array(
        //getChildRows('Todo', 'Creator');
        //getChildRows('Todo:Creator');
        //getChildRows('Todo->Creator');
        'Todo' => 'Vps_Model_Relations_MultipleReferencesToSameModel_Todo',

        //getChildRows('TodoCreator');
        'TodoCreator' => 'Vps_Model_Relations_MultipleReferencesToSameModel_Todo:Creator',
        'TodoAssignee' => 'Vps_Model_Relations_MultipleReferencesToSameModel_Todo:Assignee',

        'TodoCreator' => 'Vps_Model_Relations_MultipleReferencesToSameModel_Todo->Creator',
        'TodoAssignee' => 'Vps_Model_Relations_MultipleReferencesToSameModel_Todo->Assignee',

        'Creator' => 'Vps_Model_Relations_MultipleReferencesToSameModel_Todo',
        'Assignee' => 'Vps_Model_Relations_MultipleReferencesToSameModel_Todo',
    );
}
