<?php
class Kwc_Articles_Directory_TagsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_model = 'Kwc_Articles_Directory_TagsModel';
    protected $_buttons = array('save', 'add', 'delete');
    protected $_paging = 25;
    protected $_filters = array('text'=>true);
    protected $_position = 'pos';

    public function indexAction()
    {
        parent::indexAction();
        $this->view->baseParams = array(
            'type' => $this->_getParam('type')
        );
    }

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Name'), 200))
            ->setEditor(new Kwf_Form_Field_TextField());
        if ($this->_getParam('type') == 'tag') $this->_position = false;
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('type', $this->_getParam('type'));
        return $ret;
    }

    protected function _beforeInsert($row, $submitRow)
    {
        parent::_beforeInsert($row, $submitRow);
        $row->type = $this->_getParam('type');
    }
}
