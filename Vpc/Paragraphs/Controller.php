<?php
class Vpc_Paragraphs_Controller_EditComponentsData extends Vps_Data_Abstract
{
    private $_componentClass;
    private $_componentConfigs = array();

    public function __construct($componentClass)
    {
        $this->_componentClass = $componentClass;
    }

    public function load($row)
    {
        $generators = Vpc_Abstract::getSetting($this->_componentClass, 'generators');
        $classes = $generators['paragraphs']['component']; 
        $admin = Vpc_Admin::getInstance($classes[$row->component]);
        $ret = array();
        foreach ($admin->getExtConfig() as $k=>$cfg) {
            $cls = $classes[$row->component];
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

class Vpc_Paragraphs_Controller extends Vps_Controller_Action_Auto_Vpc_Grid
{
    protected $_buttons = array(
        'save',
        'delete',
        'reload',
        'addparagraph'
        );
    protected $_paging = 0;
    protected $_position = 'pos';

    protected function _initColumns()
    {
        $this->_columns->add(new Vps_Grid_Column('pos'));
        $this->_columns->add(new Vps_Grid_Column('component_class'))
            ->setData(new Vps_Data_Vpc_ComponentClass($this->_getParam('class')));
        $this->_columns->add(new Vps_Grid_Column('component_name'))
            ->setData(new Vps_Data_Vpc_ComponentName($this->_getParam('class')));
        $this->_columns->add(new Vps_Grid_Column('component_icon'))
            ->setData(new Vps_Data_Vpc_ComponentIcon($this->_getParam('class')));

        $this->_columns->add(new Vps_Grid_Column('preview'))
            ->setData(new Vps_Data_Vpc_Frontend($this->_getParam('class')))
            ->setRenderer('component');
        $this->_columns->add(new Vps_Grid_Column_Visible());
        $this->_columns->add(new Vps_Grid_Column('edit_components'))
            ->setData(new Vpc_Paragraphs_Controller_EditComponentsData($this->_getParam('class')));
    }

    public function jsonDataAction()
    {
        parent::jsonDataAction();
        $this->view->componentConfigs = $this->_columns['edit_components']
                                ->getData()->getComponentConfigs();
    }

    public function preDispatch()
    {
        parent::preDispatch();
        $this->_components = array();
        foreach (Vpc_Abstract::getChildComponentClasses($this->_getParam('class'), 'paragraphs') as $c) {
            if (Vpc_Abstract::hasSetting($c, 'componentName')) {
                $name = Vpc_Abstract::getSetting($c, 'componentName');
                if ($name) $this->_components[$name] = $c;
            }
        }
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        if ($this->_getParam('filter_visible')) {
            $ret->whereEquals('visible', $this->_getParam('filter_visible'));
        }
        return $ret;
    }

    public function jsonAddParagraphAction()
    {
        $class = $this->_getParam('component');
        if (array_search($class, $this->_components)) {
            $admin = Vpc_Admin::getInstance($class);
            if ($admin) $admin->setup();
            $row = $this->_model->createRow();
            $this->_preforeAddParagraph($row);
            $generators = Vpc_Abstract::getSetting($this->_getParam('class'), 'generators');
            $classes =$generators['paragraphs']['component'];
            $row->component = array_search($class, $classes);
            $row->visible = 0;
            $row->pos = $this->_getParam('pos');
            $row->save();
            $id = $row->id;
            $where['component_id = ?'] = $this->_getParam('componentId');

            // Hack für weiterleiten auf Edit-Seite
            $name = Vpc_Abstract::getSetting($this->_getParam('class'), 'componentName');
            $name = str_replace('.', ' -> ', $name);
            $this->view->id = $row->id;
            //wird des braucht? $this->view->componentClass = $classes[$row->component];
            //wird des braucht? $this->view->componentName = $name;

            $this->view->componentConfigs = array();
            $this->view->editComponents = array();
            $cfg = Vpc_Admin::getInstance($classes[$row->component])->getExtConfig();
            foreach ($cfg as $k=>$i) {
                $this->view->componentConfigs[$classes[$row->component].'-'.$k] = $i;
                $this->view->editComponents[] = array(
                    'componentClass' => $classes[$row->component],
                    'type' => $k
                );
            }
        } else {
            throw new Vps_Exception("Component $class not found");
        }
    }
    protected function _preforeAddParagraph($row)
    {
        $row->component_id = $this->_getParam('componentId');
    }
}
