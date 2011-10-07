<?php
class Vpc_Directories_Category_Directory_CategoriesController
    extends Vps_Controller_Action_Auto_Grid
{
    protected $_buttons = array('save', 'add', 'delete');
    protected $_position = 'pos';

    protected function _isAllowedComponent()
    {
        if ($this->_getParam('componentId')) {
            $class = $this->_getParam('class');
            $c = Vps_Component_Data_Root::getInstance()
                ->getComponentByDbId($this->_getParam('componentId'),
                    array('ignoreVisible'=>true, 'limit'=>1));
            $c = $c->getChildComponent(array('componentClass'=>$class));
            $allowed = Vps_Registry::get('acl')
                ->isAllowedComponentById($c->componentId, $class, $this->_getAuthData());
        } else {
            $allowed = false;
            $class = $this->_getParam('class');
            foreach (Vps_Registry::get('acl')->getAllResources() as $r) {
                if ($r instanceof Vps_Acl_Resource_ComponentClass_Interface) {
                    if ($class == $r->getComponentClass()) {
                        $allowed = Vps_Registry::get('acl')->getComponentAcl()
                            ->isAllowed($this->_getAuthData(), $this->_getParam('class'));
                        break;
                    }
                }
            }
        }
        return $allowed;
    }

    private function _getCategoryDirectory()
    {
        $c = Vps_Component_Data_Root::getInstance()
            ->getComponentByDbId($this->_getParam('componentId'), array('ignoreVisible'=>true));
        return $c->getChildComponent(array('componentClass'=>$this->_getParam('class'), 'ignoreVisible'=>true));
    }

    protected function _getSelect()
    {
        $ret = parent::_getSelect();
        $ret->whereEquals('component_id', $this->_getCategoryDirectory()->dbId);
        return $ret;
    }

    protected function _initColumns()
    {
        $this->setModel(Vpc_Abstract::createChildModel($this->_getParam('class')));
        $this->_columns->add(new Vps_Grid_Column('name', trlVps('Category'), 300))
            ->setEditor(new Vps_Form_Field_TextField());
        $this->_columns->add(new Vps_Grid_Column_Visible());
    }

    protected function _beforeInsert(Vps_Model_Row_Interface $row, $submitRow)
    {
        parent::_beforeInsert($row, $submitRow);
        $row->component_id = $this->_getCategoryDirectory()->dbId;
    }
}
