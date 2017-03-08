<?php
class Kwc_Editable_ComponentsController_EditComponentsData extends Kwf_Data_Abstract
{
    public function load($row, array $info = array())
    {
        $admin = Kwc_Admin::getInstance($row->content_component_class);
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

class Kwc_Editable_ComponentsController extends Kwf_Controller_Action_Auto_Grid
{
    protected $_buttons = array();
    protected $_modelName = 'Kwc_Editable_ComponentsModel';

    protected function _initColumns()
    {
        parent::_initColumns();
        $this->_columns->add(new Kwf_Grid_Column('name', trlKwf('Name'), 290));
        $this->_columns->add(new Kwf_Grid_Column('edit_components'))
            ->setData(new Kwc_Editable_ComponentsController_EditComponentsData());
    }

    public function indexAction()
    {
        $admin = Kwc_Admin::getInstance($this->_getParam('class'));
        $this->view->componentsControllerUrl = $admin->getControllerUrl('Components');
        $this->view->xtype = 'kwc.editable';

        $componentConfigs = array();
        $m = Kwf_Model_Abstract::getInstance('Kwc_Editable_ComponentsModel');
        foreach ($m->getRows() as $row) {
            $cls = $row->content_component_class;
            $admin = Kwc_Admin::getInstance($cls);
            foreach ($admin->getExtConfig() as $k=>$cfg) {
                if (!isset($componentConfigs[$cls.'-'.$k])) {
                    $componentConfigs[$cls.'-'.$k] = $cfg;
                }
            }
        }
        $this->view->componentConfigs = $componentConfigs;
    }
}
