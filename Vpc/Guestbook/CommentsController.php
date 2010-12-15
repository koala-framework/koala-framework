<?php
class Vpc_Guestbook_CommentsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_defaultOrder = array('field' => 'id', 'direction' => 'DESC');
    protected $_filters = array('text' => true);
    protected $_paging = 25;
    protected $_editDialog = array(
        'width' =>  500,
        'height' =>  400
    );

    public function preDispatch()
    {
        if (!isset($this->_model) && !isset($this->_tableName)) {
            $this->setModel(Vpc_Abstract::createChildModel($this->_getParam('class')));
        }
        parent::preDispatch();
        $url = Vpc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Comment');
        $this->_editDialog['controllerUrl'] = $url;
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column_Checkbox('visible', trlVps('Visible'), 40))
            ->setEditor(new Vps_Form_Field_Checkbox());
        $this->_columns->add(new Vps_Grid_Column_Date('create_time', trlVps('Create Date')));
        $this->_columns->add(new Vps_Grid_Column('content', trlVps('Content'), 350));
        $this->_columns->add(new Vps_Grid_Column('name', trlVps('Name'), 130));
        $this->_columns->add(new Vps_Grid_Column('email', trlVps('E-Mail'), 150));
        $this->_columns->add(new Vps_Grid_Column_Button('editdialog'));
    }
}
