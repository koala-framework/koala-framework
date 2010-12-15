<?php
///TODO : ist im moment vom normalen paragraph alles kopiert!!!
class Vpc_Paragraphs_Controller_Trl_EditComponentsData extends Vps_Data_Abstract
{
    private $_componentClass;
    private $_componentConfigs = array();

    public function __construct($componentClass)
    {
        $this->_componentClass = $componentClass;
    }

    public function load($row)
    {
        $admin = Vpc_Admin::getInstance($row->component_class);
        $ret = array();
        foreach ($admin->getExtConfig() as $k=>$cfg) {
            $cls = $row->component_class;
            if (!isset($this->_componentConfigs[$cls.'-'.$k])) {
                $this->_componentConfigs[$cls.'-'.$k] = $cfg;
            }
            $ret[] = array(
                'componentClass' => $cls,
                'type' => $k
            );
        }
        return $ret;
    }

    public function getComponentConfigs()
    {
        return $this->_componentConfigs;
    }
}

class Vpc_Paragraphs_Trl_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_permissions = array(
        'save',
        );
    protected $_model = 'Vpc_Paragraphs_Trl_AdminModel';
    protected $_sortable = false;
    protected $_defaultOrder = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('component_class'));
        $this->_columns->add(new Vps_Grid_Column('component_name'));
        $this->_columns->add(new Vps_Grid_Column('component_icon'));
        $this->_columns->add(new Vps_Grid_Column('pos'));

        $this->_columns->add(new Vps_Grid_Column('preview'))
            ->setData(new Vps_Data_Vpc_Frontend($this->_getParam('class')))
            ->setRenderer('component');
        $this->_columns->add(new Vps_Grid_Column_Visible());
        $this->_columns->add(new Vps_Grid_Column('edit_components'))
            ->setData(new Vpc_Paragraphs_Controller_Trl_EditComponentsData($this->_getParam('class')));
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
        $c = Vps_Component_Data_Root::getInstance()->getComponentByDbId($id, array('ignoreVisible'=>true));
        Vpc_Admin::getInstance($c->componentClass)->makeVisible($c);
    }
}
