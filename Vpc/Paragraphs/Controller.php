<?php
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
            $data = $this->_model->getRow($id)->toArray();
            $this->view->data = $data;
            $this->view->data['component_name'] = $name;

            $this->view->hasController = !is_null(
                Vpc_Admin::getComponentFile($row->component, 'Controller')
            );
        } else {
            throw new Vps_Exception("Component $class not found");
        }
    }
    protected function _preforeAddParagraph($row)
    {
        $row->component_id = $this->_getParam('componentId');
    }
}
