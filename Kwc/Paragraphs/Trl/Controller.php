<?php
class Kwc_Paragraphs_Trl_Controller extends Kwf_Controller_Action_Auto_Kwc_Grid
{
    protected $_permissions = array(
        'save',
        );
    protected $_model = 'Kwc_Paragraphs_Trl_AdminModel';
    protected $_sortable = false;
    protected $_defaultOrder = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('component_class'));
        $this->_columns->add(new Kwf_Grid_Column('component_name'));
        $this->_columns->add(new Kwf_Grid_Column('component_icon'));
        $this->_columns->add(new Kwf_Grid_Column('pos'));

        $this->_columns->add(new Kwf_Grid_Column('preview'))
            ->setData(new Kwf_Data_Kwc_Frontend($this->_getParam('class')))
            ->setRenderer('component');
        $this->_columns->add(new Kwf_Grid_Column_Visible());
        $this->_columns->add(new Kwf_Grid_Column('edit_components'))
            ->setData(new Kwc_Paragraphs_Trl_EditComponentsData($this->_getParam('class')));
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_getModel()->setComponentId($this->_getParam('componentId'));
    }

    public function jsonDataAction()
    {
        parent::jsonDataAction();
        $this->view->componentConfigs = $this->_columns['edit_components']
                                ->getData()->getComponentConfigs();
        $c = Kwf_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('limit'=>1, 'ignoreVisible'=>true));
        $this->view->contentWidth = $c->getComponent()->getContentWidth();
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if ($this->_getParam('filter_visible')) {
            $ret->whereEquals('visible', $this->_getParam('filter_visible'));
        }
        return $ret;
    }

    public function jsonMakeAllVisibleAction()
    {
        $id = $this->_getParam('componentId');
        $c = Kwf_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true));
        Kwc_Admin::getInstance($c->componentClass)->makeVisible($c);
    }

    public function openPreviewAction()
    {
        $page = Kwf_Component_Data_Root::getInstance()->getComponentByDbId(
            $this->_getParam('componentId'),
            array('ignoreVisible'=>true, 'limit' => 1)
        );
        if (!$page) {
            throw new Kwf_Exception_Client(trlKwf('Page not found'));
        }
        header('Location: '.$page->getPreviewUrl());
        exit;
    }
}
