<?php
class Vpc_Forum_Directory_Controller extends Vps_Controller_Action_Auto_Vpc_Tree
{
    protected $_buttons = array(
        'add' => true, 'edit' => true, 'delete' => true,
        'invisible' => true, 'reload' => true, 'moderators' => true
    );
    protected $_rootVisible = true;
    protected $_textField = 'name';

    public function init()
    {
        $this->_editDialog = array(
            'controllerUrl'=> Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Form'),
            'width'=>600,
            'height'=>300
        );
        parent::init();
    }

    public function jsonDeleteAction()
    {
        $id = $this->getRequest()->getParam('id');
        $row = $this->_table->find($id)->current();

        $threadTable = new Vpc_Forum_Group_Model();
        $threadRow = $threadTable->fetchRow(
            array('component_id = ?' => $row->component_id.'_'.$row->id)
        );

        if ($threadRow) {
            $this->view->error = 'Gruppe kann nicht gelöscht werden - es sind noch Themen vorhanden.';
        } else {
            parent::jsonDeleteAction();
        }
    }
}
