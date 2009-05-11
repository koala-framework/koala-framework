<?php
class Vpc_Shop_Products_Directory_Controller extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('add', 'delete', 'save');
    protected $_editDialog = array(
        'controllerUrl' => '/admin/component/edit/Vpc_Shop_Products_Directory_Component!Form',
        'width' => 600,
        'height' => 500
    );
    protected $_modelName = 'Vpc_Shop_Products';
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('title', trlVps('Title'), 200));
        $this->_columns->add(new Vps_Grid_Column('price', trlVps('Price'), 100))
            ->setRenderer('euroMoney');
        $this->_columns->add(new Vps_Grid_Column_Visible());
        $this->_columns->add(new Vps_Grid_Column_Button('properties', ' ', 20))
            ->setButtonIcon('/assets/silkicons/newspaper.png')
            ->setToolTip('Properties');
        $this->_columns->add(new Vps_Grid_Column_Button('edit', ' ', 20))
            ->setButtonIcon('/assets/silkicons/newspaper_go.png')
            ->setToolTip('Edit Product');
    }
    public function jsonIndexAction()
    {
        $this->view->vpc(Vpc_Admin::getInstance($this->_getParam('class'))->getExtConfig());
    }

    public function indexAction()
    {
        $c = array(
            'xtype' => 'vps.component',
            'mainComponentClass' => $this->_getParam('class')
        );
        $this->view->vpc($c);
    }
}
