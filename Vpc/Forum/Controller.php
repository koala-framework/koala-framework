<?php
class Vpc_Forum_Controller extends Vps_Controller_Action_Auto_Vpc_Tree
{
    protected $_buttons = array(
        'add' => true, 'edit' => true, 'delete' => true,
        'invisible' => true, 'reload' => true, 'moderators' => true
    );
    protected $_rootVisible = true;
    protected $_textField = 'name';
    protected $_editDialog = array('controllerUrl'=>'/admin/component/edit/Vpc_Forum_Form',
                                   'width'=>450,
                                   'height'=>200);
}
