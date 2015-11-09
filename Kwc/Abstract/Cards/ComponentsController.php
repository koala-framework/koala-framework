<?php
class Kwc_Abstract_Cards_ComponentsController extends Kwf_Controller_Action_Auto_Grid
{
    protected function _initColumns()
    {
        $this->_columns->add(new Kwf_Grid_Column('id'));
        $this->_columns->add(new Kwf_Grid_Column('name'));

        if ($this->_getParam('id')) {
            $subRootComponentId = $this->_getParam('id');
        } else if ($this->_getParam('parent_id')) {
            $subRootComponentId = $this->_getParam('parent_id');
        } else if ($this->_getParam('componentId')) {
            $subRootComponentId = $this->_getParam('componentId');
        } else {
            throw new Kwf_Exception("componentId, id or parent_id required");
        }

        $data = array();
        $gen = Kwc_Abstract::getSetting($this->_getParam('class'), 'generators');
        foreach ($gen['child']['component'] as $name => $class) {
            if (!$class) continue;
            $admin = Kwc_Admin::getInstance($class);
            $forms = $admin->getCardForms();
            foreach ($admin->getVisibleCardForms($subRootComponentId) as $k) {
                $id = count($forms)==1 ? $name : $name.'_'.$k;
                $data[] = array(
                    'id' => $id,
                    'name' => $forms[$k]['title']
                );
            }
        }
        $this->_model = new Kwf_Model_FnF(array(
            'data' => $data
        ));
        parent::_initColumns();
    }

    protected function _getSelect()
    {
        if (preg_match('#^id:(.*)$#', $this->_getParam('query'), $m)) {
            $s = $this->_model->select();
            $s->whereEquals('id', $m[1]);
            return $s;
        }
        return parent::_getSelect();
    }

    protected function _getOrder($order)
    {
        return null;
    }

    protected function _isAllowedComponent()
    {
        return true;
    }
}
