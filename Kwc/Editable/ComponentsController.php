<?php
class Vpc_Editable_ComponentsController_EditComponentsData extends Vps_Data_Abstract
{
    public function load($row)
    {
        $admin = Vpc_Admin::getInstance($row->content_component_class);
        $ret = array();
        foreach ($admin->getExtConfig() as $k=>$cfg) {
            $ret[] = array(
                'componentClass' => $row->content_component_class,
                'type' => $k
            );
        }
        return $ret;
    }
}

class Vpc_Editable_ComponentsController extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_modelName = 'Vpc_Editable_ComponentsModel';

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Vps_Grid_Column('name', trlVps('Name'), 190));
        $this->_columns->add(new Vps_Grid_Column('edit_components'))
            ->setData(new Vpc_Editable_ComponentsController_EditComponentsData());
    }

    public function indexAction()
    {
        $admin = Vpc_Admin::getInstance($this->_getParam('class'));
        $this->view->componentsControllerUrl = $admin->getControllerUrl('Components');
        $this->view->xtype = 'vpc.editable';

        $componentConfigs = array();
        $m = Vps_Model_Abstract::getInstance('Vpc_Editable_ComponentsModel');
        foreach ($m->getRows() as $row) {
            $cls = $row->content_component_class;
            $admin = Vpc_Admin::getInstance($cls);
            foreach ($admin->getExtConfig() as $k=>$cfg) {
                if (!isset($componentConfigs[$cls.'-'.$k])) {
                    $componentConfigs[$cls.'-'.$k] = $cfg;
                }
            }
        }
        $this->view->componentConfigs = $componentConfigs;
    }
}
