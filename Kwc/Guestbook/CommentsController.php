<?php
class Kwc_Guestbook_CommentsController extends Kwc_Directories_Item_Directory_Controller
{
    protected $_defaultOrder = array('field' => 'create_time', 'direction' => 'DESC');
    protected $_filters = array('text' => true);
    protected $_paging = 25;
    protected $_editDialog = array(
        'width' =>  500,
        'height' =>  400
    );
    protected $_buttons = array('save', 'delete');

    public function preDispatch()
    {
        if (!isset($this->_model) && !isset($this->_tableName)) {
            $this->setModel(Kwc_Abstract::createChildModel($this->_getParam('class')));
        }
        parent::preDispatch();
        $url = Kwc_Admin::getInstance($this->_getParam('class'))->getControllerUrl('Comment');
        $this->_editDialog['controllerUrl'] = $url;
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column_Checkbox('visible', trlKwf('Visible'), 40))
            ->setEditor(new Kwf_Form_Field_Checkbox());
        $this->_columns->add(new Kwf_Grid_Column_Date('create_time', trlKwf('Create Date')));
        $this->_columns->add(new Kwf_Grid_Column('content', trlKwf('Content'), 350));
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Name'), 130));
        $this->_columns->add(new Kwf_Grid_Column('email', trlKwf('E-Mail'), 150));
        parent::_initColumns();
    }
}
